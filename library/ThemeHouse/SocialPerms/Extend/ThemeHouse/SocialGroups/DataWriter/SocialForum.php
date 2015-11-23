<?php

/**
 *
 * @see ThemeHouse_SocialGroups_DataWriter_SocialForum
 */
class ThemeHouse_SocialPerms_Extend_ThemeHouse_SocialGroups_DataWriter_SocialForum extends XFCP_ThemeHouse_SocialPerms_Extend_ThemeHouse_SocialGroups_DataWriter_SocialForum
{

    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_social_forum']['guest_permissions'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );
        $fields['xf_social_forum']['member_permissions'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );
        $fields['xf_social_forum']['moderator_permissions'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );
        $fields['xf_social_forum']['social_permission_set_id_th'] = array(
            'type' => self::TYPE_UINT,
            'default' => 0
        );

        return $fields;
    } /* END _getFields */

    protected function _preSave()
    {
        if (isset($GLOBALS['ThemeHouse_SocialGroups_ControllerPublic_SocialForum']) ||
             isset($GLOBALS['ThemeHouse_SocialGroups_ControllerPublic_SocialCategory'])) {
            if (isset($GLOBALS['ThemeHouse_SocialGroups_ControllerPublic_SocialForum'])) {
                /* @var $controller ThemeHouse_SocialGroups_ControllerPublic_SocialForum */
                $controller = $GLOBALS['ThemeHouse_SocialGroups_ControllerPublic_SocialForum'];
            } else {
                /* @var $controller ThemeHouse_SocialGroups_ControllerPublic_SocialCategory */
                $controller = $GLOBALS['ThemeHouse_SocialGroups_ControllerPublic_SocialCategory'];
            }

            $socialPermissionSetId = $controller->getInput()->filterSingle('social_permission_set_id',
                XenForo_Input::UINT);
            $this->set('social_permission_set_id_th', $socialPermissionSetId);
        }

        if ($this->isChanged('social_permission_set_id_th') && !$this->get('social_permission_set_id_th')) {
            $socialForumModel = ThemeHouse_SocialGroups_SocialForum::getSocialForumModel();
            $socialCategory = $this->_getSocialCategoryData();
            if (!$socialForumModel->canManageSocialPermissions(array(), $socialCategory)) {
                $this->error(new XenForo_Phrase('th_must_select_social_permission_set_socialperms'));
            }
        }

        parent::_preSave();
    } /* END _preSave */
}