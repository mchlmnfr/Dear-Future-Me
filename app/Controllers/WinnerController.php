<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Winner;

/**
 * WinnerController displays the winners of the wellness challenge.
 */
class WinnerController extends Controller
{
    /**
     * Show the winners page. Assumes a single challenge with ID 1.
     */
    public function index(): void
    {
        $winnerModel = new Winner($this->config);
        $winners = $winnerModel->getByChallenge(1);
        $this->render('leaderboard/winners', [
            'winners' => $winners,
        ]);
    }
}