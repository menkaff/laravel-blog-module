<?php
namespace App\Http\Controllers\Admin\Post;
use DB, Redirect, Input, Auth;
use App\Http\Controllers\Controller;
use App\Models\Post\Post, App\Models\Post\Comment;

class CommentsController extends Controller {

	public function __construct() {
		$this -> middleware('auth');
		$this -> middleware('role:admin');
	}

	

	public function Edit() {
		$id = Input::get('id');
		$comment = Comment::find($id);
		$posts = post::all();
		return view(env('admin').'posts.comments.edit', [
		'comment' => $comment,
		'posts' => $posts]);
	}

	public function Update() {
		$id = Input::get('id');
		$comment = Comment::find($id);

		if (Input::get('choice') == 'post')
			$comment -> post_id = Input::get('post_id');

		$comment -> content = Input::get('content');
		$comment -> save();
		return Redirect::back() -> withErrors(trans('blog::messages.done'));
	}

	public function Delete() {
		if (Input::has('id')) {
			$id = Input::get('id');
			$delete = Comment::where('id', $id) -> delete();
			return Redirect::back() -> withErrors(trans('blog::messages.done'));
		} elseif (Input::has('bulk')) {
			$ids = Input::get('bulk');
			$delete = Comment::whereIn('id', $ids) -> delete();
			return Redirect::back() -> withErrors(trans('blog::messages.done'));
		}

	}

	public function Index() {
		$comments = Comment::all();
		return view('blog::'.env('ADMIN_THEME').'.posts.comments.all', ['comments' => $comments]);

	}

}
