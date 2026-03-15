<?php

namespace App\Services;

use Exception;

/**
 * Service for extracting a step count from a screenshot using the
 * Google Cloud Vision API. This service requires a valid API key
 * to be set in an environment variable or passed via the config
 * array. The API is called via its REST interface with the
 * `TEXT_DETECTION` feature enabled.
 */
class StepDetectionService
{
    /**
     * @var string|null Google API key for Vision API
     */
    private ?string $apiKey;

    /**
     * Construct the service and capture the API key from the
     * environment or config. The environment variable
     * `GOOGLE_VISION_API_KEY` takes precedence; otherwise
     * the key may be supplied in the config under
     * `vision_api_key`.
     *
     * @param array $config Application configuration
     */
    public function __construct(array $config = [])
    {
        $this->apiKey = getenv('GOOGLE_VISION_API_KEY') ?: ($config['vision_api_key'] ?? null);
    }

    /**
     * Attempt to extract a step count from an image file. The
     * screenshot is sent to the Google Vision API for text
     * detection. The full recognised text is then searched for
     * numeric patterns preceding the word "step" or "steps". If
     * such a pattern is found it is returned as an integer. If no
     * clear match is found the method falls back to returning null.
     *
     * @param string $filePath Absolute path to the image file
     * @return int|null The extracted step count, or null on failure
     */
    public function extractSteps(string $filePath): ?int
    {
        // Ensure we have a key and the file exists
        if (!$this->apiKey || !is_file($filePath)) {
            return null;
        }
        // Read and base64 encode the image
        $imageData = @file_get_contents($filePath);
        if ($imageData === false) {
            return null;
        }
        $base64Image = base64_encode($imageData);
        // Build request payload
        $requestBody = [
            'requests' => [[
                'image'    => ['content' => $base64Image],
                'features' => [['type' => 'TEXT_DETECTION']],
            ]],
        ];
        $jsonBody = json_encode($requestBody);
        if ($jsonBody === false) {
            return null;
        }
        // Prepare the HTTP request
        $url = 'https://vision.googleapis.com/v1/images:annotate?key=' . urlencode($this->apiKey);
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $jsonBody,
            CURLOPT_TIMEOUT        => 10,
        ]);
        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);
        if ($response === false || $error) {
            return null;
        }
        $data = json_decode($response, true);
        if (!is_array($data) || empty($data['responses'][0]['fullTextAnnotation']['text'])) {
            return null;
        }
        $text = $data['responses'][0]['fullTextAnnotation']['text'];
        if (!is_string($text) || $text === '') {
            return null;
        }
        // Normalise the text: remove punctuation and reduce whitespace
        $normalised = preg_replace('/[\r\n]+/', ' ', $text);
        if (!is_string($normalised)) {
            $normalised = $text;
        }
        // Look for numbers immediately followed by the word step/steps
        if (preg_match('/\b(\d{1,6})\s*steps?\b/i', $normalised, $match)) {
            return (int)$match[1];
        }
        // Fallback: find numbers anywhere and choose the most plausible
        if (preg_match_all('/\b\d{1,6}\b/', $normalised, $matches)) {
            $numbers = array_map('intval', $matches[0]);
            // Remove improbable counts (e.g. extremely large numbers)
            $numbers = array_filter($numbers, static function ($n) {
                return $n >= 0 && $n <= 200000;
            });
            if (!empty($numbers)) {
                // Prefer the largest plausible number (likely the total)
                return max($numbers);
            }
        }
        return null;
    }
}