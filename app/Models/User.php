<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class, 'user_id');
    }

    public function bestScore(): int
    {
        return $this->games()->whereDay('completed_at', now()->day)->max('score') ?? 0;
    }

    public function rankingPosition(): int
    {
        $currentDate = Carbon::now()->toDateString();

        $position = DB::table('games')
            ->join('users', 'games.user_id', '=', 'users.id')
            ->whereDate('games.created_at', $currentDate)
            ->where('games.is_completed', true)
            ->where('games.score', '>', $this->bestScore())
            ->groupBy('users.id')
            ->count();

        // Add 1 to get the actual position (1-based index)
        return $position + 1;
    }
}
