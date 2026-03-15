<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\LeaderboardService;

/**
 * LeaderboardController displays the leaderboard of participants based on
 * total scores and steps.
 */
class LeaderboardController extends Controller
{
    public function index(): void
    {
        $service = new LeaderboardService($this->config);
        $ranking = $service->getLeaderboard();
        $this->render('leaderboard/index', [
            'ranking' => $ranking,
        ]);
    }
}