@extends('blog::front.layouts.master') 
@section('head') 
@stop 
@section('title') {{ $title=trans('blog::messages.blog') }} 
@stop

@section('content')

<div class="front_sections row">
    <div class="col-md-4">
        <a class="btn btn-primary col-md-12" href="/blog/posts">
            <h2>
                {{trans('blog::messages.post_section')}}
            </h2>
        </a>
    </div>
    <div class="col-md-4 hide">
        <a class="btn btn-info col-md-12" href="/blog/category">
            <h2>
                {{trans('blog::messages.categories')}}
            </h2>
        </a>
    </div>
    <div class="col-md-4">
        <a class="btn btn-success col-md-12" href="/form">
            <h2>
                {{trans('blog::messages.forms')}}
            </h2>
        </a>
    </div>
</div>

@stop