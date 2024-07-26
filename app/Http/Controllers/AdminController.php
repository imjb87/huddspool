<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class AdminController extends Controller
{
    public function switchUser(User $user)
    {
        Session::put('previous_user', Auth()->user());

        Auth::login($user);

        return redirect('/')->with('success', 'Switched to user ' . $user->name);
    }

    public function switchBack()
    {        
        $previous_user = Session::get('previous_user');

        Auth::login($previous_user);

        Session::forget('previous_user');

        return redirect()->route('admin.dashboard')->with('success', 'Switched back to admin');
    }
}
