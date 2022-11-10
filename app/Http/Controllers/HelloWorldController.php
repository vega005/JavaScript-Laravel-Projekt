<?php

namespace App\Http\Controllers;

class HelloWorldController extends Controller
{
    public function show()
    {
        return view("hello");
    }
}
