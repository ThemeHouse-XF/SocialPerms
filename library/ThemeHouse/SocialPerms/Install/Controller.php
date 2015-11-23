<?php

/**
 * Installer for [⌂] Social Permissions.
 */
class ThemeHouse_SocialPerms_Install_Controller extends ThemeHouse_Install
{

    protected $_resourceManagerUrl = 'https://xenforo.com/community/resources/social-permissions.1878/';

    /**
     *
     * @see ThemeHouse_Install::_getPrerequisites()
     */
    protected function _getPrerequisites()
    {
        return array(
            'ThemeHouse_SocialGroups' => '1370009392'
        );
    } /* END _getPrerequisites */

    /**
     * Gets the tables (with fields) to be created for this add-on.
     * See parent for explanation.
     *
     * @return array Format: [table name] => fields
     */
    protected function _getTables()
    {
        return array(
            'xf_social_permission_set_th' => array(
                'social_permission_set_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', /* END 'social_permission_set_id' */
                'creator_permissions' => 'TEXT', /* 'creator_permissions' */
                'awaiting_permissions' => 'TEXT', /* 'awaiting_permissions' */
                'invited_permissions' => 'TEXT', /* 'invited_permissions' */
                'guest_permissions' => 'TEXT', /* 'guest_permissions' */
                'member_permissions' => 'TEXT', /* 'member_permissions' */
                'moderator_permissions' => 'TEXT', /* 'moderator_permissions' */
            ), /* END 'xf_social_permission_set_th' */
        );
    } /* END _getTables */

    /**
     *
     * @see ThemeHouse_Install::_getTableChanges()
     */
    protected function _getTableChanges()
    {
        return array(
            'xf_social_forum' => array(
                'guest_permissions' => 'TEXT', /* 'guest_permissions' */
                'member_permissions' => 'TEXT', /* 'member_permissions' */
                'moderator_permissions' => 'TEXT', /* 'moderator_permissions' */
                'social_permission_set_id_th' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0' /* 'social_permission_set_ids_th' */
            ), /* 'xf_social_forum' */
            'xf_forum' => array(
                'member_permissions' => 'TEXT', /* 'member_permissions' */
                'moderator_permissions' => 'TEXT', /* 'moderator_permissions' */
                'creator_permissions' => 'TEXT', /* 'creator_permissions' */
            ), /* 'xf_forum' */
        );
    } /* END _getTableChanges */

    /**
     *
     * @see ThemeHouse_Install::_getPermissionEntries()
     */
    protected function _getPermissionEntries()
    {
        return array(
            'forum' => array(
                'manageSocialPermissions' => array(
                    'permission_group_id' => 'forum', /* 'permission_group_id' */
                    'permission_id' => 'editSocialForum', /* 'permission_id' */
                ), /* 'manageSocialPermissions' */
            ), /* 'forum' */
        );
    } /* END _getPermissionEntries */

    protected function _postInstall()
    {
        $addOn = $this->getModelFromCache('XenForo_Model_AddOn')->getAddOnById('Waindigo_SocialPerms');

        if ($addOn) {
            $db = XenForo_Application::getDb();

            $db->query("
				INSERT INTO xf_social_permission_set_th (social_permission_set_id, creator_permissions, awaiting_permissions, invited_permissions, guest_permissions, member_permissions, moderator_permissions)
					SELECT social_permission_set_id, creator_permissions, awaiting_permissions, invited_permissions, guest_permissions, member_permissions, moderator_permissions
					FROM xf_social_permission_set_waindigo");
        }
    }
}