<?php

namespace Modules\Blog\Http\Controllers\WEB\EndUser;

use Illuminate\Routing\Controller;
use Modules\Blog\Models\Post;

class BlogController extends Controller
{

    public function index()
    {
        $posts = Post::all();

        return view('blog::front.index');

    }

}
