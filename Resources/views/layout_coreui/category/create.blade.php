@extends('blog::layouts.master')
@section('head')
<link href="/adminlte/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" />
@stop
@section('title')
{{ $title=trans('blog::messages.create').' '.trans('blog::messages.category') }}
@stop

@section('content')

@include('theme::layout_coreui.errorbox')

<form action="/admin/blog/category/store" class="form-horizontal" method="POST">
    @csrf
    <div class="form-group">
        <label>{{trans('blog::messages.name')}}</label>
        <div class="col-sm-10">
            <input class="form-control" type="text" name='name' value='{{Request::old('name')}}' />
        </div>
    </div>



    <div class="tree well">
        {{trans('blog::messages.categories')}}
        {{\Modules\Blog\Models\Category::render(array(),'radio')}}
    </div>
    <button type="submit" class="btn btn-primary text-right"> {{trans('blog::messages.create') }} </button>

</form>



@stop