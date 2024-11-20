<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller{

    public function dashboard(){
        
        return view('admin.dashboard', [
            'breadcrumb' => (object)[
                'title' => 'Dashboard Admin',
                'list' => ['Home', 'Dashboard']
            ],
            'activemenu' => 'dashboard'
        ]);
    }
}