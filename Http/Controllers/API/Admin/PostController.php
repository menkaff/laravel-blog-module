<?php

namespace Modules\Blog\Http\Controllers\API\Admin;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Services\PostService;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $post_service = new PostService;
        $result = $post_service->index($request->all());
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }
    }

    public function show(Request $request)
    {
        $post_service = new PostService;
        $result = $post_service->show($request->all());
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }

    }

    public function store(Request $request)
    {
        $post_service = new PostService;

        $data = $request->all();
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['user_table'] = $user->getTable();

        $result = $post_service->store($data, $request);
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }
    }

    public function update(Request $request)
    {
        $post_service = new PostService;

        $data = $request->all();
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['user_table'] = $user->getTable();

        $result = $post_service->update($data, $request);
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }
    }

    public function delete(Request $request)
    {
        $post_service = new PostService;
        $result = $post_service->delete($request->all());
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }
    }

}
