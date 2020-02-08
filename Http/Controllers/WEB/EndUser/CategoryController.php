<?php

namespace Modules\Blog\Http\Controllers\WEB\EndUser;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Models\Category;
use Modules\Blog\Models\Post;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::whereIsRoot()->orderBy('name')->get();

        return view('theme::blog.category.index', array(
            'categories' => $categories,
            'title' => trans('blog::messages.levelone') . ' ' . trans('blog::messages.categories'),
        ));
    }

    public function show(Request $request)
    {
        $id = $request->input('id');
        $category = Category::findorFail($id);
        $categories = Category::where('parent_id', $id)->get();

        $posts = Post::join('blog_post_category', 'post_id', 'id')
            ->where('blog_post_category.category_id', $id)
            ->where(['status' => 1])
            ->get();

        return view('theme::blog.post.index')->with([
            'title' => trans('blog::messages.showchild') . ' ' . trans('blog::messages.category') . ' : ' . $category->name,
            'categories' => $categories,
            'posts' => $posts]);

    }

}
