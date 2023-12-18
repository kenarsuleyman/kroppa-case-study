<?php

namespace App\Http\Controllers;

use App\Http\Requests\EndGameRequest;
use App\Http\Requests\StartGameRequest;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function start(StartGameRequest $request)
    {
        $validated = $request->validated();

        $user = User::firstOrCreate(
            [ 'email' =>  $validated['email'] ],
            [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'password' => Str::password()
            ]
        );

        $ongoingGame = $user->games()->where('is_completed', false)->first();
        if ($ongoingGame) {
            return response()->json([
                'message' => 'You already have an ongoing game',
                'game_data' => $ongoingGame
            ], 403);
        }

        $game = $user->games()->create();

        return response()->json([
            'message' => 'Game created successfully',
            'game_data' => $game,
            'user_data' => $user
        ], 201);
    }

    public function end(EndGameRequest $request)
    {
        $validated = $request->validated();
        $user = User::where('id', $validated['user_id'])->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 401);
        }

        //EndGameRequest class already validated that the game_id is valid and belongs to the user
        $game = $user->games()->find($validated['game_id']);

        if ($game->is_completed) {
            return response()->json([
                'message' => 'This game is already completed',
                'game_data' => $game
            ], 403);
        }

        $game->is_completed = true;
        $game->score = $validated['score'];
        $game->completed_at = now();
        $game->save();

        return response()->json([
            'message' => 'Game ended successfully',
            'best_score' => $user->bestScore(),
            'ranking_position' => $user->rankingPosition(),
            'game_data' => $game,
            'user_data' => $user
        ], 200);
    }

}
