<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Blog\Models\Category;
use Modules\Blog\Models\Post;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        return view('blog::index');

    }

    public function index_front()
    {
        $posts=Post::all();

        return view('blog::front.index');

    }

}
