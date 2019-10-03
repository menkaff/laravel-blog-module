@extends('theme::layout_coreui.master')
@section('head')
<link rel="stylesheet" href="/adminlte/plugins/datatables/dataTables.bootstrap.css" />
@stop
@section('title')
{{ $title=trans('blog::messages.edit').' '.trans('blog::messages.category') }}
@stop

@section('content')

@foreach($errors->all() as $error)
<div class="alert alert-warning">
	{!!$error!!} <a href="#" class="close" data-dismiss="alert">&times;</a>
</div>
@endforeach

<form action="/admin/blog/category/update" class="form-orizontal" method="POST">
	@csrf
	<input type="hidden" name="id" value="{{$category->id}}" />
	<div class="form-group">
		<label>{{trans('blog::messages.name')}}</label>
		<div class="col-sm-10">
			<input class="form-control" type="text" name='name' value='{{Request::old('name',$category->name)}}' />
		</div>
	</div>




	<div class="form-group">

		<label>{{trans('blog::messages.levelone')}}</label>
		<input id="is_root" type="checkbox" name="is_root" value="root" class="col-md-1"
			style="width: 20px;height:20px;" @if($category->isRoot()) checked @endif >

	</div>

	<div class="tree well @if($category->isRoot()) disabledbutton @endif">
		{{trans('blog::messages.categories')}}
		{{\Modules\Blog\Models\Category::render(array($category->parent_id),'radio')}}
	</div>


	<button type="submit" class="btn btn-primary text-right">
		{{trans('blog::messages.edit')}}
	</button>

</form>


@stop