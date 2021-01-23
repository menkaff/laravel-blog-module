@extends('blog::layouts.master') 
@section('title') {{ $title=trans('blog::messages.report').' '.trans('blog::messages.post') }} 
@stop

@section('content')

<div class="row col-md-12">
    @foreach ($post->seen() as $role)
    <div class="col-md-4">
    <div class="report_role">
        <h3>
            نوع کاربر : 
            {{$role->display_name}}
        </h3>
        <h5>
                تعداد : 
                {{count($role->pp)}}
        </h5>
        <ul class="form-control">
            
        @foreach($role->pp as $user)
        <li>
              <h4>
                  نام کاربر :
                  {{$user->name.' '.$user->family}}
              </h4>
        </li>
        @endforeach
        </ul>
    </div>
    </div>
    @endforeach
</div>

<div class="col-md-12">
    <hr>
<h3>لیست کلی</h3>
        @foreach($post->seen_all() as $user)
        
              <h4>
                  نام کاربر :
                  {{$user->name.' '.$user->family}}
              </h4>
        
        @endforeach
</div>



@stop