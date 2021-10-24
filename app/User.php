<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\SocialAccount;
use Laravel\Cashier\Billable;
use App\Notifications\PasswordReset;


use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens,Billable,HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','avatar', 'premuim','manual_premuim','pack_name','pack_id','start_at','expired_in','role'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    protected $casts = [
        'premuim' => 'int'
    
    ];

    public function socialAccounts(){
        return $this->hasMany(SocialAccount::class);
    }


    protected $dates = [
        'trial_ends_at', 'subscription_ends_at',
    ];



    public function sendPasswordResetNotification($token)
{
    $this->notify(new PasswordReset($token));
}


}
