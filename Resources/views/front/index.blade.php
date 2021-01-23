@extends('blog::front.layouts.master')
@section('head')
@stop
@section('title') {{ $title=trans('blog::messages.blog') }}
@stop

@section('content')

<div class="front_sections row">

    <a class="button button-3d button-rounded button-primary " href="/blog/post">

        {{trans('blog::messages.post_section')}}

    </a>

    <a class="button button-3d button-rounded button-info " href="/blog/category">

        {{trans('blog::messages.categories')}}

    </a>

   

</div>

@stop