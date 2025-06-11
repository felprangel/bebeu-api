<?php

namespace App\Http\Controllers;

use App\Models\WaterIntake;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class WaterIntakeController
{
    public function create()
    {
        $data = Request::validate([
            'quantity' => ['required', 'integer'],
        ]);

        $intake = new WaterIntake();
        $intake->user_id = Auth::id();
        $intake->quantity = $data['quantity'];
        $intake->save();
    }
}
