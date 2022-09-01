<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function changeTime(Request $request)
    {
        $user = $request->user;
        $city = $request->city;

        $user= User::findOrFail($user);        
        $user->timezone = $city;
        $user->save();
        echo "1";
    }
}
