<?php
namespace Modules\Blog\Services;

class CommentService
{

    public function Delete($params)
    {
        if (isset($params['id'])) {
            $id = $params['id'];
            $delete = Comment::where('id', $id)->delete();
            return serviceOk(trans('blog::messages.done'));
        } elseif (isset($params['bulk'])) {
            $ids = $params['bulk'];
            $delete = Comment::whereIn('id', $ids)->delete();
            return serviceOk(trans('blog::messages.done'));
        }

    }

    public function Index($params)
    {
        $comments = Comment::
            when(isset($params['page']) && isset($params['per_page']), function ($query) use ($params) {
            $query->skip($params['page'] * $params['per_page'])->take($params['per_page']);
        })
            ->get();
        return serviceOk(['comments' => $comments]);

    }

    public function Confirm($params)
    {
        $comment = Comment::find($params['id']);
        $comment->is_confirm = $params['is_confirm'];
        $comment->save();

        return serviceOk(trans('blog::messages.done'));
    }
}
