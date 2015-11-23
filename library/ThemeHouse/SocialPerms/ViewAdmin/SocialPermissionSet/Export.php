<?php

/**
 * Exports social permission set as XML.
 */
class ThemeHouse_SocialPerms_ViewAdmin_SocialPermissionSet_Export extends XenForo_ViewAdmin_Base
{

    public function renderXml()
    {
        $this->setDownloadFileName('social-permission-set.xml');
        return $this->_params['xml']->saveXml();
    } /* END renderXml */
}