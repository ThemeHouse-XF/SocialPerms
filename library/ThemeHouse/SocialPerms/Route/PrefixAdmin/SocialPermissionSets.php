<?php

/**
 * Route prefix handler for social permission sets in the admin control panel.
 */
class ThemeHouse_SocialPerms_Route_PrefixAdmin_SocialPermissionSets implements XenForo_Route_Interface
{

    /**
     * Match a specific route for an already matched prefix.
     *
     * @see XenForo_Route_Interface::match()
     */
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
        $action = $router->resolveActionWithIntegerParam($routePath, $request, 'social_permission_set_id');
        $action = $router->resolveActionAsPageNumber($action, $request);
        return $router->getRouteMatch('ThemeHouse_SocialPerms_ControllerAdmin_SocialPermissionSet', $action, 'socialPermissionSets');
    } /* END match */

    /**
     * Method to build a link to the specified page/action with the provided
     * data and params.
     *
     * @see XenForo_Route_BuilderInterface
     */
    public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
    {
        return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'social_permission_set_id', 'title');
    } /* END buildLink */
}