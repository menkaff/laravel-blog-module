@extends('theme::layout_coreui.master')
@section('head')
<link href="/adminlte/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" />
@stop
@section('title') {{ $title=trans('blog::messages.comment_section') }}
@stop
@section('content')

@if ($comments->isEmpty())
<h3>
    {{ trans('blog::messages.empty') }}
</h3>
@else
<button class="btn btn-danger fa fa-trash" data-toggle="confirmation" id="delete_ids">
</button>
<table class="table table-hover tablesorter table-striped table-borderd dbt text-center" id="myTable">
    <thead>
        <tr class="info">
            <th>
                {{trans('blog::messages.group_delete')}}
                <input id="selectall" type="checkbox" />
            </th>
            <th>
                <i class="fa fa-ellipsis-v"></i>
            </th>
            <th>
                {{ trans('blog::messages.user') }}
            </th>
            <th>
                {{ trans('blog::messages.content') }}
            </th>
            <th>
                {{ trans('blog::messages.created_at') }}
            </th>


        </tr>
    </thead>
    <tbody class="filterlist">
        @foreach($comments as $comment)
        <tr>
            <td>
                <input class="case" name="ids" type="checkbox" value="{{$comment->id}}" />
            </td>
            <td>
                <a class="btn btn-info fa fa-eye" href="/blog/post/show?id={{$comment->post_id}}">
                </a>

                @if($comment->is_confirm)
                <a class="btn btn-warning fa fa-times"
                    href="/admin/blog/comment/confirm?id={{$comment->id}}&is_confirm=0">
                </a>
                @else
                <a class="btn btn-info fa fa-check"
                    href="/admin/blog/comment/confirm?id={{$comment->id}}&is_confirm=1">
                </a>
                @endif

                <a class="btn btn-danger fa fa-trash" data-toggle="confirmation"
                    href="/admin/blog/comment/delete?id={{$comment->id}}">
                </a>

            <td>
                {{$comment->content}}
            </td>


            <td>
                <a href="/admin/user/show?id={{$comment->user_id}}">
                    {{$comment->user->fullname()}}
                </a>

            </td>


            <td>
                {{jDate::forge($comment -> created_at)->format('Y-m-d')}}
            </td>


        </tr>
        @endforeach
    </tbody>
</table>
@endif


@stop