<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function show(Request $request)
    {
        // Get the current date
        $currentDate = Carbon::now()->toDateString();

        // Fetch the top 10 unique users with their highest score for the current day
        $leaderboard = Game::whereDate('created_at', $currentDate)
            ->where('is_completed', true) // Optional: Add any additional conditions
            ->groupBy('user_id')
            ->select('user_id', DB::raw('MAX(score) as max_score'))
            ->orderByDesc('max_score')
            ->take(10)
            ->get();

        // Load user details for the leaderboard
        $leaderboard = $leaderboard->load('user');

        return response()->json($leaderboard);
    }
}
