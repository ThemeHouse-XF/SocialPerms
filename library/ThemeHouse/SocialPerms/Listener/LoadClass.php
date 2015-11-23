<?php

class ThemeHouse_SocialPerms_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_SocialPerms' => array(
                'controller' => array(
                    'ThemeHouse_SocialGroups_ControllerAdmin_SocialCategory',
                    'ThemeHouse_SocialGroups_ControllerPublic_SocialForum',
                    'ThemeHouse_SocialGroups_ControllerPublic_SocialCategory'
                ), /* END 'controller' */
                'datawriter' => array(
                    'ThemeHouse_SocialGroups_DataWriter_SocialForum',
                    'XenForo_DataWriter_Forum'
                ), /* END 'datawriter' */
                'model' => array(
                    'ThemeHouse_SocialGroups_Model_SocialForum'
                ), /* END 'model' */
            ), /* END 'ThemeHouse_SocialPerms' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassController($class, array &$extend)
    {
        $loadClassController = new ThemeHouse_SocialPerms_Listener_LoadClass($class, $extend, 'controller');
        $extend = $loadClassController->run();
    } /* END loadClassController */

    public static function loadClassDataWriter($class, array &$extend)
    {
        $loadClassDataWriter = new ThemeHouse_SocialPerms_Listener_LoadClass($class, $extend, 'datawriter');
        $extend = $loadClassDataWriter->run();
    } /* END loadClassDataWriter */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new ThemeHouse_SocialPerms_Listener_LoadClass($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */
}