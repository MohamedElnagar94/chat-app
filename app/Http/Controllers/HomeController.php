<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('chat');
    }

    public function send(Request $request){
        $user = User::find(Auth::id());
        event(new ChatEvent($request->message,$user));
    }
    public function user(){
        $user = User::find(Auth::id());
        return response()->json(['user' => $user]);
    }
//    public function send(){
//        $message = "hello";
//        $user = User::find(Auth::id());
//        event(new ChatEvent($message,$user));
//    }
}
