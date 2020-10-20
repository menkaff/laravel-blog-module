<?php
namespace Modules\Blog\Services;

use App\Rules\PersianYear;
use Carbon\Carbon;
use DB;
use File;
use Modules\Blog\Models\Image;
use Modules\Blog\Models\Post;
use Validator;

class PostService
{

    public function recent_posts($count = 3)
    {
        $recent_posts = Post::orderBy('created_at', 'DESC')->limit($count)->get();
        return serviceOk($recent_posts);
    }

    public function index($params)
    {
        $validator = Validator::make($params, [
            'from_date' => ['nullable', new PersianYear],
            'to_date' => ['nullable', new PersianYear],
            'page' => 'nullable|numeric',
            'per_page' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return serviceError($validator->errors());
        }

        $posts = Post::
            when(isset($params['word']), function ($query) use ($params) {
            $query->where('title', 'LIKE', '%' . $params['word'] . '%');
            $query->orwhere('content', 'LIKE', '%' . $params['word'] . '%');
        })
            ->when(isset($params['from_date']), function ($q) use ($params) {
                $from_date = explode('/', $params['from_date']);
                $from_date_g = \Morilog\Jalali\CalendarUtils::toGregorian($from_date[0], $from_date[1], $from_date[2]);
                $from_date_g = Carbon::createFromDate($from_date_g[0], $from_date_g[1], $from_date_g[2], 'Asia/Tehran')->format('Y-m-d');

                return $q->where('blog_post.created_at', '>=', $from_date_g);
            })
            ->when(isset($params['to_date']), function ($q) use ($params) {
                $to_date = explode('/', $params['to_date']);
                $to_date_g = \Morilog\Jalali\CalendarUtils::toGregorian($to_date[0], $to_date[1], $to_date[2]);
                $to_date_g = Carbon::createFromDate($to_date_g[0], $to_date_g[1], $to_date_g[2], 'Asia/Tehran')->format('Y-m-d');

                return $q->where('blog_post.created_at', '<=', $to_date_g);
            })
            ->when(isset($params['user_id']), function ($query) use ($params) {
                // $query->where('blog_post.user_id', $params['user_id']);
                if (is_array($params['user_id'])) {
                    $query->whereIn('blog_post.user_id', $params['user_id']);
                } else {
                    $query->where('blog_post.user_id', '=', $params['user_id']);
                }
            })
            ->when(isset($params['user_table']), function ($query) use ($params) {
                $query->where('blog_post.user_table', $params['user_table']);
            })
            ->when(isset($params['user_table']) && isset($params['include_user']), function ($query) use ($params) {
                $query->join($params['user_table'], $params['user_table'] . '.id', 'blog_post.user_id');
                $query->where('blog_post.user_table', $params['user_table']);

                $query->addSelect([
                    $params['user_table'] . '.id as user.id',
                    $params['user_table'] . '.name as user.name',
                ]);
            })
            ->when(isset($params['category_ids']), function ($query) use ($params) {

                $query->join('blog_post_category', 'blog_post_category.post_id', 'blog_post.id');
                $query->whereIn('blog_post_category.category_id', $params['category_ids']);

                return $query;
            })
            ->when(isset($params['order_by']), function ($query) use ($params) {
                $query->orderBy($params['order_by']['key'], $params['order_by']['value']);
            })
            ->when(isset($params['page']) && isset($params['per_page']), function ($query) use ($params) {
                return $query->skip($params['page'] * $params['per_page'])->take($params['per_page']);
            })
            ->with(['images'])
            ->addSelect([
                "blog_post.id",
                "blog_post.title",
                "blog_post.url",
                "blog_post.user_id",
                "blog_post.user_table",
                "blog_post.content",
                "blog_post.excerpt",
                "blog_post.image",
                "blog_post.video",
                "blog_post.status",
                "blog_post.is_comment",
                "blog_post.created_at",
                "blog_post.updated_at",
            ])
            ->get();

        return serviceOk($posts);

    }

    public function store($params, $request)
    {
        $post = new Post;

        $data = [
            'title' => $params['title'],
            'content' => $params['content']];

        $rules = ['title' => 'required', 'content' => 'required'];

        $valid = Validator::make($data, $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput();
        }
        if (isset($params['status'])) {
            $post->status = $params['status'];
        }

        $post->title = $params['title'];
        $post->url = str_replace(' ', '-', $params['title']);
        $post->user_id = $params['user_id'];
        $post->user_table = $params['user_table'];
        $post->content = $params['content'];
        $post->excerpt = str_limit(strip_tags($params['content'], 50));

        if (isset($params['is_comment'])) {
            $post->is_comment = $params['is_comment'];
        }

        if (isset($params['filepath'])) {
            $post->image = parse_url($params['filepath'], PHP_URL_PATH);
        } elseif ($request->hasFile('image') || $request->filled('image')) {
            $is_upload = upload_file($request->file('image'), null, $post->id, 'uploads/blog/post', $request->image);
            if ($is_upload) {
                $post->image = $is_upload;
            } else {
                return serviceError('Image Invalid');
            }

        }

        if ($request->hasFile('video')) {
            $is_upload = upload_file($request->file('video'), null, $post->id, 'uploads/blog/post');
            if ($is_upload) {
                $post->video = $is_upload;
            } else {
                return serviceError('Video Invalid');
            }

        }

        if (isset($params['created_at'])) {
            $created_at = explode('/', $params['created_at']);
            $created_at_g = \Morilog\Jalali\CalendarUtils::toGregorian($created_at[0], $created_at[1], $created_at[2]);
            $created_at_g = Carbon::createFromDate($created_at_g[0], $created_at_g[1], $created_at_g[2]);
            $post->created_at = $created_at_g;
        }

        $post->save();

        if ($request->images && is_array($request->images)) {

            foreach ($request->images as $image) {

                $is_upload = upload_file($image, null, $post->id, 'uploads/blog/post', $image);
                if ($is_upload) {

                    $blog_image = new Image;
                    $blog_image->user_id = $params['user_id'];
                    $blog_image->user_type = $params['user_table'];
                    $blog_image->parent_id = $post->id;
                    $blog_image->parent_type = get_class($post);
                    $blog_image->url = $is_upload;
                    $blog_image->save();
                } else {
                    return serviceError('Image Invalid');
                }
            }

        }

        if (isset($params['categories'])) {
            $categories = $params['categories'];
            if (!is_array($categories)) {
                $categories = [$params['categories']];
            }
            foreach ($categories as $category) {
                DB::table('blog_post_category')->insert([
                    'post_id' => $post->id,
                    'category_id' => $category]);
            }

        }
        return serviceOk(true);
    }

    public function show($params)
    {

        $post = post::where('id', $params['id'])->when(isset($params['user_id']), function ($query) use ($params) {
            $query->where('user_id', $params['user_id']);
        })
            ->with(['images'])
            ->first();

        $categories = DB::table('blog_post_category')->select('category_id')->where('post_id', $params['id'])->get();
        $categories_ids = array();
        foreach ($categories as $category) {
            $categories_ids[] = $category->category_id;
        }

        return serviceOk(['post' => $post, 'categories_ids' => $categories_ids]);

    }

    public function update($params, $request)
    {

        $data = [
            'title' => $params['title'],
            'content' => $params['content']];

        $rules = ['title' => 'required', 'content' => 'required'];

        $valid = Validator::make($data, $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput();
        }

        $post = post::where('id', $params['id'])->when(isset($params['user_id']), function ($query) use ($params) {
            $query->where('user_id', $params['user_id']);
        })
            ->first();

        $post->status = $params['status'];
        $post->title = $params['title'];
        $post->url = str_replace(' ', '-', $params['title']);
        $post->content = $params['content'];
        $post->excerpt = str_limit(strip_tags($post->content, 50));
        $post->is_comment = $params['is_comment'];

        if (isset($params['delete_image'])) {
            delete_file($post->image);
            $post->image = ' ';
        }

        if (isset($params['filepath'])) {
            $post->image = parse_url($params['filepath'], PHP_URL_PATH);
        } elseif ($request->hasFile('image') || $request->filled('image')) {
            $is_upload = upload_file($request->file('image'), $post->image, $post->id, 'uploads/blog/post', $request->image);
            if ($is_upload) {
                ///Delete previous image
                if ($post->image) {
                    $image_url = $post->image;
                    $image_url = parse_url($image_url);
                    if (isset($image_url['path'])) {
                        $image_url = public_path($image_url['path']);

                        File::delete($image_url);
                    }
                }

                ///
                $post->image = $is_upload;

            } else {
                return serviceError('Image Invalid');
            }

        }

        if ($request->hasFile('video')) {
            $is_upload = upload_file($request->file('video'), null, $post->id, 'uploads/blog/post');
            if ($is_upload) {
                ///Delete previous video
                if ($post->video) {
                    $video_url = $post->video;
                    $video_url = parse_url($video_url);
                    if (isset($video_url['path'])) {
                        $video_url = public_path($video_url['path']);

                        File::delete($video_url);
                    }
                }

                ///
                $post->video = $is_upload;

            } else {
                return serviceError('Video Invalid');
            }

        }

        if (isset($params['delete_image'])) {
            delete_file($post->image);
            $post->image = ' ';
        }

        if (isset($params['delete_images']) && is_array($params['delete_images'])) {
            $blog_images = Image::where([
                "parent_id" => $post->id,
                "parent_type" => get_class($post),
            ])
                ->whereIn('id', $params['delete_images'])
                ->get();

            foreach ($blog_images as $blog_image) {
                $image_url = $blog_image;
                $image_url = parse_url($image_url);
                if (isset($image_url['path'])) {
                    $image_url = public_path($image_url['path']);

                    File::delete($image_url);
                }
                $blog_image->delete();
            }
        }

        if ($request->images && is_array($request->images)) {

            $blog_images = Image::where([
                "parent_id" => $post->id,
                "parent_type" => get_class($post),
            ])
                ->get();

            foreach ($blog_images as $blog_image) {
                $image_url = $blog_image;
                $image_url = parse_url($image_url);
                if (isset($image_url['path'])) {
                    $image_url = public_path($image_url['path']);

                    File::delete($image_url);
                }
                $blog_image->delete();
            }

            foreach ($request->images as $image) {

                $is_upload = upload_file($image, null, $post->id, 'uploads/blog/post', $image);
                if ($is_upload) {

                    $blog_image = new Image;
                    $blog_image->user_id = $params['user_id'];
                    $blog_image->user_type = $params['user_table'];
                    $blog_image->parent_id = $post->id;
                    $blog_image->parent_type = get_class($post);
                    $blog_image->url = $is_upload;
                    $blog_image->save();
                } else {
                    return serviceError('Image Invalid');
                }
            }

        } else {
            return serviceOk(var_dump($request->images));
        }

        if (isset($params['created_at'])) {
            $created_at = explode('/', $params['created_at']);
            $created_at_g = \Morilog\Jalali\CalendarUtils::toGregorian($created_at[0], $created_at[1], $created_at[2]);
            $created_at_g = Carbon::createFromDate($created_at_g[0], $created_at_g[1], $created_at_g[2]);
            $post->created_at = $created_at_g;
        }

        $post->save();
        if (isset($params['categories'])) {
            $categories = $params['categories'];
            if (!is_array($categories)) {
                $categories = [$params['categories']];
            }
            DB::table('blog_post_category')->where('post_id', $params['id'])->delete();
            if ($categories != null) {
                foreach ($categories as $category) {
                    DB::table('blog_post_category')->insert([
                        'post_id' => $params['id'],
                        'category_id' => $category]);
                }
            }
        }

        return serviceOk(true);
    }

    public function delete($params)
    {
        if (isset($params['ids'])) {
            $params['ids'] = $params['ids'];
        } else {
            $params['ids'] = array($params['id']);
        }

        foreach ($params['ids'] as $params['id']) {

            $post = post::where('id', $params['id'])->when(isset($params['user_id']), function ($query) use ($params) {
                $query->where('user_id', $params['user_id']);
            })
                ->first();
            if ($post) {
                $post->delete();
                $delete = DB::table('blog_post_category')->where('post_id', $params['id'])->delete();
            } else {
                return serviceError(trans('blog::messages.not_found'));
            }

        }

        return serviceOk(true);

    }

}
