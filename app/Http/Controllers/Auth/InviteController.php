<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInviteRegistrationRequest;
use App\Models\User;
use App\Notifications\InviteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InviteController extends Controller
{
    public static function send(User $user)
    {
        $invitation_token = hash('sha256', $user->email.time());

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

        $registrationRequest = new StoreInviteRegistrationRequest;

        $data = Validator::make(
            $request->all(),
            $registrationRequest->rules(),
            $registrationRequest->messages(),
            $registrationRequest->attributes(),
        )->validate();

        $user->update([
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
            'invitation_token' => null,
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully! You can now log in.');
    }
}
