<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    
    public function index()
    {   
        return view('front/home/plantilla', [
            'title' => 'CVA Muebles'
        ]);
    }
}
