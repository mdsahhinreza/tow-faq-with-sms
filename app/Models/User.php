<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Exception;
use Mail;
use App\Mail\SendCodeMail;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generateCode()
    {
        $user_id = User::where('id',auth()->user()->id)->first();
        // dd(auth()->user()->id);
        $code = rand(1000, 9999);
        // $code = 55555;
        // dd($code);
        UserCode::updateOrCreate(
            ['user_id' => $user_id->id],
            ['code' => $code]
        );




        try {
            $url = "https://login.esms.com.bd/api/v3/sms/send";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                "Accept: application/json",
                "Authorization: Bearer 48|OShMo2cBQhwZfQNRqqaDRU5sKcRna1NOZmhQfteY",
                "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $data = [
                'recipient' => '8801793832886',
                'sender_id' => '8809612443982',
                'message' => urldecode('Your Varification code is - '.$code),
            ];

            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            curl_close($curl);

            // dd($resp);

        } catch (\Exception $e) {
            info("Error: " . $e->getMessage());
            // dd($e);
        }

    }
}
