<?php

/**
 *
 * @see ThemeHouse_SocialGroups_ControllerPublic_SocialForum
 */
class ThemeHouse_SocialPerms_Extend_ThemeHouse_SocialGroups_ControllerPublic_SocialForum extends XFCP_ThemeHouse_SocialPerms_Extend_ThemeHouse_SocialGroups_ControllerPublic_SocialForum
{

    /**
     *
     * @see ThemeHouse_SocialGroups_ControllerPublic_SocialForum::actionIndex()
     */
    public function actionIndex()
    {
        $response = parent::actionIndex();

        return $this->_getSocialPermissionsResponse($response);
    } /* END actionIndex */

    /**
     *
     * @see ThemeHouse_SocialGroups_ControllerPublic_SocialForum::actionForum()
     */
    public function actionForum()
    {
        $response = parent::actionForum();

        return $this->_getSocialPermissionsResponse($response);
    } /* END actionForum */

    /**
     *
     * @param XenForo_ControllerResponse_Abstract $response
     * @return XenForo_ControllerResponse_Abstract
     */
    protected function _getSocialPermissionsResponse(XenForo_ControllerResponse_Abstract $response)
    {
        if ($this->_routeMatch->getResponseType() == 'rss') {
            return $response;
        }

        if ($response instanceof XenForo_ControllerResponse_View) {
            $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance()->toArray();

            $viewParams = array(
                'canManageSocialPermissions' => $this->_getForumModel()->canManageSocialPermissions($socialForum, $socialForum)
            );

            if ($response->subView) {
                $response->subView->params = array_merge($response->subView->params, $viewParams);
            }
            $response->params = array_merge($response->params, $viewParams);
        }

        return $response;
    } /* END _getSocialPermissionsResponse */

    /**
     *
     * @see ThemeHouse_SocialGroups_ControllerPublic_SocialForum::actionEdit()
     */
    public function actionEdit()
    {
        $response = parent::actionEdit();

        if ($response instanceof XenForo_ControllerResponse_View) {
            /* @var $socialPermissionSetModel ThemeHouse_SocialPerms_Model_SocialPermissionSet */
            $socialPermissionSetModel = $this->getModelFromCache('ThemeHouse_SocialPerms_Model_SocialPermissionSet');

            $socialPermissionSets = $socialPermissionSetModel->prepareSocialPermissionSets(
                $socialPermissionSetModel->getSocialPermissionSets());
            $response->params['socialPermissionSets'] = $socialPermissionSets;

            $socialForum = $response->params['socialForum'];

            $response->params['canManageSocialPermissions'] = $this->_getForumModel()->canManageSocialPermissions(
                $socialForum, $socialForum);
        }

        return $response;
    } /* END actionEdit */

    /**
     *
     * @see ThemeHouse_SocialGroups_ControllerPublic_SocialForum::actionSave()
     */
    public function actionSave()
    {
        $GLOBALS['ThemeHouse_SocialGroups_ControllerPublic_SocialForum'] = $this;

        return parent::actionSave();
    } /* END actionSave */

    public function actionPermissions()
    {
        if (ThemeHouse_SocialGroups_SocialForum::hasInstance()) {
            $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance()->toArray();
        } else {
            return $this->responseError('th_requested_social_forum_not_found_socialgroups');
        }

        $this->_assertCanManageSocialPermissions($socialForum);

        $userGroup = $this->_input->filterSingle('social_user_group', XenForo_Input::STRING);

        if (!$userGroup) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('social-forums/permissions/guest', $socialForum));
        }

        $guest = false;
        $selectedPermissions = array();
        if ($userGroup == 'moderator') {
            $selectedUserGroup = new XenForo_Phrase('th_moderator_socialgroups');
            if ($socialForum['moderator_permissions']) {
                $selectedPermissions = unserialize($socialForum['moderator_permissions']);
            }
            $userGroupId = 2;
        } elseif ($userGroup == 'member') {
            $selectedUserGroup = new XenForo_Phrase('th_member_socialgroups');
            if ($socialForum['member_permissions']) {
                $selectedPermissions = unserialize($socialForum['member_permissions']);
            }
            $userGroupId = 1;
        } else {
            $selectedUserGroup = new XenForo_Phrase('th_guest_socialgroups');
            $guest = true;
            if ($socialForum['guest_permissions']) {
                $selectedPermissions = unserialize($socialForum['guest_permissions']);
            }
            $userGroupId = 0;
        }

        $permissionModel = XenForo_Model::create('XenForo_Model_Permission');

        $preparedOption = $permissionModel->getSocialGroupsPreparedOption(array(), $guest);

        $permissions = array();
        foreach ($preparedOption['permissions'] as $permissionId => $permissionName) {
            if ($permissionId == 'joinSocialForum' && $guest) {
                $permissions[$permissionId] = array(
                    'value' => $permissionId,
                    'label' => $permissionName,
                    'checked' => $socialForum['social_forum_open'],
                    'disabled' => true
                );
            } elseif (isset(XenForo_Application::get('options')->th_socialGroups_permissions[3][$permissionId])) {
                $isChecked = array_key_exists($permissionId, $selectedPermissions) ||
                     isset(
                        XenForo_Application::get('options')->th_socialGroups_permissions[$userGroupId][$permissionId]);
                $permissions[$permissionId] = array(
                    'value' => $permissionId,
                    'label' => $permissionName,
                    'checked' => $isChecked,
                    'disabled' => isset(
                        XenForo_Application::get('options')->th_socialGroups_permissions[$userGroupId][$permissionId])
                );
            }
        }

        $viewParams = array(
            'socialForum' => $socialForum,
            'permissions' => $permissions,
            'selectedUserGroup' => $selectedUserGroup,
            'socialUserGroup' => $userGroup
        );

        $subView = $this->responseView('ThemeHouse_SocialPerms_ViewPublic_SocialForum_Permissions',
            'th_social_permissions_socialperms', $viewParams);
        return $this->_getWrapper($subView);
    } /* END actionPermissions */

    public function actionPermissionsGuest()
    {
        $this->_request->setParam('social_user_group', 'guest');
        return $this->responseReroute(__CLASS__, 'permissions');
    } /* END actionPermissionsGuest */

    public function actionPermissionsMember()
    {
        $this->_request->setParam('social_user_group', 'member');
        return $this->responseReroute(__CLASS__, 'permissions');
    } /* END actionPermissionsMember */

    public function actionPermissionsModerator()
    {
        $this->_request->setParam('social_user_group', 'moderator');
        return $this->responseReroute(__CLASS__, 'permissions');
    } /* END actionPermissionsModerator */

    public function actionPermissionsSave()
    {
        $this->_assertPostOnly();

        if (ThemeHouse_SocialGroups_SocialForum::hasInstance()) {
            $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance()->toArray();
        } else {
            return $this->responseError('th_requested_social_forum_not_found_socialgroups');
        }

        $this->_assertCanManageSocialPermissions($socialForum);

        $data = $this->_input->filter(
            array(
                'permissions' => XenForo_Input::ARRAY_SIMPLE,
                'social_user_group' => XenForo_Input::STRING
            ));

        foreach ($data['permissions'] as $permissionId => $permissionSet) {
            if (!isset(XenForo_Application::get('options')->th_socialGroups_permissions[3][$permissionId])) {
                unset($data['permissions'][$permissionId]);
            }
            if ($data['social_user_group'] == 'member' &&
                 isset(XenForo_Application::get('options')->th_socialGroups_permissions[1][$permissionId])) {
                unset($data['permissions'][$permissionId]);
            } elseif ($data['social_user_group'] == 'moderator' &&
                 isset(XenForo_Application::get('options')->th_socialGroups_permissions[2][$permissionId])) {
                unset($data['permissions'][$permissionId]);
            }
        }

        $dw = XenForo_DataWriter::create('ThemeHouse_SocialGroups_DataWriter_SocialForum');
        $dw->setExistingData($socialForum);
        if ($data['social_user_group'] == 'member') {
            $dw->set('member_permissions', $data['permissions']);
        } elseif ($data['social_user_group'] == 'moderator') {
            $dw->set('moderator_permissions', $data['permissions']);
        } else {
            $dw->set('guest_permissions', $data['permissions']);
        }
        $dw->save();

        if ($data['social_user_group'] == 'member') {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('social-forums/permissions/member', $socialForum));
        } elseif ($data['social_user_group'] == 'moderator') {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('social-forums/permissions/moderator', $socialForum));
        } else {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('social-forums/permissions/guest', $socialForum));
        }
    } /* END actionPermissionsSave */

    public function actionPermissionsMemberSave()
    {
        $this->_request->setParam('social_user_group', 'member');
        return $this->responseReroute(__CLASS__, 'permissions-save');
    } /* END actionPermissionsMemberSave */

    public function actionPermissionsModeratorSave()
    {
        $this->_request->setParam('social_user_group', 'moderator');
        return $this->responseReroute(__CLASS__, 'permissions-save');
    } /* END actionPermissionsModeratorSave */

    public function actionPermissionsGuestSave()
    {
        $this->_request->setParam('social_user_group', 'guest');
        return $this->responseReroute(__CLASS__, 'permissions-save');
    } /* END actionPermissionsGuestSave */

    /**
     * Asserts that the currently browsing user can manage social permissions.
     */
    protected function _assertCanManageSocialPermissions(array $socialForum)
    {
        if (!$this->_getForumModel()->canManageSocialPermissions($socialForum, $socialForum, $errorPhraseKey)) {
            throw $this->getNoPermissionResponseException();
        }
    } /* END _assertCanManageSocialPermissions */
}