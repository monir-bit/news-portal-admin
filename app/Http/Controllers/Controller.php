<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function redirectBackWithSuccess($message)
    {
        return redirect()->back()->with('success', $message);
    }

    public function redirectBackWithError($message){
        return redirect()->back()->with('error', $message);
    }

    public function redirectRouteWithSuccess($route, $message)
    {
        return redirect()->route($route)->with('success', $message);
    }

    public function redirectRouteWithError($route, $message){
        return redirect()->route($route)->with('error', $message);
    }


}
