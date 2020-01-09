<?php

namespace App\Services\Admin;

use App\Models\AdminUserModel;
use App\Models\GroupModel;
use App\Models\GroupUserModel;
use App\Models\PermissionModel;

class PermissionService
{

    protected $permissionModel = null;

    protected $groupUserModel = null;

    protected $groupModel = null;

    public function __construct(PermissionModel $permissionModel, GroupUserModel $groupUserModel, GroupModel $groupModel)
    {
        $this->permissionModel = $permissionModel;
        $this->groupUserModel = $groupUserModel;
        $this->groupModel = $groupModel;
    }

    /**
     * 获取权限
     * @param $userId
     * @return array
     */
    public function getPermission($userId)
    {
        return $this->groupUserModel
            ->selectRaw('t4.*')
            ->from('group_user as t1')
            ->join('group as t2', 't1.group_id', '=', 't2.id')
            ->join('group_permission as t3', 't2.id', '=', 't3.group_id')
            ->join('permission as t4', 't3.permission_id', '=', 't4.id')
            ->where('t1.admin_user_id', $userId)
            ->where('t4.is_menu', 0)
            ->get()->toArray();
    }

    /**
     * @param $userId
     * @return array
     */
    public function getMenu($userId)
    {
        return $this->groupUserModel
            ->selectRaw('t4.*')
            ->from('group_user as t1')
            ->join('group as t2', 't1.group_id', '=', 't2.id')
            ->join('group_permission as t3', 't2.id', '=', 't3.group_id')
            ->join('permission as t4', 't3.permission_id', '=', 't4.id')
            ->where('t1.admin_user_id', $userId)
            ->where('t4.is_menu', 1)
            ->get()->toArray();
    }
}