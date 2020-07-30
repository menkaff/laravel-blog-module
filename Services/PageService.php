<?php
namespace Modules\Blog\Services;

use App\Rules\PersianYear;
use Carbon\Carbon;
use Modules\Blog\Models\Page;
use Validator;

class PageService
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

        $pages = Page::
            when(isset($params['word']), function ($query) use ($params) {
            $query->where('title', 'LIKE', '%' . $params['word'] . '%');
            $query->orwhere('content', 'LIKE', '%' . $params['word'] . '%');
        })
            ->when(isset($params['from_date']), function ($q) use ($params) {
                $from_date = explode('/', $params['from_date']);
                $from_date_g = \Morilog\Jalali\CalendarUtils::toGregorian($from_date[0], $from_date[1], $from_date[2]);
                $from_date_g = Carbon::createFromDate($from_date_g[0], $from_date_g[1], $from_date_g[2], 'Asia/Tehran')->format('Y-m-d');

                return $q->where('blog_page.created_at', '>=', $from_date_g);
            })
            ->when(isset($params['to_date']), function ($q) use ($params) {
                $to_date = explode('/', $params['to_date']);
                $to_date_g = \Morilog\Jalali\CalendarUtils::toGregorian($to_date[0], $to_date[1], $to_date[2]);
                $to_date_g = Carbon::createFromDate($to_date_g[0], $to_date_g[1], $to_date_g[2], 'Asia/Tehran')->format('Y-m-d');

                return $q->where('blog_page.created_at', '<=', $to_date_g);
            })
            ->when(isset($params['user_id']), function ($query) use ($params) {
                if (is_array($params['user_id'])) {
                    $query->whereIn('blog_page.user_id', $params['user_id']);
                } else {
                    $query->where('blog_page.user_id', '=', $params['user_id']);
                }
            })
            ->when(isset($params['user_table']), function ($query) use ($params) {
                $query->where('blog_page.user_table', $params['user_table']);
            })
            ->when(isset($params['user_table']) && isset($params['include_user']), function ($query) use ($params) {
                $query->join($params['user_table'], $params['user_table'] . '.id', 'blog_page.user_id');
                $query->where('blog_page.user_table', $params['user_table']);

                $query->addSelect([
                    $params['user_table'] . '.id as user.id',
                    $params['user_table'] . '.name as user.name',
                ]);
            })
            ->when(isset($params['order_by']), function ($query) use ($params) {
                $query->orderBy($params['order_by']['key'], $params['order_by']['value']);
            })
            ->when(isset($params['page']) && isset($params['per_page']), function ($query) use ($params) {
                return $query->skip($params['page'] * $params['per_page'])->take($params['per_page']);
            })
            ->addSelect([
                "blog_page.id",
                "blog_page.title",
                "blog_page.url",
                "blog_page.user_id",
                "blog_page.user_table",
                "blog_page.content",
                "blog_page.excerpt",
                "blog_page.image",
                "blog_page.video",
                "blog_page.status",
                "blog_page.created_at",
                "blog_page.updated_at",
            ])
            ->get();

        return serviceOk($pages);

    }

    public function store($params, $request)
    {
        $page = new Page;

        $data = [
            'title' => $params['title'],
            'content' => $params['content']];

        $rules = ['title' => 'required', 'content' => 'required'];

        $valid = Validator::make($data, $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput();
        }
        if (isset($params['status'])) {
            $page->status = $params['status'];
        }

        $page->title = $params['title'];
        $page->url = str_replace(' ', '-', $params['title']);
        $page->user_id = $params['user_id'];
        $page->user_table = $params['user_table'];
        $page->content = $params['content'];
        $page->excerpt = str_limit(strip_tags($params['content'], 50));

        if (isset($params['filepath'])) {
            $page->image = parse_url($params['filepath'], PHP_URL_PATH);
        } elseif ($request->hasFile('image')) {
            $is_upload = upload_file($request->file('image'), null, $page->id, 'uploads/blog/post');
            if ($is_upload) {
                $page->image = $is_upload;
            } else {
                return serviceError('Image Invalid');
            }

        }

        if ($request->hasFile('video')) {
            $is_upload = upload_file($request->file('video'), null, $page->id, 'uploads/blog/post');
            if ($is_upload) {
                $page->video = $is_upload;
            } else {
                return serviceError('Video Invalid');
            }

        }

        if (isset($params['created_at'])) {
            $created_at = explode('/', $params['created_at']);
            $created_at_g = \Morilog\Jalali\CalendarUtils::toGregorian($created_at[0], $created_at[1], $created_at[2]);
            $created_at_g = Carbon::createFromDate($created_at_g[0], $created_at_g[1], $created_at_g[2]);
            $page->created_at = $created_at_g;
        }

        $page->save();

        return serviceOk($page);
    }

    public function show($params)
    {

        $page = Page::where('id', $params['id'])->when(isset($params['user_id']), function ($query) use ($params) {
            $query->where('user_id', $params['user_id']);
        })
            ->first();

        return serviceOk(['post' => $page]);

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

        $page = Page::where('id', $params['id'])->when(isset($params['user_id']), function ($query) use ($params) {
            $query->where('user_id', $params['user_id']);
        })
            ->first();

        $page->status = $params['status'];
        $page->title = $params['title'];
        $page->url = str_replace(' ', '-', $params['title']);
        $page->content = $params['content'];
        $page->excerpt = str_limit(strip_tags($page->content, 50));

        if (isset($params['filepath'])) {
            $page->image = parse_url($params['filepath'], PHP_URL_PATH);
        } elseif ($request->hasFile('image')) {
            $is_upload = upload_file($request->file('image'), null, $page->id, 'uploads/blog/post');
            if ($is_upload) {
                ///Delete previous image
                if ($page->image) {
                    $image_url = $page->image;
                    $image_url = parse_url($image_url);
                    if (isset($image_url['path'])) {
                        $image_url = public_path($image_url['path']);

                        File::delete($image_url);
                    }
                }

                ///
                $page->image = $is_upload;

            } else {
                return serviceError('Image Invalid');
            }

        }

        if ($request->hasFile('video')) {
            $is_upload = upload_file($request->file('video'), null, $page->id, 'uploads/blog/post');
            if ($is_upload) {
                ///Delete previous video
                if ($page->video) {
                    $video_url = $page->video;
                    $video_url = parse_url($video_url);
                    if (isset($video_url['path'])) {
                        $video_url = public_path($video_url['path']);

                        File::delete($video_url);
                    }
                }

                ///
                $page->video = $is_upload;

            } else {
                return serviceError('Video Invalid');
            }

        }

        if (isset($params['created_at'])) {
            $created_at = explode('/', $params['created_at']);
            $created_at_g = \Morilog\Jalali\CalendarUtils::toGregorian($created_at[0], $created_at[1], $created_at[2]);
            $created_at_g = Carbon::createFromDate($created_at_g[0], $created_at_g[1], $created_at_g[2]);
            $page->created_at = $created_at_g;
        }

        $page->save();

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

            $page = Page::where('id', $params['id'])->when(isset($params['user_id']), function ($query) use ($params) {
                $query->where('user_id', $params['user_id']);
            })
                ->first();
            if ($page) {
                $page->delete();
            } else {
                return serviceError(trans('blog::messages.not_found'));
            }

        }

        return serviceOk(true);

    }
}
