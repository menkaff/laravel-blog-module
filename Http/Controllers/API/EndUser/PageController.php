<?php
namespace Modules\Blog\Http\Controllers\API\EndUSer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

}
