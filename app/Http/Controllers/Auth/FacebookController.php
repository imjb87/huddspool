<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
  
class FacebookController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }
           
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFacebookCallback()
    {
        $user = Socialite::driver('facebook')->user();
        
        $finduser = User::where('email', $user->email)->first();
        
        if($finduser){
        
            Auth::login($finduser);
    
            return route('team.show', $finduser->team_id);
        
        } else {

            return redirect()->route('login')->withErrors('A user does not exist with the email associated with this Facebook account.');

        }    
    }
}