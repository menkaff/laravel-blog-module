<?php
namespace App\Http\Controllers\Admin\Post;
use DB, Redirect, Input, Auth,Mail;
use App\Http\Controllers\Controller;
use App\Models\Post\Page,App\Models\User;

class PagesController extends Controller {

	public function __construct() {
		$this -> middleware('auth');
		$this -> middleware('role:admin');
	}

	public function Create() {
		return view(env('admin').'posts.pages.create');

	}
	
	public function nl_create() {
		return view(env('admin').'posts.newsletter');

	}
	
	public function nl_store(){
	$data=array();
	$data['title']=Input::get('title');
	$data['content']=Input::get('content');
	$data['f_image']=Input::get('f_image');

	$users=User::select(['email'])->get();
	
	foreach($users as $user){
		$email=$user->email;
	Mail::send('emails.newsletter', $data, function ($message)use($email) {
    $message->from('newsletter@kermanbalan.com', 'کرمان بالان');

    $message->to($email)->subject('خبرنامه');
     });
	}
	
	$nl_users=DB::table('newsletter_users')->all();
	if(count($nl_users)>0){
	foreach($nl_users as $nl_user){
		$email=$nl_user->email;
	Mail::send('emails.newsletter', $data, function ($message)use($email) {
    $message->from('newsletter@kermanbalan.com', 'کرمان بالان');

    $message->to($email)->subject('خبرنامه');
     });
	}
	}
	 
	 return Redirect::back() -> withErrors(trans('blog::messages.done'));
	}
	

	public function Store() {
		$page = new page();

		$page -> title = Input::get('title');
		$page -> user_id = Auth::user() -> id;
		$page -> content = Input::get('content');
		$page -> f_image = Input::get('f_image');
		$page -> save();

		if (Input::has('tags')) {
			//$tags = explode(',', Input::get('tags'));
			//$page -> tag($tags);
		}

		return Redirect::back() -> withErrors(trans('blog::messages.done'));

	}

	public function Edit() {
		$id = Input::get('id');
		$page = page::find($id);
		if ($page -> page_id != 0)
			return Redirect::back() -> withErrors(trans('blog::messages.error'));

		if ($page -> f_image != null) {
			$f_image = $page -> f_image;
			$f_image = str_replace('/filemanager/', '/thumbs/', $page -> f_image);
			$path_parts = pathinfo($f_image);
			$f_image = $path_parts['dirname'] . '/' . $path_parts['filename'] . '-small.' . $path_parts['extension'];

		} else {
			$f_image = '';
		}

		//$tags = $page -> tagNames();
		//$tags = implode(',', $tags);
		$f_image = strtolower($f_image);
		return view(env('admin').'posts.pages.edit', [
		'f_image' => $f_image,
		'page' => $page]);

	}

	public function Update() {
		$id = Input::get('id');
		$title = Input::get('title');

		$page = page::find($id);
		if ($page -> page_id != 0)
			return Redirect::back() -> withErrors(trans('blog::messages.error'));

		$page -> title = $title;
		$page -> content = Input::get('content');
		$page -> f_image = Input::get('f_image');
		$page -> save();

		if (Input::has('tags')) {
			//$tags = explode(',', Input::get('tags'));
			//$page -> tag($tags);
		}

		return Redirect::back() -> withErrors(trans('blog::messages.done'));
	}

	public function Delete() {

		if (Input::has('id')) {
			$id = Input::get('id');
			$delete = page::where('id', $id) -> delete();
			return Redirect::back() -> withErrors(trans('blog::messages.done'));
		} elseif (Input::has('bulk')) {
			$ids = Input::get('bulk');
			\DB::table('pages') -> whereIn('id', $ids) -> delete();
			return Redirect::back() -> withErrors(trans('blog::messages.done'));
			return $ids;
		}

	}

	public function Index() {

		if (Input::has('location_id')) {
			$location_id = Input::get('location_id');
			$location = location::findorFail($location_id);
			$ids = DB::table('page_location') -> select('page_id') -> where('location_id', $location_id);
			$pages = page::whereIn('id', $ids) -> get();
			return view(env('admin').'posts.pages.all', ['pages' => $pages]);
		}
		$pages = page::all();
		return view(env('admin').'posts.pages.all', ['pages' => $pages]);

	}

}
