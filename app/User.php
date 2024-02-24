<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'first_name', 'last_name', 'city', 'country'
    ];

    

    public static function updateUser(int $userId, array $userData)
    {
        self::where('id', $userId)
            ->update([
                'first_name' => addslashes($userData['first_name']),
                'last_name' => addslashes($userData['last_name']),
                'city' => addslashes($userData['city']),
                'country' => addslashes($userData['country'])
            ]);
    }


    public static function addUser(array $userData)
    {
        self::create([
            'first_name' =>  addslashes($userData['first_name']),
            'last_name' =>  addslashes($userData['last_name']),
            'city' =>  addslashes($userData['city']),
            'country' =>  addslashes($userData['country'])
        ]);

        // $model = new self;

        // $model->first_name = addslashes($userData['first_name']);
        // $model->last_name = addslashes($userData['last_name']);
        // $model->city = addslashes($userData['city']);
        // $model->country = addslashes($userData['country']);

        // $model->save();
    }
}
