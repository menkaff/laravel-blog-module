<?php
namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{

    protected $table = 'blog_image';

    public function getUrlAttribute($image)
    {
        return $image = make_absolute($image, env('APP_URL'));
    }

}
