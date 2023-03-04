<?php

namespace App\Http\Controllers;

use App\Exceptions\FEException;
use Illuminate\Http\Request;
use App\User;

class VerificationController extends Controller
{
    /**
     * Verify if the user email address is verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $user_id
     * @return \Illuminate\Http\Response
     */
    public function verify($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            new FEException(__('Invalid/Expired url provided.'), '', 401);
        }
        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return redirect()->to('/home');
    }
    /**
     * Resend the verification email to ther user.
     *
     * @return \Illuminate\Http\Response
     */
    public function resend()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            new FEException(__('Email already verified.'), '', 401);
        }
        auth()->user()->sendEmailVerificationNotification();
        new FEException(__('Email verification link sent on your Email.'), '', 401);
    }
}
