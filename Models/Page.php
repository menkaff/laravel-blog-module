<?php
namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blog_page';

    public function user()
    {
        return $this->belongsTo('Modules\Auth\Models\User', 'user_id')->withDefault(
            ['id' => 0, 'name' => 'نامشخص']);

    }

}
