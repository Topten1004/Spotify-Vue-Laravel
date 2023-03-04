<?php

namespace App\installer\Controllers;

use Illuminate\Routing\Controller;

class WelcomeController extends Controller
{
    /**
     * Display the installer welcome page.
     *
     * @return \Illuminate\Http\Response
     */
    public function installer()
    {
        return view('installer.installer');
    }
}
