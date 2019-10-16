<?php

namespace Modules\Blog\Http\Controllers;

use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Blog\Models\Comment;
use Modules\Blog\Models\Post;
use Redirect;
use Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $posts = Post::get();

        return view('blog::' . env('ADMIN_THEME') . '.post.index')->with('posts', $posts);

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {

        return view('blog::' . env('ADMIN_THEME') . '.post.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $post = new Post;

        $data = [
            'title' => $request->input('title'),
            'content' => $request->input('content')];

        $rules = ['title' => 'required', 'content' => 'required'];

        $valid = Validator::make($data, $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput();
        }

        $post->status = $request->input('status');
        $post->title = $request->input('title');
        $post->url = str_replace(' ', '-', $request->input('title'));
        $post->f_image = $request->input('filepath');
        $post->user_id = Auth::user()->id;
        $images = $request->input('multi_image');
        $post->content = $request->input('content');
        $post->excerpt = str_limit(strip_tags($request->input('content'), 50));

        if ($images != '') {
            $images = explode(',', $images);
            $i = 0;
            foreach ($images as $image) {
                if ($i != 0) {
                    $post->content .= "<img src='/$image' >";
                } else {
                    $post->content .= "<img src='$image' >";
                }
                $i++;
            }
        }

        $post->save();

        if ($request->has('categories')) {
            $categories = $request->input('categories');
            foreach ($categories as $category) {
                DB::table('blog_post_category')->insert([
                    'post_id' => $post->id,
                    'category_id' => $category]);
            }
        }
        return Redirect::back()->withErrors(trans('blog::messages.done'));
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function report(Request $request)
    {
        $post = Post::find($request->input('id'));

        return view('blog::' . env('ADMIN_THEME') . '.post.report')->with('post', $post);
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit(Request $request)
    {
        $id = $request->input('id');
        $post = post::find($id);
        if ($post->post_id != 0) {
            return Redirect::back()->withErrors(trans('blog::messages.error'));
        }

        $categories = DB::table('blog_post_category')->select('category_id')->where('post_id', $id)->get();
        $categories_ids = array();
        foreach ($categories as $category) {
            $categories_ids[] = $category->category_id;
        }

        return view('blog::' . env('ADMIN_THEME') . '.post.edit')->with(
            'post', $post)->with(
            'categories', $categories_ids);

    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $images = $request->input('multi_image');

        $data = [
            'title' => $request->input('title'),
            'content' => $request->input('content')];

        $rules = ['title' => 'required', 'content' => 'required'];

        $valid = Validator::make($data, $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput();
        }

        $post = Post::find($id);
        if ($post->post_id != 0) {
            return Redirect::back()->withErrors(trans('blog::messages.error'));
        }

        $post->status = $request->input('status');
        $post->title = $request->input('title');
        $post->url = str_replace(' ', '-', $request->input('title'));
        $post->f_image = $request->input('filepath');
        $post->content = $request->input('content');
        $post->excerpt = str_limit(strip_tags($post->content, 50));
        if ($request->input('is_comment') == -1) {
            $post->is_comment = -1;
        }
        if ($images != '') {
            $images = explode(',', $images);
            $i = 0;
            foreach ($images as $image) {
                if ($i != 0) {
                    $post->content .= "<img src='/$image' >";
                } else {
                    $post->content .= "<img src='$image' >";
                }
                $i++;
            }
        }
        $post->save();

        $categories = $request->input('categories');
        DB::table('blog_post_category')->where('post_id', $id)->delete();
        if ($categories != null) {
            foreach ($categories as $category) {
                DB::table('blog_post_category')->insert([
                    'post_id' => $id,
                    'category_id' => $category]);
            }
        }

        return Redirect::back()->withErrors(trans('blog::messages.done'));
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('ids')) {
            $ids = $request->input('ids');
        } else {
            $ids = array($request->input('id'));
        }

        foreach ($ids as $id) {

            $delete = post::where('id', $id)->delete();
            $delete = DB::table('blog_post_category')->where('post_id', $id)->delete();

            $posts = Post::get();
            $post_ids = array();
            foreach ($posts as $post) {
                $post_ids[] = $post->id;
            }
            $delete = DB::table('blog_post_category')->whereNotIn('post_id', $post_ids)->delete();
        }
        if (!$request->ajax()) {
            return Redirect::back()->withErrors(trans('blog::messages.done'));
        }

    }

    /********************* Front Functions ****************************/
    public function index_front(Request $request)
    {
        if ($request->has('category_id')) {
            $posts = DB::table('blog_post')->join('blog_post_category', 'blog_post.id', '=', 'blog_post_category.post_id')->where('blog_post_category.category_id', $request->input('category_id'))->get();
        } else {
            $posts = Post::all();
        }

        return view('blog::front.post.index')->with(['posts' => $posts]);
    }

    public function show_front(Request $request)
    {
        $post = Post::find($request->id);

        return view('blog::front.post.show')->with(['post' => $post]);
    }

    /********************* API Functions ****************************/
    public function index_api(Request $request)
    {
        if (!$request->has('user_name')) {
            return $posts = Post::where('status', 1)->get();
        } else {
            $user = User::where('name', $request->input('user_name'))->first();

            return $posts = Post::where('user_id', $user->id)->where('status', 1)->skip($request->input('skip'))->limit($request->input('limit'))->get();
        }

    }

    public function show_api(Request $request)
    {

        // $post = Post::where('url', $request->input('url'))->where('status', 1)->with(['user', 'comments'])->first();
        // $post->increment('visit');
        // $post->save();
        $post = Post::find($request->input('id'));
        $post->user_name = $post->user->name . ' ' . $post->user->family;

        return $post;

    }

    public function store_comment(Request $request)
    {

        $comment = new Comment;
        $comment->content = $request->input()['params']['content'];
        $comment->user_id = $request->input()['params']['user_id'];
        $comment->post_id = $request->input()['params']['post_id'];
        $comment->user_name = $comment->user->name;
        $comment->save();
        //return 1;
        return $comment;

    }
}
