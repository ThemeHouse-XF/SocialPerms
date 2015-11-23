<?php

/**
 *
 * @see ThemeHouse_SocialGroups_ControllerAdmin_SocialCategory
 */
class ThemeHouse_SocialPerms_Extend_ThemeHouse_SocialGroups_ControllerAdmin_SocialCategory extends XFCP_ThemeHouse_SocialPerms_Extend_ThemeHouse_SocialGroups_ControllerAdmin_SocialCategory
{

    /**
     *
     * @see ThemeHouse_SocialGroups_ControllerAdmin_SocialCategory::actionEdit()
     */
    public function actionEdit()
    {
        $response = parent::actionEdit();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $response->params['forum']['memberPermissions'] = array();
            $response->params['forum']['moderatorPermissions'] = array();
            $response->params['forum']['creatorPermissions'] = array();
            if (isset($response->params['forum']['member_permissions']) && $response->params['forum']['member_permissions']) {
                $response->params['forum']['memberPermissions'] = unserialize($response->params['forum']['member_permissions']);
            }
            if (isset($response->params['forum']['moderator_permissions']) && $response->params['forum']['moderator_permissions']) {
                $response->params['forum']['moderatorPermissions'] = unserialize($response->params['forum']['moderator_permissions']);
            }
            if (isset($response->params['forum']['creator_permissions']) && $response->params['forum']['creator_permissions']) {
                $response->params['forum']['creatorPermissions'] = unserialize($response->params['forum']['creator_permissions']);
            }

            /* @var $permissionModel XenForo_Model_Permission */
            $permissionModel = $this->getModelFromCache('XenForo_Model_Permission');
            $permissions = $permissionModel->getPermissionByGroup('forum');
            foreach ($permissions as $permissionId => $permission) {
                if ($permissionId == "joinSocialForum" || $permissionId == "leaveSocialForum") {
                    unset($permissions[$permissionId]);
                }
            }
            $response->params['permissions'] = $permissionModel->preparePermissions($permissions);
        }

        return $response;
    } /* END actionEdit */

    /**
     *
     * @see XenForo_ControllerAdmin_Forum::actionSave()
     */
    public function actionSave()
    {
        $GLOBALS['ThemeHouse_SocialGroups_ControllerAdmin_SocialCategory'] = $this;

        return parent::actionSave();
    } /* END actionSave */
}