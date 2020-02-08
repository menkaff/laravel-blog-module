@extends('blog::layouts.master') 
@section('head')
<link href="/adminlte/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" /> 
@stop 
@section('title') {{ $title=trans('blog::messages.post_section') }} 
@stop 
@section('content')

<a class="btn btn-info" href="/admin/blog/post/create">
    <i class="fa fa-plus-square">
    </i>
    {{ trans('blog::messages.new') }}
</a> @if ($posts->isEmpty())
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
                {{ trans('blog::messages.title') }}
            </th>
            <th>
                {{ trans('blog::messages.content') }}
            </th>
            <th>
                کاربر ادمین
            </th>
            


            <th>
                {{ trans('blog::messages.created_at') }}
            </th>
            <th>
                {{ trans('blog::messages.updated_at') }}
            </th>

        </tr>
    </thead>
    <tbody class="filterlist">
        @foreach($posts as $post)
        <tr>
            <td>
                <input class="case" name="ids" type="checkbox" value="{{$post->id}}" />
            </td>
            <td>
                <a class="btn btn-info fa fa-eye" href="/blog/posts/show?id={{$post->id}}">
                    </a>

                <a class="btn btn-info fa fa-edit" href="/admin/blog/post/edit?id={{$post->id}}">
                    </a>

                <a class="btn btn-danger fa fa-trash" data-toggle="confirmation" href="/admin/blog/post/delete?id={{$post->id}}">
                    </a>

                <td>
                    {{$post->title}}
                </td>
                <td>
                    {{strip_tags(str_limit($post->content,20))}}
                </td>

                <td>
                    {{-- <a href="/admin/users/show?id={{$post->user->id}}"> --}}
                        {{$post->user->name.' '.$post->user->family}}
                    {{-- </a> --}}

                </td>
               

                <td>
                    {{jDate::forge($post -> created_at)->format('Y-m-d')}}
                </td>
                <td>
                    {{jDate::forge($post -> updated_at)->format('Y-m-d')}}
                </td>

        </tr>
        @endforeach
    </tbody>
</table>
@endif


@stop