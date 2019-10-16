@extends('blog::layouts.master')
@section('title')
{{ $title=trans('blog::messages.blog').' '.trans('blog::messages.post_new') }}
@stop

@section('content')

{!! Form::open(['url'=>['/admin/blog/post/store'],'class'=>'form-horizontal']) !!}

@include('theme::layout_coreui.errorbox')

<div class="form-group hide">
    <label for="" class="col-md-2 control-label">{{trans('blog::messages.status')}}</label>
    <div class="col-sm-10">
      <select name="status">
        <option value="1" selected>publish</option>
        <option value="0" >hide</option>
      </select>
    </div>
</div>

<div class="form-group">
    <label for="" class="col-md-2 control-label">{{trans('blog::messages.title')}}</label>
    <div class="col-sm-10">
        <input class="form-control" type="text" name='title' value='{{Request::old('title')}}' />
    </div>
</div>
<div class="form-group">
    <label for="" class="col-md-2 control-label">{{trans('blog::messages.content')}}</label>
    <div class="col-sm-10">
        <textarea name='content'>{{Request::old('content')}}</textarea>
    </div>
</div>

<div class="form-group">
        <label for="" class="col-md-2 control-label">{{trans(trans('blog::messages.image').' '.trans('blog::messages.featured'))}}</label>
        <div class="col-sm-10">
    <div class="input-group">
        <span class="input-group-btn">
            <a class="btn btn-primary" data-input="thumbnail" data-preview="holder" id="lfm">
                <i class="fa fa-picture-o">
                </i>
                {{trans('blog::messages.choose')}}
            </a>
        </span>
        <input value="{!!Request::old('filepath')!!}" class="form-control" id="thumbnail" name="filepath" type="text" />
    </div>
    <img id="holder" style="margin-top:15px;max-height:100px;" src="{!!Request::old('filepath')!!}" /> 
        </div>
    </div>


    
<div class="tree well">
    {{ trans('blog::messages.categories') }}
            {{\Modules\Blog\Models\Category::render()}}
</div>
    <button type="submit" class="btn btn-primary text-right" > {{trans('blog::messages.create') }} </button>

</form>
<script src="/tinymce/tinymce.min.js">
</script>
<script>
    var editor_config = {
               directionality : 'rtl',
              language: 'fa_IR',
    path_absolute : "/",
    selector: "textarea.my-editor",
    plugins: [
      "directionality advlist autolink lists link image charmap print preview hr anchor pagebreak",
      "searchreplace wordcount visualblocks visualchars code fullscreen",
      "insertdatetime media nonbreaking save table contextmenu directionality",
      "emoticons template paste textcolor colorpicker textpattern"
    ],
    toolbar: "rtl ltr insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
    relative_urls: false,
    file_browser_callback : function(field_name, url, type, win) {
      var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
      var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

      var cmsURL = editor_config.path_absolute + 'filemanager?field_name=' + field_name;
      if (type == 'image') {
        cmsURL = cmsURL + "&type=Images";
      } else {
        cmsURL = cmsURL + "&type=Files";
      }

      tinyMCE.activeEditor.windowManager.open({
        file : cmsURL,
        title : 'Filemanager',
        width : x * 0.8,
        height : y * 0.8,
        resizable : "yes",
        close_previous : "no"
      });
    }
  };

  tinymce.init(editor_config);
</script>
<script src="/vendor/laravel-filemanager/js/lfm.js">
</script>
<script>
    $(function () {
   $('#lfm').filemanager('image');
   $('#lfm2').filemanager('file');
  });
</script>
@stop
