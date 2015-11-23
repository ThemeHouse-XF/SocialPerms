<?php

/**
 *
 * @see XenForo_DataWriter_Forum
 */
class ThemeHouse_SocialPerms_Extend_XenForo_DataWriter_Forum extends XFCP_ThemeHouse_SocialPerms_Extend_XenForo_DataWriter_Forum
{

    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_forum']['member_permissions'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );
        $fields['xf_forum']['moderator_permissions'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );
        $fields['xf_forum']['creator_permissions'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );

        return $fields;
    } /* END _getFields */

    /**
     * Pre-save handling.
     */
    protected function _preSave()
    {
        parent::_preSave();

        if (isset($GLOBALS['ThemeHouse_SocialGroups_ControllerAdmin_SocialCategory'])) {
            /* @var $controller ThemeHouse_SocialGroups_ControllerAdmin_SocialCategory */
            $controller = $GLOBALS['ThemeHouse_SocialGroups_ControllerAdmin_SocialCategory'];

            $input = $controller->getInput()->filter(array(
                'member_permissions' => XenForo_Input::ARRAY_SIMPLE,
                'moderator_permissions' => XenForo_Input::ARRAY_SIMPLE,
                'creator_permissions' => XenForo_Input::ARRAY_SIMPLE,
            ));

            $this->bulkSet($input);
        }
    } /* END _preSave */
}