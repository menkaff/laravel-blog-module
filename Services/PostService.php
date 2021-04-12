<?php

namespace Modules\Blog\Services;

use App\Rules\PersianYear;
use Carbon\Carbon;
use DB;
use File;
use Modules\Blog\Models\Image;
use Modules\Blog\Models\Post;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostService
{
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

        $posts = Post::when(isset($params['word']), function ($query) use ($params) {
            $query->where('title', 'LIKE', '%' . $params['word'] . '%');
            $query->orwhere('content', 'LIKE', '%' . $params['word'] . '%');
        })
            ->when(isset($params['from_date']), function ($query) use ($params) {
                $from_date = explode('/', $params['from_date']);
                $from_date_g = \Morilog\Jalali\CalendarUtils::toGregorian($from_date[0], $from_date[1], $from_date[2]);
                $from_date_g = Carbon::createFromDate($from_date_g[0], $from_date_g[1], $from_date_g[2], 'Asia/Tehran')->format('Y-m-d');

                $query->where('blog_post.created_at', '>=', $from_date_g);
            })
            ->when(isset($params['to_date']), function ($query) use ($params) {
                $to_date = explode('/', $params['to_date']);
                $to_date_g = \Morilog\Jalali\CalendarUtils::toGregorian($to_date[0], $to_date[1], $to_date[2]);
                $to_date_g = Carbon::createFromDate($to_date_g[0], $to_date_g[1], $to_date_g[2], 'Asia/Tehran')->format('Y-m-d');

                $query->where('blog_post.created_at', '<=', $to_date_g);
            })
            ->when(isset($params['userable_id']), function ($query) use ($params) {
                if (is_array($params['userable_id'])) {
                    $query->whereIn('blog_post.userable_id', $params['userable_id']);
                } else {
                    $query->where('blog_post.userable_id', '=', $params['userable_id']);
                }
            })
            ->when(isset($params['userable_type']), function ($query) use ($params) {
                $query->where('blog_post.userable_type', $params['userable_type']);
            })
            ->when(isset($params['userable_type']) && isset($params['include_userable']), function ($query) use ($params) {
                $query->join($params['userable_type'], $params['userable_type'] . '.id', 'blog_post.userable_id');
                $query->where('blog_post.userable_type', $params['userable_type']);

                $query->addSelect([
                    $params['userable_type'] . '.id as userable.id',
                    $params['userable_type'] . '.name as userable.name',
                ]);
            })
            ->when(isset($params['category_ids']), function ($query) use ($params) {

                $query->join('blog_post_category', 'blog_post_category.post_id', 'blog_post.id');
                $query->whereIn('blog_post_category.category_id', $params['category_ids']);
            })
            ->when(isset($params['order_by']), function ($query) use ($params) {
                $query->orderBy($params['order_by']['key'], $params['order_by']['value']);
            })
            ->when(isset($params['page']) && isset($params['per_page']), function ($query) use ($params) {
                $query->skip($params['page'] * $params['per_page'])->take($params['per_page']);
            })
            ->with(['images'])
            ->addSelect([
                "blog_post.id",
                "blog_post.title",
                "blog_post.url",
                "blog_post.userable_id",
                "blog_post.userable_type",
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

        $validator = Validator::make($params, [
            'title' => 'required',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return serviceError($validator->errors());
        }

        if (isset($params['status'])) {
            $post->status = $params['status'];
        }

        $post->title = $params['title'];
        $post->url = str_replace(' ', '-', $params['title']);
        $post->userable_id = $params['userable_id'];
        $post->userable_type = $params['userable_type'];
        $post->content = $params['content'];
        $post->excerpt = Str::limit(strip_tags($params['content'], 50));

        if (isset($params['is_comment'])) {
            $post->is_comment = $params['is_comment'];
        }

        if (isset($params['filepath'])) {
            $post->image = parse_url($params['filepath'], PHP_URL_PATH);
        } elseif ($request->hasFile('image') || $request->filled('image')) {
            $is_upload = upload_file($request->file('image'), null, $post->id, 'public/uploads/blog/post', $request->image);
            if ($is_upload) {
                $post->image = $is_upload;
            } else {
                return serviceError('Image Invalid');
            }
        }

        if ($request->hasFile('video')) {
            $is_upload = upload_file($request->file('video'), null, $post->id, 'public/uploads/blog/post');
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

        if ($request->filled("images")) {

            $images = [];
            if (is_array($request->images)) {
                $images = $request->images;
            } else {
                $images = json_decode($request->images);
            }

            foreach ($images as $image) {

                $is_upload = upload_file($image, null, $post->id, 'public/uploads/blog/post', $image);
                if ($is_upload) {

                    $blog_image = new Image;
                    $blog_image->url = $is_upload;
                    $post->images()->save($blog_image);
                } else {
                    return serviceError('Image Invalid');
                }
            }
        }

        if (isset($params['categories'])) {
            $categories = [];
            if (is_array($request->categories)) {
                $categories = $request->categories;
            } else {
                $categories = json_decode($request->categories);
            }

            foreach ($categories as $category) {
                DB::table('blog_post_category')->insert([
                    'post_id' => $post->id,
                    'category_id' => $category
                ]);
            }
        }
        $post->images;
        $post->categories;
        return serviceOk($post);
    }

    public function show($params)
    {

        $post = post::where('id', $params['id'])->when(isset($params['userable_id']), function ($query) use ($params) {
            $query->where('userable_id', $params['userable_id']);
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

    public function updateStatus($params)
    {
        $validator = Validator::make($params, [
            'id' => ['required', 'exists:blog_post,id'],
            'status' => ['required', Rule::in(Post::getStatuses())]
        ]);

        if ($validator->fails()) {
            return serviceError($validator->errors());
        }

        $post = post::where('id', $params['id'])->when(isset($params['userable_id']), function ($query) use ($params) {
            $query->where('userable_id', $params['userable_id']);
        })
            ->first();


        if (!$post) {
            return serviceError(trans("blog::messages.403"));
        }

        $post->status = $params["status"];
        $post->save();

        return serviceOk($post);
    }

    public function update($params, $request)
    {

        $validator = Validator::make($params, [
            'id' => ['required', 'exists:blog_post,id'],
            'title' => 'required',
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            return serviceError($validator->errors());
        }

        $post = post::where('id', $params['id'])->when(isset($params['userable_id']), function ($query) use ($params) {
            $query->where('userable_id', $params['userable_id']);
        })
            ->first();


        if (!$post) {
            return serviceError(trans("blog::messages.403"));
        }

        $post->title = $params['title'];
        $post->url = str_replace(' ', '-', $params['title']);
        $post->content = $params['content'];
        $post->excerpt = Str::limit(strip_tags($post->content, 50));

        if (isset($params["status"]))
            $post->status = $params['status'];
        if (isset($params["is_comment"]))
            $post->is_comment = $params['is_comment'];

        if (isset($params['delete_image'])) {
            delete_file($post->image);
            $post->image = ' ';
        }

        if (isset($params['filepath'])) {
            $post->image = parse_url($params['filepath'], PHP_URL_PATH);
        } elseif ($request->hasFile('image') || $request->filled('image')) {
            $is_upload = upload_file($request->file('image'), $post->image, $post->id, 'public/uploads/blog/post', $request->image);
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
                $post->image = $is_upload;
            } else {
                return serviceError('Image Invalid');
            }
        }

        if ($request->hasFile('video')) {
            $is_upload = upload_file($request->file('video'), null, $post->id, 'public/uploads/blog/post');
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
                "userable_id" => $post->id,
                "userable_type" => get_class($post),
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

        if ($request->filled("images")) {

            $images = [];
            if (is_array($request->images)) {
                $images = $request->images;
            } else {
                $images = json_decode($request->images);
            }

            $remain_images = [];
            foreach ($images as $obj) {
                if (is_string($obj) && (filter_var($obj, FILTER_VALIDATE_URL) != false)) {
                    $remain_images[] = parse_url($obj, PHP_URL_PATH);
                }
            }

            $delete_images = Image::whereNotIn('url', $remain_images)
                ->where([
                    "imageable_id" => $post->id,
                    "imageable_type" => get_class($post),
                ])
                ->get();
            foreach ($delete_images as $delete_image) {
                delete_file($delete_image->url);
                $delete_image->delete();
            }

            foreach ($images as $obj) {
                if (is_string($obj) && (filter_var($obj, FILTER_VALIDATE_URL) === false)) {

                    $is_upload = upload_file($obj, null, $post->id, 'public/uploads/blog/post', $obj);
                    if ($is_upload) {

                        $blog_image = new Image;
                        $blog_image->url = $is_upload;
                        $post->images()->save($blog_image);
                    } else {

                        return serviceError('Image Invalid');
                    }
                }
            }
        }

        if (isset($params['created_at'])) {
            $created_at = explode('/', $params['created_at']);
            $created_at_g = \Morilog\Jalali\CalendarUtils::toGregorian($created_at[0], $created_at[1], $created_at[2]);
            $created_at_g = Carbon::createFromDate($created_at_g[0], $created_at_g[1], $created_at_g[2]);
            $post->created_at = $created_at_g;
        }

        $post->save();
        if (isset($params['categories'])) {
            $categories = [];
            if (is_array($request->categories)) {
                $categories = $request->categories;
            } else {
                $categories = json_decode($request->categories);
            }
            DB::table('blog_post_category')->where('post_id', $params['id'])->delete();
            if ($categories != null) {
                foreach ($categories as $category) {
                    DB::table('blog_post_category')->insert([
                        'post_id' => $params['id'],
                        'category_id' => $category
                    ]);
                }
            }
        }

        $post->images;
        $post->categories;

        return serviceOk($post);
    }

    public function delete($params)
    {
        if (isset($params['ids'])) {
            $params['ids'] = $params['ids'];
        } else {
            $params['ids'] = array($params['id']);
        }

        foreach ($params['ids'] as $params['id']) {

            $post = post::where('id', $params['id'])->when(isset($params['userable_id']), function ($query) use ($params) {
                $query->where('userable_id', $params['userable_id']);
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
