<?php
namespace Modules\Blog\Http\Controllers\WEB\EndUser;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Models\Comment;
use Redirect;

class CommentController extends Controller
{

    public function store(Request $request)
    {

        $comment = new Comment;
        $comment->content = $request->content;
        $comment->user_id = Auth::id();
        $comment->post_id = $request->post_id;
        $comment->save();
        return Redirect::back()->withErrors(trans('blog::messages.done'));

    }

}
