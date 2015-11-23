<?php

class ThemeHouse_SocialPerms_Listener_TemplateHook extends ThemeHouse_Listener_TemplateHook
{

    protected function _getHooks()
    {
        return array(
            'th_social_forum_fields_extra_socialgroups',
            'th_social_forum_tools_socialgroups',
            'admin_forum_edit_forum_options'
        );
    } /* END _getHooks */

    public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
    {
        $templateHook = new ThemeHouse_SocialPerms_Listener_TemplateHook($hookName, $contents, $hookParams, $template);
        $contents = $templateHook->run();
    } /* END templateHook */

    protected function _thSocialForumFieldsExtraSocialGroups()
    {
        $pattern = '#(<dt><label>' . new XenForo_Phrase('th_set_social_forum_status_socialgroups') . '.*)(</ul>)#Us';
        $replacement = '${1}' . $this->_escapeDollars($this->_render('th_social_forum_fields_socialperms')) . '${2}';
        $this->_patternReplace($pattern, $replacement);
    } /* END _thSocialForumFieldsExtraSocialGroups */

    protected function _thSocialForumToolsSocialGroups()
    {
        $this->_appendTemplate('th_social_forum_tools_socialperms');
    } /* END _thSocialForumToolsSocialGroups */

    protected function _adminForumEditForumOptions()
    {
        $viewParams = $this->_fetchViewParams();
        if ($viewParams['controllerName'] != 'ThemeHouse_SocialGroups_ControllerAdmin_SocialCategory') {
            return;
        }

        $this->_appendTemplate('th_permissions_pane_socialperms', $viewParams);
    } /* END _adminForumEditForumOptions */
}