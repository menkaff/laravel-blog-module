@extends('theme::layout_coreui.master')
@section('head')
<link href="/adminlte/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" />
@stop
@section('title')
{{ $title=trans('blog::messages.create').' '.trans('blog::messages.category') }}
@stop

@section('content')

@foreach($errors->all() as $error)
<div class="alert alert-warning">
    {!!$error!!}
    <a class="close" data-dismiss="alert" href="#">
        ×
    </a>
</div>
@endforeach

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