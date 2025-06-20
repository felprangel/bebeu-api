<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        return [
            'token' => $token->plainTextToken,
            'user_data' => [
                'name' => $user->name,
                'water_goal' => $user->water_goal
            ]
        ];
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
    }

    public function changeWaterGoal()
    {
        $data = Request::validate([
            'water_goal' => ['required', 'integer'],
        ]);

        $user = User::find(Auth::id());
        $user->water_goal = $data['water_goal'];
        $user->save();
    }

    public function getWaterGoal()
    {
        $user = User::find(Auth::id());
        return $user->water_goal;
    }

    public function getWaterIntake()
    {
        $intake = DB::select(
            "SELECT SUM(water_intake.quantity) AS sum
            FROM water_intake
            WHERE DATE(water_intake.created_at) = CURDATE()
                AND water_intake.user_id = :user_id",
            ['user_id' => Auth::id()]
        );

        $user = User::find(Auth::id());
        $user->water_goal;

        $percentage = ($intake[0]->sum / $user->water_goal) * 100;

        return $percentage;
    }
}
