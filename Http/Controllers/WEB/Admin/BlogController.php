<?php

namespace Modules\Blog\Http\Controllers\WEB\Admin;

use Illuminate\Routing\Controller;

class BlogController extends Controller
{

    public function index()
    {

        return view('blog::' . env('ADMIN_THEME') . '.index');

    }

}
