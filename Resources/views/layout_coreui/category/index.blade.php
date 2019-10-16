@extends('blog::layouts.master')
@section('head')
@stop
@section('title') {{ $title=trans('blog::messages.categories') }}
@stop
@section('content')
<a class="btn btn-info" href="/admin/blog/category/create">
    <i class="fa fa-plus-square">
    </i>
    {{ trans('blog::messages.new') }}
</a> @if ($categories->isEmpty())
<h3>
    {{ trans('blog::messages.empty') }}
</h3>
@else


<table class="table table-hover tablesorter table-striped table-borderd dbt text-center" id="myTable">
    <thead>
        <tr class="info">
            <th>
                <i class="fa fa-ellipsis-v"></i>
            </th>

            <th>
                {{ trans('blog::messages.name') }}
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
        @foreach($categories as $category)
        <tr>
            <td>
                <a class="btn btn-info fa fa-eye" href="/admin/blog/category/show?id={{$category->id}}">
                </a>

                <a class="btn btn-info fa fa-edit" href="/admin/blog/category/edit?id={{$category->id}}">
                </a>

                <a class="btn btn-danger fa fa-trash" data-toggle="confirmation"
                    href="/admin/blog/category/destroy?id={{$category->id}}">
                </a>
            </td>
            <td>
                {{$category->name}}

            </td>



            <td>
                {{jDate::forge($category -> created_at)->format('%d %B, %Y')}}
            </td>
            <td>
                {{jDate::forge($category -> updated_at)->format('%d %B, %Y')}}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif


@endsection


