<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
  
class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
           
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
        
        $finduser = User::where('email', $user->email)->first();
        
        if($finduser){
        
            Auth::login($finduser);
    
            return redirect()->route('team.show', $finduser->team_id);
        
        } else {

            return redirect()->route('login')->withErrors('A user does not exist with the email associated with this Google account.');

        }    
    }
}