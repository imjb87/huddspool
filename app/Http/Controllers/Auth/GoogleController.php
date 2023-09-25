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
        try {
        
            $user = Socialite::driver('google')->user();
         
            $finduser = User::where('email', $user->email)->first();
         
            if($finduser){
         
                Auth::login($finduser);
       
                return redirect()->intended('/');
         
            } else {

                return redirect()->route('login')->with('error', 'User not found with the email address associated with this Google account.');

            }
       
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}