<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Post;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.home');
    }

    public function slugger(Request $request) {
        return response()->json([
            'slug' => Post::generateSlug($request->all()['originalStr'])
        ]);
    }
}
