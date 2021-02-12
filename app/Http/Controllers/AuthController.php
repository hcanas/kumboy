<?php
namespace App\Http\Controllers;

use App\Events\GenericUserActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends DatabaseController
{
    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        try {
            $this->beginTransaction();

            $user = User::query()
                ->where('email', $request->get('email'))
                ->first();

            if ($user === null OR Hash::check($request->get('password'), $user->password) === false) {
                return back()
                    ->with('message_type', 'danger')
                    ->with('message_content', 'Email or password is incorrect.');
            } elseif ($user->banned_until !== null) {
                return back()
                    ->with('message_type', 'danger')
                    ->with('message_content', 'Your account is currently banned until '.$user->banned_until.'.');
            }

            Auth::login($user);
            event(new GenericUserActivity('Logged in.'));

            $this->commit();

            return redirect()->route('home');
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }

    public function logout()
    {
        try {
            $this->beginTransaction();

            event(new GenericUserActivity('Logged out.'));
            Auth::logout();

            $this->commit();

            return redirect()->route('home');
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Unable to logout.');
        }
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $this->beginTransaction();

            $google_user = Socialite::driver('google')->user();

            $user = User::query()
                ->where('email', $google_user->getEmail())
                ->first();

            if ($user === null) {
                $user = User::query()
                    ->create([
                        'name' => $google_user->getName(),
                        'email' => $google_user->getEmail(),
                        'email_verified_at' => now(),
                        'password' => Hash::make(bin2hex(random_bytes(8))),
                        'role' => 'user',
                    ]);
            }

            Auth::login($user);
            event(new GenericUserActivity('Logged in via google.'));
            $this->commit();

            return redirect()->route('home');
        } catch (\Exception $e) {
            logger($e);
            return redirect('/login')
                ->with('message_type', 'danger')
                ->with('message_content', 'Login failed. Please try again later.');
        }
    }
}
