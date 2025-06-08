<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UsersController
{
    public function createUser()
    {
        $data = Request::validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users')],
            'password' => ['required', 'confirmed', Password::min(8)]
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $token = $user->createToken('cookie');
        Auth::login($user);

        return ['token' => $token->plainTextToken];
    }

    public function login()
    {
        $data = Request::validate([
            'email' => ['required', 'email'],
            'password' => ['required', Password::min(8)]
        ]);

        if (!Auth::attempt($data)) {
            throw new UnauthorizedHttpException('', 'Email ou senha incorretos');
        }
        $user = User::find(Auth::id());
        $token = $user->createToken('cookie');

        return ['token' => $token->plainTextToken];
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
    }
}
