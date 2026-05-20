<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationMapController extends Controller
{
    /**
     * Display the location tracking map page
     */
    public function index()
    {
        return view('location-map.index');
    }
}
