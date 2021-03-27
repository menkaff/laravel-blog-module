<?php
namespace Modules\Blog\Http\Controllers\API\Editor;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Models\User;
use Modules\Blog\Services\PageService;

class PageController extends Controller
{

    public function index(Request $request)
    {
        $page_service = new PageService;
        $result = $page_service->index($request->all());
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }
    }

    public function show(Request $request)
    {
        $page_service = new PageService;
        $result = $page_service->show($request->all());
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }

    }

    public function store(Request $request)
    {
        $page_service = new PageService;

        $data = $request->all();
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['user_table'] = $user->getTable();

        $result = $page_service->store($data, $request);
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }
    }

    public function update(Request $request)
    {
        $page_service = new PageService;

        $data = $request->all();
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['user_table'] = $user->getTable();

        $result = $page_service->update($data, $request);
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }
    }

    public function delete(Request $request)
    {
        $page_service = new PageService;
        $result = $page_service->delete($request->all());
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }
    }

}
