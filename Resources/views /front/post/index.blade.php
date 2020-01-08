@extends('blog::front.layouts.master') 
@section('title') {{ $title=trans('blog::messages.post_section') }} 
@stop 
@section('content')
<div class="text-right">
    @if($posts->isEmpty())
    <h3>
        {{ trans('blog::messages.empty') }}
    </h3>
    @endif @foreach($posts as $post)
    <h1>
        {{$post->title}}
    </h1>
    <div class="post_list_content">
        {{strip_tags($post->excerpt)}}
    </div>
    <a href="/blog/posts/show?id={{$post->id}}" class="btn btn-primary">{{trans('blog::messages.show')}}</a>
    <hr /> @endforeach
</div>


@stop