<?php
namespace App\Http\Controllers;

use App\Events\UserLogin;
use App\Events\UserLogout;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends DatabaseController
{
    public function showLoginForm()
    {
        return view('auth.login_form');
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
            event(new UserLogin($user));

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

            event(new UserLogout(Auth::user()));
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
            $user = Socialite::driver('google')->user();

            $existing_user = User::query()
                ->where('email', $user->getEmail())
                ->first();

            if ($existing_user === null) {
                $new_user = User::query()
                    ->create([
                        'name' => $user->getName(),
                        'email' => $user->getEmail(),
                        'email_verified_at' => now(),
                        'password' => Hash::make(bin2hex(random_bytes(8))),
                        'role' => 'user',
                    ]);

                event(new UserLogin($new_user));

                Auth::login($new_user);
            } else {
                event(new UserLogin($existing_user));
                Auth::login($existing_user);
            }

            return redirect('/');
        } catch (\Exception $e) {
            logger($e);
            return redirect('/login')
                ->with('message_type', 'danger')
                ->with('message_content', 'Login failed. Please try again later.');
        }
    }
}
