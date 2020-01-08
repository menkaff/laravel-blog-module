@extends('theme::layout_coreui.master')
@section('title')
{{ $title=trans('blog::messages.blog').' '.trans('blog::messages.page_new') }}
@stop

@section('content')



{!! Form::open(['url'=>['/admin/blog/page/store'],'class'=>'form-horizontal']) !!}

@include('theme::layout_coreui.errorbox')

<div class="form-group hide">
  <label for="" class="col-md-2 control-label">{{trans('blog::messages.status')}}</label>
  <div class="col-sm-10">
    <select name="status">
      <option value="1" selected>{{trans('blog::messages.publish')}}</option>
      <option value="0">{{trans('blog::messages.draft')}}</option>
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
    <textarea class="my-editor" name='content'>
          {{Request::old('content')}}
    </textarea>
  </div>
</div>

<div class="form-group">
  <label for=""
    class="col-md-2 control-label">{{trans(trans('blog::messages.image').' '.trans('blog::messages.featured'))}}</label>
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




<button type="submit" class="btn btn-primary text-right"> {{trans('blog::messages.create') }} </button>

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
    file_browser_callback: function(field_name, url, type, win) {
        tinyMCE.activeEditor.windowManager.open({
          file: '/file-manager/tinymce',
          title: 'Laravel File Manager',
          width: window.innerWidth * 0.8,
          height: window.innerHeight * 0.8,
          resizable: 'yes',
          close_previous: 'no',
        }, {
          setUrl: function(url) {
            win.document.getElementById(field_name).value = url;
          },
        });
      },
  };

  tinymce.init(editor_config);

</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {

document.getElementById('lfm').addEventListener('click', (event) => {
  event.preventDefault();

  window.open('/file-manager/fm-button', 'fm', 'width=1400,height=800');
});
});

// set file link
function fmSetLink($url) {
document.getElementById('thumbnail').value = $url;
}
  
</script>


@stop