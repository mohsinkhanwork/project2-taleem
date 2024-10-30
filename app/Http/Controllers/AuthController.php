<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function redirectToProvider()
    {
        $url = Socialite::driver('keycloak')->redirect();
        
        return $url;
    }

    public function handleProviderCallback()
    {
        try {
            $keycloakUser = Socialite::driver('keycloak')->user();
    
            $existingUser = User::where('email', $keycloakUser->getEmail())->first();
    
            if ($existingUser) {
                Auth::login($existingUser);
            } else {
                $newUser = User::create([
                    'name' => $keycloakUser->getName(),
                    'email' => $keycloakUser->getEmail(),
                    'password' => bcrypt('newporjetc1password'), // Assign a default password
                ]);
    
                Auth::login($newUser);
            }
    
            return redirect()->intended('/');
        } catch (\Exception $e) {

            return redirect('/login')->withErrors(['msg' => 'Unable to log in. Please try again.']);
        }
    }

public function logout()
{
    Auth::logout();

    session()->invalidate();  // Invalidate the session
    session()->regenerateToken();  // Prevent CSRF attacks

    return redirect('/login');  // Redirect to login page
}

}