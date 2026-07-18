<?php

namespace App\Http\Controllers;


class LifecoachController extends Controller
{
    public function dashboard()
    {
        return view('lifecoach.dashboard');
    }

    public function patients()
    {
        return view('lifecoach.patients');
    }

    public function notes()
    {
        return view('lifecoach.notes');
    }

    public function tasks()
    {
        return view('lifecoach.tasks');
    }

    public function profile()
    {
        return view('lifecoach.profile');
    }
}
