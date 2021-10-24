<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;
use App\User;
use App\Plan;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class LoginController extends Controller
{

    use IssueTokenTrait;

    private $client;

    public function __construct()
    {
        $this->client = Client::find(1);
    }

    public function login(Request $request)
    {

        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        return $this->issueToken($request, 'password');

    }


    public function refresh(Request $request)
    {
        $this->validate($request, [
            'refresh_token' => 'required'
        ]);

        return $this->issueToken($request, 'refresh_token');


    }

    public function update(Request $request,Plan $plan)
    {

       
        $accessToken = Auth::user()->token();


        DB::table('users')
            ->where('id', $accessToken->user_id)
            ->update(

                array( 
                    "premuim" => true,
                    "pack_name" => request('pack_name'),
                    "expired_in" => Carbon::now()->addDays(request('pack_duration'))
    
   )

            );
            


        return response()->json([], 204);

    }



    public function updatePaypal(Request $request,Plan $plan)
    {

       
        $accessToken = Auth::user()->token();


        DB::table('users')
            ->where('id', $accessToken->user_id)
            ->update(

                array( 
                    "premuim" => true,
                    "transaction_id" => request('transaction_id'),
                    "pack_id" => request('pack_id'),
                    "pack_name" => request('pack_name'),
                    "expired_in" => Carbon::now()->addDays(request('pack_duration'))
    
   )

            );
            


        return response()->json([], 204);

    }




    public function addPlanToUser(Request $request)
    {

        $stripeToken = $request->get('stripe_token');
        $user = Auth::user();
        $user->newSubscription($request->get('stripe_plan_id'), $request->get('stripe_plan_price'))->create($stripeToken);

        $accessToken = Auth::user()->token();

        DB::table('users')
        ->where('id', $accessToken->user_id)
        ->update(

            array( 
                "premuim" => true,
                "pack_name" => request('pack_name'),
                "pack_id" => request('stripe_plan_id'),
                "start_at" => request('start_at'),
                "expired_in" => Carbon::now()->addDays(request('pack_duration')))

        );

        return response()->json($user, 204);

    }



    public function cancelSubscription(Request $request)
    {

 
       $user = Auth::user();

        $accessToken = Auth::user()->token();

        $packId = Auth::user()->pack_id;
        
        $user->subscription($packId)->cancelNow();


        DB::table('users')
        ->where('id', $accessToken->user_id)
        ->update(

            array( 
                "premuim" => false,
                "pack_name" => "",
                "start_at" => "",
                "expired_in" => Carbon::now())

        );

         return response()->json($user, 204);

    }


    public function cancelSubscriptionPaypal(Request $request)
    {

 
       $user = Auth::user();

        $accessToken = Auth::user()->token();

        DB::table('users')
        ->where('id', $accessToken->user_id)
        ->update(

            array( 
                "premuim" => false,
                "pack_name" => "",
                "start_at" => "",
                "expired_in" => Carbon::now())

        );

         return response()->json($user, 204);

    }



    public function profile(Request $request)
    {

        $user = User::find(1);
        $user->subscribedTo("1");

        return response()->json($user, 204);

    }



    public function update_avatar(Request $request){

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();

        $avatarName = $user->id.'_avatar'.time().'.'.request()->avatar->getClientOriginalExtension();

        $request->avatar->storeAs('avatars',$avatarName);

        $user->avatar = $avatarName;
        $user->save();

        return response()->json([], 204);

    }


    public function user (Request $request){


    
        return $request->user();
     }


    public function logout(Request $request)
    {

        $accessToken = Auth::user()->token();

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update(['revoked' => true]);

        $accessToken->revoke();

        return response()->json([], 204);

    }
}
