<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\PermissionService;
use App\Utils\Code;
use App\Utils\Result;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{

    public $permissionService = null;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function getMenu(Request $request)
    {
        $data = $this->permissionService->getMenu($request->input('user_id'));
        return Result::getRes(Code::SUCCESS, $data);
    }
}
