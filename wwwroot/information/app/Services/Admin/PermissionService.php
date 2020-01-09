<?php

namespace App\Services\Admin;

use App\Models\AdminUserModel;
use App\Models\GroupModel;
use App\Models\GroupUserModel;
use App\Models\MenuModel;
use App\Models\PermissionModel;

class PermissionService
{

    protected $adminUserModel = null;

    protected $permissionModel = null;

    protected $groupUserModel = null;

    protected $groupModel = null;

    protected $menuModel = null;

    public function __construct(AdminUserModel $adminUserModel, PermissionModel $permissionModel, GroupUserModel $groupUserModel, GroupModel $groupModel, MenuModel $menuModel)
    {
        $this->adminUserModel = $adminUserModel;
        $this->permissionModel = $permissionModel;
        $this->groupUserModel = $groupUserModel;
        $this->groupModel = $groupModel;
        $this->menuModel = $menuModel;
    }
}