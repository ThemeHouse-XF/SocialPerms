<?php

/**
 *
 * @see ThemeHouse_SocialGroups_Model_SocialForum
 */
class ThemeHouse_SocialPerms_Extend_ThemeHouse_SocialGroups_Model_SocialForum extends XFCP_ThemeHouse_SocialPerms_Extend_ThemeHouse_SocialGroups_Model_SocialForum
{

    const FETCH_SOCIAL_PERMISSIONS = 0x1000;

    /**
     *
     * @see ThemeHouse_SocialGroups_Model_SocialForum::getCurrentSocialForumById()
     */
    public function getCurrentSocialForumById($socialForumId, array $fetchOptions = array())
    {
        $this->addFetchOptionJoin($fetchOptions, self::FETCH_SOCIAL_PERMISSIONS);

        return parent::getCurrentSocialForumById($socialForumId, $fetchOptions);
    } /* END getCurrentSocialForumById */

    /**
     *
     * @see ThemeHouse_SocialGroups_Model_SocialForum::prepareSocialForumFetchOptions()
     */
    public function prepareSocialForumFetchOptions(array $fetchOptions)
    {
        $socialForumFetchOptions = parent::prepareSocialForumFetchOptions($fetchOptions);

        $db = $this->_getDb();

        $selectFields = $socialForumFetchOptions['selectFields'];
        $joinTables = $socialForumFetchOptions['joinTables'];
        $orderClause = $socialForumFetchOptions['orderClause'];

        if (!empty($fetchOptions['join'])) {
            if ($fetchOptions['join'] & self::FETCH_SOCIAL_PERMISSIONS) {
                $selectFields .= ',
                    social_permission_set_th.creator_permissions AS social_creator_permissions,
                    social_permission_set_th.awaiting_permissions AS social_awaiting_permissions,
                    social_permission_set_th.invited_permissions AS social_invited_permissions,
                    social_permission_set_th.guest_permissions AS social_guest_permissions,
                    social_permission_set_th.member_permissions AS social_member_permissions,
                    social_permission_set_th.moderator_permissions AS social_moderator_permissions,
                    social_category_permissions.member_permissions AS category_member_permissions,
					social_category_permissions.moderator_permissions AS category_moderator_permissions,
                    social_category_permissions.creator_permissions AS category_creator_permissions';
                $joinTables .= '
					LEFT JOIN xf_social_permission_set_th AS social_permission_set_th ON
						(social_permission_set_th.social_permission_set_id = social_forum.social_permission_set_id_th)
                    INNER JOIN xf_forum AS social_category_permissions ON
						(social_category_permissions.node_id = social_forum.node_id)';
            }
        }

        return array(
            'selectFields' => $selectFields,
            'joinTables' => $joinTables,
            'orderClause' => $orderClause
        );
    } /* END prepareSocialForumFetchOptions */

    /**
     *
     * @see ThemeHouse_SocialGroups_Model_SocialForum::getSocialForumPermissions()
     */
    public function getSocialForumPermissions(array $user, $permissions)
    {
        if (!isset($user['guest_permissions']) || !isset($user['member_permissions']) ||
             !isset($user['moderator_permissions'])) {
            if (ThemeHouse_SocialGroups_SocialForum::hasInstance()) {
                $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance()->toArray();
            } else {
                $socialForum = array();
            }
        } else {
            $socialForum = $user;
        }

        $isMember = isset($user['is_approved']) && isset($user['is_invited']) && !empty($user) && $user['is_approved'] &&
             !$user['is_invited'];

        // add per-node permissions
        $categoryPermissions = array();
        if ($isMember) {
            if (!empty($user['is_social_forum_creator']) && !empty($socialForum['category_creator_permissions'])) {
                $categoryPermissions = unserialize($socialForum['category_creator_permissions']);
            } elseif (!empty($user['is_social_forum_moderator']) && !empty($socialForum['category_moderator_permissions'])) {
                $categoryPermissions = unserialize($socialForum['category_moderator_permissions']);
            } elseif (!empty($socialForum['category_member_permissions'])) {
                $categoryPermissions = unserialize($socialForum['category_member_permissions']);
            }
        }
        foreach ($categoryPermissions as $categoryPermission => $permissionValue) {
            if ($permissionValue) {
                $permissions[$categoryPermission] = 1;
            }
        }

        // add pre-set permissions
        $setPermissions = array();
        if ($isMember) {
            if (!empty($user['is_social_forum_creator']) && !empty($socialForum['social_creator_permissions'])) {
                $setPermissions = unserialize($socialForum['social_creator_permissions']);
            } elseif (!empty($user['is_social_forum_moderator']) && !empty($socialForum['social_moderator_permissions'])) {
                $setPermissions = unserialize($socialForum['social_moderator_permissions']);
            } elseif (!empty($socialForum['social_member_permissions'])) {
                $setPermissions = unserialize($socialForum['social_member_permissions']);
            }
        } elseif (!empty($user['is_invited']) && !empty($socialForum['social_invited_permissions'])) {
            $setPermissions = unserialize($socialForum['social_invited_permissions']);
        } elseif (!empty($user['social_forum_member_id']) && !empty($socialForum['social_awaiting_permissions'])) {
            $setPermissions = unserialize($socialForum['social_awaiting_permissions']);
        } elseif (!empty($socialForum['social_guest_permissions'])) {
            $setPermissions = unserialize($socialForum['social_guest_permissions']);
        }
        foreach ($setPermissions as $setPermission => $permissionValue) {
            if ($permissionValue) {
                $permissions[$setPermission] = 1;
            }
        }

        // add per-social group permissions
        $groupPermissions = array();
        if ($isMember) {
            if (!empty($user['is_social_forum_moderator']) && !empty($socialForum['moderator_permissions'])) {
                $groupPermissions = unserialize($socialForum['moderator_permissions']);
            } elseif (!empty($socialForum['member_permissions'])) {
                $groupPermissions = unserialize($socialForum['member_permissions']);
            }
        } elseif (!empty($socialForum['guest_permissions'])) {
            $groupPermissions = unserialize($socialForum['guest_permissions']);
        }
        foreach ($groupPermissions as $groupPermission => $permissionValue) {
            if ($permissionValue) {
                $permissions[$groupPermission] = 1;
            }
        }

        return parent::getSocialForumPermissions($user, $permissions);
    } /* END getSocialForumPermissions */

    /**
     * Determines if the specified user can manage social permissions.
     *
     * @param array $socialForum
     * @param string $errorPhraseKey By ref. More specific error, if available.
     * @param array|null $viewingUser Viewing user reference
     *
     * @return boolean
     */
    public function canManageSocialPermissions(array $socialForum, array $socialCategory, &$errorPhraseKey = '', array $nodePermissions = null,
        array $viewingUser = null)
    {
        $this->standardizeViewingUserReferenceForNode($socialCategory['node_id'], $viewingUser, $nodePermissions);

        return XenForo_Permission::hasContentPermission($nodePermissions, 'manageSocialPermissions');
    } /* END canManageSocialPermissions */
}