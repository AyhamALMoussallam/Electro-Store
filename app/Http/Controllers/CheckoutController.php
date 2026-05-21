<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Area;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout', [
            'cities' => City::all(),
            'areas'  => Area::all(),
        ]);
    }
}