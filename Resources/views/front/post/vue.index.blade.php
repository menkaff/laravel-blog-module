@extends('blog::front.layouts.master')
@section('head')
<script src="/vue/vue.js">
</script>
<script src="/vue/vue-router.min.js">
</script>
<script src="/vue/axios.min.js">
</script>
@stop
@section('title')
{{ $title=trans('blog::messages.post_section') }}
@stop

@section('content')
<ol class="breadcrumb">
    <li>
        <a href="/">
            <i class="fa fa-home">
            </i>
            {{trans('blog::messages.home')}}
        </a>
    </li>
    <li>
        <a href="/blog">
            {{trans('blog::messages.blog')}}
        </a>
    </li>
    <li>
        <a href="/blog/post">
            {{trans('blog::messages.post_section')}}
        </a>
    </li>
</ol>
<div id="app">
    <router-link :to="'/blog/post/'">
        <h1>
            {{trans('blog::messages.post_section')}}
        </h1>
    </router-link>
    <router-view>
    </router-view>
</div>
<script src="/modules/blog/js/post.js">
</script>
@stop
