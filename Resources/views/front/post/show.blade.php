@extends('blog::front.layouts.master') 
@section('title') {{ $title=trans('blog::messages.post_section') }} 
@stop 
@section('content')
<div class="text-right">

    <img src="{{$post->f_image}}" class="col-md-12" height="300" onerror="this.src='/noImage.svg'">
    <h1>
        {{$post->title}}
    </h1>
    <div class="post_list_content">
    {{strip_tags($post->content)}}
    </div>
   
    
</div>

@stop