<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class UsersSessions extends Model
{
    use Notifiable;

    protected $table = 'users_sessions';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'access_token'
    ];


    public static function replace(int $userId, string $replace)
    {
        $user = self::where('user_id', $userId)->first();

        DB::update("UPDATE users_sessions SET access_token = REPLACE(access_token, ?, ?) WHERE user_id = ?", [
            $user->access_token,
            $replace,
            $user->user_id
        ]);
    }

    public static function add(int $userId, string $token)
    {
        $model = new self;

        $model->user_id = $userId;
        $model->access_token = $token;

        $model->save();
    }
}
