<?php

/**
 *
 * @see ThemeHouse_SocialGroups_ControllerPublic_SocialCategory
 */
class ThemeHouse_SocialPerms_Extend_ThemeHouse_SocialGroups_ControllerPublic_SocialCategory extends XFCP_ThemeHouse_SocialPerms_Extend_ThemeHouse_SocialGroups_ControllerPublic_SocialCategory
{

    /**
     *
     * @see ThemeHouse_SocialGroups_ControllerPublic_SocialCategory::actionCreateSocialForum()
     */
    public function actionCreateSocialForum()
    {
        $response = parent::actionCreateSocialForum();

        if ($response instanceof XenForo_ControllerResponse_View) {
            /* @var $socialPermissionSetModel ThemeHouse_SocialPerms_Model_SocialPermissionSet */
            $socialPermissionSetModel = $this->getModelFromCache('ThemeHouse_SocialPerms_Model_SocialPermissionSet');

            $socialPermissionSets = $socialPermissionSetModel->prepareSocialPermissionSets(
                $socialPermissionSetModel->getSocialPermissionSets());
            $response->params['socialPermissionSets'] = $socialPermissionSets;

            $forum = $response->params['forum'];
            $canManageSocialPermissions = $this->_getSocialForumModel()->canManageSocialPermissions(
                array(), $forum);
            $response->params['canManageSocialPermissions'] = $canManageSocialPermissions;

            if (!$canManageSocialPermissions) {
                $socialPermissionSetIds = array_keys($socialPermissionSets);
                $response->params['socialForum']['social_permission_set_id_th'] = reset($socialPermissionSetIds);
            }
        }

        return $response;
    } /* END actionCreateSocialForum */

    /**
     *
     * @see ThemeHouse_SocialGroups_ControllerPublic_SocialCategory::actionAddSocialForum()
     */
    public function actionAddSocialForum()
    {
        $GLOBALS['ThemeHouse_SocialGroups_ControllerPublic_SocialCategory'] = $this;

        return parent::actionAddSocialForum();
    } /* END actionAddSocialForum */
}