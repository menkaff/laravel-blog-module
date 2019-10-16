<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Models\Category;
use Modules\Blog\Models\Post;
use Redirect;
use Validator;

class CategoryController extends Controller
{

    public function Create()
    {
        $categories = Category::orderBy('name')->get();
        return view('blog::'.env('ADMIN_THEME').'.category.create', ['categories' => $categories]);
    }

    public function Store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            // 'featured_image' => 'required',
        ]);

        $category = new Category;

        $category->name = $request->input('name');
        // $category->featured_image = parse_url($request->input('featured_image'), PHP_URL_PATH);

        $category->save();

        if ($request->category == "root") {
            $category->makeRoot();
        } else {
            $category->parent_id = $request->category;
            $category->save();
        }

        $category->save();

        // return $request->all();
        // $data = [
        //     'name' => $request->input('name'),
        //     'categories' => $request->input('categories'),
        //     'image' => $request->input('f_image')
        // ];

        // $rules = ['name' => 'required'];

        // $valid = Validator::make($data, $rules);
        // if ($valid->fails()) {
        //     return Redirect::back()->withErrors($valid)->withInput();
        // }

        // $category = Category::create([
        //     'name' => $data['name'],
        //     'image' => $data['image']
        // ]);
        // $category->name = $data['name'];
        // $category->image = $data['image'];
        // $category->save();
        // if ($request->filled('categories')) {
        //     $parent = Category::where('id', '=', $data['categories'])->first();
        //     $category->parent_id = $parent->id;
        // } else {
        //     $category->makeRoot();
        // }

        return Redirect::back()->withErrors(trans('blog::messages.done'));
    }

    public function Edit(Request $request)
    {
        $id = $request->input('id');
        $categories = Category::orderBy('name')->get();
        $category = Category::findOrFail($id);
        return view('blog::'.env('ADMIN_THEME').'.category.edit', [
            'title' => trans('blog::messages.edit') . ' ' . trans('blog::messages.category') . ' : ' . $category->name,
            'category' => $category,
            'categories' => $categories,
        ]);
    }

    public function Update(Request $request)
    {

        $data = [
            'name' => $request->input('name'),
            'categories' => $request->input('categories'),
            'image' => $request->input('f_image'),
        ];
        $rules = ['name' => 'required'];

        $valid = Validator::make($data, $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput();
        }

        $choice = "child";
        if ($request->has('is_root')) {
            $choice = $request->input('radio');
        }
        $id = $request->input('id');
        $category = Category::findorFail($id);
        $category->name = $data['name'];
        $category->image = $data['image'];
        $category->save();
        if ($request->category == "root") {
            $category->makeRoot();
        } else {
            $category->parent_id = $request->category;
            $category->save();
        }

        return Redirect::back()->withErrors(trans('blog::messages.done'));
    }

    public function Show(Request $request)
    {
        $id = $request->input('id');
        $category = Category::findorFail($id);
        $categories = Category::where('parent_id', $id)->get();
        return view('blog::'.env('ADMIN_THEME').'.category.index', [
            'title' => trans('blog::messages.showchild') . ' ' . trans('blog::messages.category') . ' : ' . $category->name,
            'categories' => $categories,
        ]);
    }

    public function Destroy(Request $request)
    {

        $id = $request->input('id');
        $category = Category::findorFail($id);
        $category->delete();
        return Redirect::back()->withErrors(trans('blog::messages.done'));
    }

    public function index()
    {
        $categories = Category::whereIsRoot()->orderBy('name')->get();
        return view('blog::'.env('ADMIN_THEME').'.category.index', array(
            'categories' => $categories,
            'title' => trans('blog::messages.levelone') . ' ' . trans('blog::messages.categories'),
        ));
    }

    /********************* Front Functions ****************************/
    public function index_front()
    {
        $categories = Category::whereIsRoot()->orderBy('name')->get();

        return view('theme::blog.category.index', array(
            'categories' => $categories,
            'title' => trans('blog::messages.levelone') . ' ' . trans('blog::messages.categories'),
        ));
    }

    public function show_front(Request $request)
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
