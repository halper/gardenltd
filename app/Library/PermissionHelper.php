<?php
/**
 * Created by PhpStorm.
 * User: alper
 * Date: 6/2/16
 * Time: 3:19 PM
 */

namespace App\Library {
    class PermissionHelper
    {
        const POST_PERMISSION = 2;
        public static function checkUserPostPermissionOnModule($user, $module)
        {
            return $user->hasPermissionOnModule(self::POST_PERMISSION, $module->id) ||
            $user->isAdmin() ||
            self::checkUsersGroupPostPermissionOnModule($user, $module);
        }

        public static function checkUsersGroupPostPermissionOnModule($user, $module)
        {
            $permission = false;
            foreach ($user->group()->get() as $user_group) {
                if ($user_group->hasPermissionOnModule(self::POST_PERMISSION, $module->id)) {
                    $permission = true;
                    break;
                }
            }
            return $permission;
        }

    }
}