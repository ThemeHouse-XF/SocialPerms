<?php

/**
 * Data writer for social permission sets.
 */
class ThemeHouse_SocialPerms_DataWriter_SocialPermissionSet extends XenForo_DataWriter
{

    /**
     * Constant for extra data that holds the value for the phrase
     * that is the title of this section.
     *
     * This value is required on inserts.
     *
     * @var string
     */
    const DATA_TITLE = 'phraseTitle';

    /**
     * Constant for extra data that holds the value for the phrase
     * that is the description of this data.
     *
     * @var string
     */
    const DATA_DESCRIPTION = 'phraseDescription';

    /**
     * Title of the phrase that will be created when a call to set the
     * existing data fails (when the data doesn't exist).
     *
     * @var string
     */
    protected $_existingDataErrorPhrase = 'th_requested_social_permission_set_not_found_socialperms';

    /**
     * Gets the fields that are defined for the table.
     * See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            'xf_social_permission_set_th' => array(
                'social_permission_set_id' => array(
                    'type' => self::TYPE_UINT,
                    'autoIncrement' => true
                ), /* END 'social_permission_set_id' */
                'awaiting_permissions' => array(
                    'type' => self::TYPE_SERIALIZED,
                    'default' => ''
                ), /* END 'awaiting_permissions' */
                'invited_permissions' => array(
                    'type' => self::TYPE_SERIALIZED,
                    'default' => ''
                ), /* END 'invited_permissions' */
                'guest_permissions' => array(
                    'type' => self::TYPE_SERIALIZED,
                    'default' => ''
                ), /* END 'guest_permissions' */
                'member_permissions' => array(
                    'type' => self::TYPE_SERIALIZED,
                    'default' => ''
                ), /* END 'member_permissions' */
                'moderator_permissions' => array(
                    'type' => self::TYPE_SERIALIZED,
                    'default' => ''
                ), /* END 'moderator_permissions' */
                'creator_permissions' => array(
                    'type' => self::TYPE_SERIALIZED,
                    'default' => ''
                ), /* END 'creator_permissions' */
            ), /* END 'xf_social_permission_set_th' */
        );
    } /* END _getFields */

    /**
     * Gets the actual existing data out of data that was passed in.
     * See parent for explanation.
     *
     * @param mixed
     *
     * @return array false
     */
    protected function _getExistingData($data)
    {
        if (!$socialPermissionSetId = $this->_getExistingPrimaryKey($data, 'social_permission_set_id')) {
            return false;
        }

        $socialPermissionSet = $this->_getSocialPermissionSetModel()->getSocialPermissionSetById($socialPermissionSetId);
        if (!$socialPermissionSet) {
            return false;
        }

        return $this->getTablesDataFromArray($socialPermissionSet);
    } /* END _getExistingData */

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'social_permission_set_id = ' . $this->_db->quote($this->getExisting('social_permission_set_id'));
    } /* END _getUpdateCondition */

    /**
     * Pre-save handling.
     */
    protected function _preSave()
    {
        $titlePhrase = $this->getExtraData(self::DATA_TITLE);
        if ($titlePhrase !== null && strlen($titlePhrase) == 0) {
            $this->error(new XenForo_Phrase('please_enter_valid_title'), 'title');
        }
    } /* END _preSave */

    /**
     * Post-save handling.
     */
    protected function _postSave()
    {
        $titlePhrase = $this->getExtraData(self::DATA_TITLE);
        if ($titlePhrase !== null) {
            $this->_insertOrUpdateMasterPhrase($this->_getTitlePhraseName($this->get('social_permission_set_id')),
                $titlePhrase, '');
        }

        $descriptionPhrase = $this->getExtraData(self::DATA_DESCRIPTION);
        if ($titlePhrase !== null) {
            $this->_insertOrUpdateMasterPhrase($this->_getDescriptionPhraseName($this->get('social_permission_set_id')),
                $descriptionPhrase, '');
        }
    } /* END _postSave */

    /**
     * Post-delete handling.
     */
    protected function _postDelete()
    {
        $socialPermissionSetId = $this->get('social_permission_set_id');

        $this->_deleteMasterPhrase($this->_getTitlePhraseName($socialPermissionSetId));
        $this->_deleteMasterPhrase($this->_getDescriptionPhraseName($socialPermissionSetId));
    } /* END _postDelete */

    /**
     * Gets the name of the social permission set's title phrase.
     *
     * @param string $id
     *
     * @return string
     */
    protected function _getTitlePhraseName($id)
    {
        return $this->_getSocialPermissionSetModel()->getSocialPermissionSetTitlePhraseName($id);
    } /* END _getTitlePhraseName */

    /**
     * Gets the name of the social permission set's description phrase.
     *
     * @param string $id
     *
     * @return string
     */
    protected function _getDescriptionPhraseName($id)
    {
        return $this->_getSocialPermissionSetModel()->getSocialPermissionSetDescriptionPhraseName($id);
    } /* END _getDescriptionPhraseName */

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