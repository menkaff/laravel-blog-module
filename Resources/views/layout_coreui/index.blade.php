@extends('blog::layouts.master')
@section('head')
@stop
@section('title') {{ $title=trans('blog::messages.blog') }}
@stop
@section('content')





<a href="/admin/blog/post" class="btn btn-info">
    {{trans('blog::messages.post_section')}}
</a>

<a href="/admin/blog/category" class="btn btn-info">
    {{trans('blog::messages.category_section')}}
</a>


@stop