<?php

/**
 * Admin controller for handling actions on social permission sets.
 */
class ThemeHouse_SocialPerms_ControllerAdmin_SocialPermissionSet extends XenForo_ControllerAdmin_Abstract
{

    /**
     * Shows a list of social permission sets.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionIndex()
    {
        $socialPermissionSetModel = $this->_getSocialPermissionSetModel();
        $socialPermissionSets = $socialPermissionSetModel->prepareSocialPermissionSets(
            $socialPermissionSetModel->getSocialPermissionSets());
        $viewParams = array(
            'socialPermissionSets' => $socialPermissionSets
        );
        return $this->responseView('ThemeHouse_SocialPerms_ViewAdmin_SocialPermissionSet_List',
            'th_social_permission_set_list_socialperms', $viewParams);
    } /* END actionIndex */

    /**
     * Helper to get the social permission set add/edit form controller
     * response.
     *
     * @param array $socialPermissionSet
     *
     * @return XenForo_ControllerResponse_View
     */
    protected function _getSocialPermissionSetAddEditResponse(array $socialPermissionSet)
    {
        /* @var $permissionModel XenForo_Model_Permission */
        $permissionModel = $this->getModelFromCache('XenForo_Model_Permission');

        $preparedOption = $permissionModel->getSocialGroupsPreparedOption(array(), true);
        $guestPermissions = array();
        foreach ($preparedOption['permissions'] as $permissionId => $permissionName) {
            $guestPermissions[$permissionId] = array(
                'value' => $permissionId,
                'label' => $permissionName
            );
        }

        $preparedOption = $permissionModel->getSocialGroupsPreparedOption(array(), false);
        $memberPermissions = array();
        foreach ($preparedOption['permissions'] as $permissionId => $permissionName) {
            $memberPermissions[$permissionId] = array(
                'value' => $permissionId,
                'label' => $permissionName
            );
        }

        $selectedPermissions = array();
        if (!empty($socialPermissionSet['awaiting_permissions'])) {
            $selectedPermissions['awaiting'] = unserialize($socialPermissionSet['awaiting_permissions']);
        }
        if (!empty($socialPermissionSet['invited_permissions'])) {
            $selectedPermissions['invited'] = unserialize($socialPermissionSet['invited_permissions']);
        }
        if (!empty($socialPermissionSet['guest_permissions'])) {
            $selectedPermissions['guest'] = unserialize($socialPermissionSet['guest_permissions']);
        }
        if (!empty($socialPermissionSet['member_permissions'])) {
            $selectedPermissions['member'] = unserialize($socialPermissionSet['member_permissions']);
        }
        if (!empty($socialPermissionSet['moderator_permissions'])) {
            $selectedPermissions['moderator'] = unserialize($socialPermissionSet['moderator_permissions']);
        }
        if (!empty($socialPermissionSet['creator_permissions'])) {
            $selectedPermissions['creator'] = unserialize($socialPermissionSet['creator_permissions']);
        }

        $socialPermissionSetModel = $this->_getSocialPermissionSetModel();

        $viewParams = array(
            'socialPermissionSet' => $socialPermissionSet,

            'selectedPermissions' => $selectedPermissions,

            'guestPermissions' => $guestPermissions,
            'memberPermissions' => $memberPermissions
        );

        if (!empty($socialPermissionSet['social_permission_set_id'])) {
            $viewParams = array_merge($viewParams,
                array(
                    'masterTitle' => $socialPermissionSetModel->getSocialPermissionSetMasterTitlePhraseValue(
                        $socialPermissionSet['social_permission_set_id']),
                    'masterDescription' => $socialPermissionSetModel->getSocialPermissionSetMasterDescriptionPhraseValue(
                        $socialPermissionSet['social_permission_set_id'])
                ));
        }

        return $this->responseView('ThemeHouse_SocialPerms_ViewAdmin_SocialPermissionSet_Edit',
            'th_social_permission_set_edit_socialperms', $viewParams);
    } /* END _getSocialPermissionSetAddEditResponse */

    /**
     * Displays a form to add a new social permission set.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionAdd()
    {
        $socialPermissionSet = $this->_getSocialPermissionSetModel()->getDefaultSocialPermissionSet();

        return $this->_getSocialPermissionSetAddEditResponse($socialPermissionSet);
    } /* END actionAdd */

    /**
     * Displays a form to edit an existing social permission set.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionEdit()
    {
        $socialPermissionSetId = $this->_input->filterSingle('social_permission_set_id', XenForo_Input::STRING);

        if (!$socialPermissionSetId) {
            return $this->responseReroute('ThemeHouse_SocialPerms_ControllerAdmin_SocialPermissionSet', 'add');
        }

        $socialPermissionSet = $this->_getSocialPermissionSetOrError($socialPermissionSetId);

        return $this->_getSocialPermissionSetAddEditResponse($socialPermissionSet);
    } /* END actionEdit */

    /**
     * Inserts a new social permission set or updates an existing one.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionSave()
    {
        $this->_assertPostOnly();

        $socialPermissionSetId = $this->_input->filterSingle('social_permission_set_id', XenForo_Input::STRING);

        $input = $this->_input->filter(
            array(
                'awaiting_permissions' => XenForo_Input::ARRAY_SIMPLE,
                'invited_permissions' => XenForo_Input::ARRAY_SIMPLE,
                'guest_permissions' => XenForo_Input::ARRAY_SIMPLE,
                'member_permissions' => XenForo_Input::ARRAY_SIMPLE,
                'moderator_permissions' => XenForo_Input::ARRAY_SIMPLE,
                'creator_permissions' => XenForo_Input::ARRAY_SIMPLE
            ));

        $writer = XenForo_DataWriter::create('ThemeHouse_SocialPerms_DataWriter_SocialPermissionSet');
        $writer->setExtraData(ThemeHouse_SocialPerms_DataWriter_SocialPermissionSet::DATA_TITLE,
            $this->_input->filterSingle('title', XenForo_Input::STRING));
        $writer->setExtraData(ThemeHouse_SocialPerms_DataWriter_SocialPermissionSet::DATA_DESCRIPTION,
            $this->_input->filterSingle('description', XenForo_Input::STRING));
        if ($socialPermissionSetId) {
            $writer->setExistingData($socialPermissionSetId);
        }
        $writer->bulkSet($input);
        $writer->save();

        if ($this->_input->filterSingle('reload', XenForo_Input::STRING)) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_UPDATED,
                XenForo_Link::buildAdminLink('social-permission-sets/edit', $writer->getMergedData()));
        } else {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildAdminLink('social-permission-sets') .
                     $this->getLastHash($writer->get('social_permission_set_id')));
        }
    } /* END actionSave */

    /**
     * Deletes a social permission set.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionDelete()
    {
        $socialPermissionSetId = $this->_input->filterSingle('social_permission_set_id', XenForo_Input::STRING);
        $socialPermissionSet = $this->_getSocialPermissionSetOrError($socialPermissionSetId);

        $writer = XenForo_DataWriter::create('ThemeHouse_SocialPerms_DataWriter_SocialPermissionSet');
        $writer->setExistingData($socialPermissionSet);

        if ($this->isConfirmedPost()) { // delete social permission set
            $writer->delete();

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildAdminLink('social-permission-sets'));
        } else { // show delete confirmation prompt
            $writer->preDelete();
            $errors = $writer->getErrors();
            if ($errors) {
                return $this->responseError($errors);
            }

            $viewParams = array(
                'socialPermissionSet' => $socialPermissionSet
            );

            return $this->responseView('ThemeHouse_SocialPerms_ViewAdmin_SocialPermissionSet_Delete',
                'th_social_permission_set_delete_socialperms', $viewParams);
        }
    } /* END actionDelete */

    public function actionExport()
    {
        $socialPermissionSetId = $this->_input->filterSingle('social_permission_set_id', XenForo_Input::UINT);
        $socialPermissionSet = $this->_getSocialPermissionSetOrError($socialPermissionSetId);

        $this->_routeMatch->setResponseType('xml');

        $viewParams = array(
            'socialPermissionSet' => $socialPermissionSet,
            'xml' => $this->_getSocialPermissionSetModel()->getSocialPermissionSetXml($socialPermissionSet)
        );

        return $this->responseView('ThemeHouse_SocialPerms_ViewAdmin_SocialPermissionSet_Export', '', $viewParams);
    } /* END actionExport */

    public function actionImport()
    {
        $socialPermissionSetModel = $this->_getSocialPermissionSetModel();

        if ($this->isConfirmedPost()) {
            $upload = XenForo_Upload::getUploadedFile('upload');
            if (!$upload) {
                return $this->responseError(
                    new XenForo_Phrase('th_please_upload_valid_social_permission_set_xml_file_socialperms'));
            }

            $document = $this->getHelper('Xml')->getXmlFromFile($upload);
            $caches = $socialPermissionSetModel->importSocialPermissionSetXml($document);

            return XenForo_CacheRebuilder_Abstract::getRebuilderResponse($this, $caches,
                XenForo_Link::buildAdminLink('social-permission-sets'));
        } else {
            return $this->responseView('ThemeHouse_SocialPerms_ViewAdmin_SocialPermissionSet_Import',
                'th_social_permission_set_import_socialperms');
        }
    } /* END actionImport */

    /**
     * Gets a valid social permission set or throws an exception.
     *
     * @param string $socialPermissionSetId
     *
     * @return array
     */
    protected function _getSocialPermissionSetOrError($socialPermissionSetId)
    {
        $socialPermissionSet = $this->_getSocialPermissionSetModel()->getSocialPermissionSetById($socialPermissionSetId);
        if (!$socialPermissionSet) {
            throw $this->responseException(
                $this->responseError(
                    new XenForo_Phrase('th_requested_social_permission_set_not_found_socialperms'), 404));
        }

        return $socialPermissionSet;
    } /* END _getSocialPermissionSetOrError */

    /**
     * Get the social permission sets model.
     *
     * @return ThemeHouse_SocialPerms_Model_SocialPermissionSet
     */
    protected function _getSocialPermissionSetModel()
    {
        return $this->getModelFromCache('ThemeHouse_SocialPerms_Model_SocialPermissionSet');
    } /* END _getSocialPermissionSetModel */
}