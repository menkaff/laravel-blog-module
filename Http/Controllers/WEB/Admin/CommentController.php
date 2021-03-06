<?php
namespace Modules\Blog\Http\Controllers\WEB\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Models\Comment;
use Redirect;

class CommentController extends Controller
{

    public function Delete(Request $request)
    {
        if ($request->filled('id')) {
            $id = $request->get('id');
            $delete = Comment::where('id', $id)->delete();
            return Redirect::back()->withErrors(trans('blog::messages.done'));
        } elseif ($request->filled('bulk')) {
            $ids = $request->get('bulk');
            $delete = Comment::whereIn('id', $ids)->delete();
            return Redirect::back()->withErrors(trans('blog::messages.done'));
        }

    }

    public function Index()
    {
        $comments = Comment::all();
        return view('blog::comment.index', ['comments' => $comments]);

    }

    public function Confirm(Request $request)
    {
        $comment = Comment::find($request->id);
        $comment->is_confirm = $request->is_confirm;
        $comment->save();

        return Redirect::back()->withErrors(trans('blog::messages.done'));
    }

}
