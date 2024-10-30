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

            $existingUser = User::where('keycloak_id', $keycloakUser->getId())->first();

            if ($existingUser) {
                Auth::login($existingUser);
            } else {
                $existingUserByEmail = User::where('email', $keycloakUser->getEmail())->first();

                if ($existingUserByEmail) {
                    $existingUserByEmail->update([
                        'keycloak_id' => $keycloakUser->getId(),
                    ]);
                    Auth::login($existingUserByEmail);
                } else {
                    $newUser = User::create([
                        'name' => $keycloakUser->getName(),
                        'email' => $keycloakUser->getEmail(),
                        'keycloak_id' => $keycloakUser->getId(),
                        'password' => bcrypt('newprojectpassword'),
                    ]);
                    Auth::login($newUser);
                }
            }

            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['msg' => 'Unable to log in. Please try again.']);
        }
    }

    public function logout()
    {
        Auth::logout();
    
        session()->invalidate();
        session()->regenerateToken();
    
        $redirectUri = url('/login');
        $clientId = config('services.keycloak.client_id');
        dd($clientId);
        $keycloakLogoutUrl = Socialite::driver('keycloak')->getLogoutUrl($redirectUri, $clientId);
    
        // Redirect the user to the Keycloak logout URL
        return redirect($keycloakLogoutUrl);
    }

}