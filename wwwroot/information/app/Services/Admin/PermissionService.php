<?php

namespace App\Services\Admin;

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
        $permission = $this->groupUserModel
            ->selectRaw('t4.*')
            ->from('group_user as t1')
            ->join('group as t2', 't1.group_id', '=', 't2.id')
            ->join('group_permission as t3', 't2.id', '=', 't3.group_id')
            ->join('permission as t4', 't3.permission_id', '=', 't4.id')
            ->where('t1.admin_user_id', $userId)
            ->where('t4.is_menu', 0);
        $publicPermission = $this->permissionModel->where('is_public', 1);
        return $permission->unionAll($publicPermission)->get()->toArray();
    }

    /**
     * @param $userId
     * @return array
     */
    public function getMenu($userId)
    {
        $data = $this->groupUserModel
            ->selectRaw('t4.*')
            ->from('group_user as t1')
            ->join('group as t2', 't1.group_id', '=', 't2.id')
            ->join('group_permission as t3', 't2.id', '=', 't3.group_id')
            ->join('permission as t4', 't3.permission_id', '=', 't4.id')
            ->where('t1.admin_user_id', $userId)
            ->where('t4.is_menu', 1)
            ->get()->toArray();
        if (empty($data)) {
            return [];
        }
        return $this->tree($data);
    }

    /**
     * 菜单整理成树状
     * @param $data
     * @return array
     */
    public function tree($data)
    {
        $newData = [];
        foreach($data as $key => $value) {
            $newData[$value['id']] = $value;
        }
        foreach($newData as $k => $v) {
            if ($v['level'] == 2) {
                $newData[$v['pid']]['child'][] = $v;
                unset($newData[$k]);
            }
        }
        return array_merge($newData);
    }
}