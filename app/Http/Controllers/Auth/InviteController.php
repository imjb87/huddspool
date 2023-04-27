<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\InviteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InviteController extends Controller
{
    public function send(User $user)
    {
        $invitation_token = hash('sha256', $user->email . time());
        
        $user->invitation_token = $invitation_token;
        $user->save();

        $user->notify(new InviteNotification($invitation_token));
    }

    public function register($token)
    {
        $user = User::where('invitation_token', $token)->firstOrFail();
        return view('auth.register', ['user' => $user]);
    }

    public function store(Request $request, $token)
    {
        $user = User::where('invitation_token', $token)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $user->update([
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'invitation_token' => null,
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully! You can now log in.');
    }
}
