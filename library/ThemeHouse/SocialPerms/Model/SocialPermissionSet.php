<?php

/**
 * Model for social permission sets.
 */
class ThemeHouse_SocialPerms_Model_SocialPermissionSet extends XenForo_Model
{

    /**
     * Gets social permission sets that match the specified criteria.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions
     *
     * @return array [social permission set id] => info.
     */
    public function getSocialPermissionSets(array $conditions = array(), array $fetchOptions = array())
    {
        $whereClause = $this->prepareSocialPermissionSetConditions($conditions, $fetchOptions);

        $sqlClauses = $this->prepareSocialPermissionSetFetchOptions($fetchOptions);
        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        return $this->fetchAllKeyed(
            $this->limitQueryResults(
                '
                SELECT social_permission_set.*
                    ' . $sqlClauses['selectFields'] . '
                FROM xf_social_permission_set_th AS social_permission_set
                ' . $sqlClauses['joinTables'] . '
                WHERE ' . $whereClause . '
                ' . $sqlClauses['orderClause'] . '
            ', $limitOptions['limit'], $limitOptions['offset']),
            'social_permission_set_id');
    } /* END getSocialPermissionSets */

    /**
     * Gets the social permission set that matches the specified criteria.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions Options that affect what is fetched.
     *
     * @return array false
     */
    public function getSocialPermissionSet(array $conditions = array(), array $fetchOptions = array())
    {
        $socialPermissionSets = $this->getSocialPermissionSets($conditions, $fetchOptions);

        return reset($socialPermissionSets);
    } /* END getSocialPermissionSet */

    /**
     * Gets a social permission set by ID.
     *
     * @param integer $socialPermissionSetId
     * @param array $fetchOptions Options that affect what is fetched.
     *
     * @return array false
     */
    public function getSocialPermissionSetById($socialPermissionSetId, array $fetchOptions = array())
    {
        $conditions = array(
            'social_permission_set_id' => $socialPermissionSetId
        );

        return $this->getSocialPermissionSet($conditions, $fetchOptions);
    } /* END getSocialPermissionSetById */

    /**
     * Gets the total number of a social permission set that match the specified
     * criteria.
     *
     * @param array $conditions List of conditions.
     *
     * @return integer
     */
    public function countSocialPermissionSets(array $conditions = array())
    {
        $fetchOptions = array();

        $whereClause = $this->prepareSocialPermissionSetConditions($conditions, $fetchOptions);
        $joinOptions = $this->prepareSocialPermissionSetFetchOptions($fetchOptions);

        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        return $this->_getDb()->fetchOne(
            '
            SELECT COUNT(*)
            FROM xf_social_permission_set_th AS social_permission_set
            ' . $joinOptions['joinTables'] . '
            WHERE ' . $whereClause . '
        ');
    } /* END countSocialPermissionSets */

    /**
     * Prepares a social permission set for display.
     *
     * @param array $socialPermissionSet
     *
     * @return array
     */
    public function prepareSocialPermissionSet(array $socialPermissionSet)
    {
        $socialPermissionSet['title'] = new XenForo_Phrase(
            $this->getSocialPermissionSetTitlePhraseName($socialPermissionSet['social_permission_set_id']));
        $socialPermissionSet['description'] = new XenForo_Phrase(
            $this->getSocialPermissionSetDescriptionPhraseName($socialPermissionSet['social_permission_set_id']));

        return $socialPermissionSet;
    }

    /**
     * Prepares a list of social permission sets for display.
     *
     * @param array $socialPermissionSets
     *
     * @return array
     */
    public function prepareSocialPermissionSets(array $socialPermissionSets)
    {
        foreach ($socialPermissionSets as &$socialPermissionSet) {
            $socialPermissionSet = $this->prepareSocialPermissionSet($socialPermissionSet);
        }

        return $socialPermissionSets;
    } /* END prepareSocialPermissionSets */ /* END prepareSocialPermissionSets */ /* END prepareSocialPermissionSets */ /* END prepareSocialPermissionSets */

    /**
     * Gets all social permission sets titles.
     *
     * @return array [social permission set id] => title.
     */
    public static function getSocialPermissionSetTitles()
    {
        $socialPermissionSets = XenForo_Model::create(__CLASS__)->getSocialPermissionSets();
        $titles = array();
        foreach ($socialPermissionSets as $socialPermissionSetId => $socialPermissionSet) {
            $titles[$socialPermissionSetId] = $socialPermissionSet['title'];
        }
        return $titles;
    } /* END getSocialPermissionSetTitles */ /* END getSocialPermissionSetTitles */

    /**
     * Gets the default social permission set record.
     *
     * @return array
     */
    public function getDefaultSocialPermissionSet()
    {
        return array(
            'social_permission_set_id' => '', /* END 'social_permission_set_id' */
        );
    } /* END getDefaultSocialPermissionSet */ /* END getDefaultSocialPermissionSet */

    /**
     * Gets the name of a social permission set's title phrase.
     *
     * @param integer $socialPermissionSetId
     *
     * @return string
     */
    public function getSocialPermissionSetTitlePhraseName($socialPermissionSetId)
    {
        return 'socialPermissionSet_' . $socialPermissionSetId . '_title';
    } /* END getSocialPermissionSetTitlePhraseName */

    /**
     * Gets a social permission set's master title phrase text.
     *
     * @param integer $socialPermissionSetId
     *
     * @return string
     */
    public function getSocialPermissionSetMasterTitlePhraseValue($socialPermissionSetId)
    {
        $phraseName = $this->getSocialPermissionSetTitlePhraseName($socialPermissionSetId);
        return $this->_getPhraseModel()->getMasterPhraseValue($phraseName);
    } /* END getSocialPermissionSetMasterTitlePhraseValue */

    /**
     * Gets the name of a social permission set's description phrase.
     *
     * @param integer $socialPermissionSetId
     *
     * @return string
     */
    public function getSocialPermissionSetDescriptionPhraseName($socialPermissionSetId)
    {
        return 'socialPermissionSet_' . $socialPermissionSetId . '_description';
    } /* END getSocialPermissionSetDescriptionPhraseName */

    /**
     * Gets a social permission set's master description phrase text.
     *
     * @param integer $socialPermissionSetId
     *
     * @return string
     */
    public function getSocialPermissionSetMasterDescriptionPhraseValue($socialPermissionSetId)
    {
        $phraseName = $this->getSocialPermissionSetDescriptionPhraseName($socialPermissionSetId);
        return $this->_getPhraseModel()->getMasterPhraseValue($phraseName);
    } /* END getSocialPermissionSetMasterDescriptionPhraseValue */

    /**
     * Prepares a set of conditions to select social permission sets against.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions The fetch options that have been provided. May
     * be edited if criteria requires.
     *
     * @return string Criteria as SQL for where clause
     */
    public function prepareSocialPermissionSetConditions(array $conditions, array &$fetchOptions)
    {
        $db = $this->_getDb();
        $sqlConditions = array();

        if (isset($conditions['social_permission_set_ids']) && !empty($conditions['social_permission_set_ids'])) {
            $sqlConditions[] = 'social_permission_set.social_permission_set_id IN (' .
                 $db->quote($conditions['social_permission_set_ids']) . ')';
        } else
            if (isset($conditions['social_permission_set_id'])) {
                $sqlConditions[] = 'social_permission_set.social_permission_set_id = ' .
                     $db->quote($conditions['social_permission_set_id']);
            }

        $this->_prepareSocialPermissionSetConditions($conditions, $fetchOptions, $sqlConditions);

        return $this->getConditionsForClause($sqlConditions);
    } /* END prepareSocialPermissionSetConditions */ /* END prepareSocialPermissionSetConditions */

    /**
     * Method designed to be overridden by child classes to add to set of
     * conditions.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions The fetch options that have been provided. May
     * be edited if criteria requires.
     * @param array $sqlConditions List of conditions as SQL snippets. May be
     * edited if criteria requires.
     */
    protected function _prepareSocialPermissionSetConditions(array $conditions, array &$fetchOptions,
        array &$sqlConditions)
    {
    } /* END _prepareSocialPermissionSetConditions */

    /**
     * Checks the 'join' key of the incoming array for the presence of the
     * FETCH_x bitfields in this class
     * and returns SQL snippets to join the specified tables if required.
     *
     * @param array $fetchOptions containing a 'join' integer key built from
     * this class's FETCH_x bitfields.
     *
     * @return string containing selectFields, joinTables, orderClause keys.
     * Example: selectFields = ', user.*, foo.title'; joinTables = ' INNER JOIN
     * foo ON (foo.id = other.id) '; orderClause = 'ORDER BY x.y'
     */
    public function prepareSocialPermissionSetFetchOptions(array &$fetchOptions)
    {
        $selectFields = '';
        $joinTables = '';
        $orderBy = '';

        $this->_prepareSocialPermissionSetFetchOptions($fetchOptions, $selectFields, $joinTables, $orderBy);

        return array(
            'selectFields' => $selectFields,
            'joinTables' => $joinTables,
            'orderClause' => ($orderBy ? "ORDER BY $orderBy" : '')
        );
    } /* END prepareSocialPermissionSetFetchOptions */

    /**
     * Method designed to be overridden by child classes to add to SQL snippets.
     *
     * @param array $fetchOptions containing a 'join' integer key built from
     * this class's FETCH_x bitfields.
     * @param string $selectFields = ', user.*, foo.title'
     * @param string $joinTables = ' INNER JOIN foo ON (foo.id = other.id) '
     * @param string $orderBy = 'x.y ASC, x.z DESC'
     */
    protected function _prepareSocialPermissionSetFetchOptions(array &$fetchOptions, &$selectFields, &$joinTables,
        &$orderBy)
    {
    } /* END _prepareSocialPermissionSetFetchOptions */

    /**
     * Gets the XML representation of a social permission set, including permissions.
     *
     * @param array $socialPermissionSet
     *
     * @return DOMDocument
     */
    public function getSocialPermissionSetXml(array $socialPermissionSet)
    {
        $document = new DOMDocument('1.0', 'utf-8');
        $document->formatOutput = true;

        $socialPermissionSetId = $socialPermissionSet['social_permission_set_id'];

        $rootNode = $document->createElement('social_permission_set');
        $rootNode->setAttribute('awaiting_permissions', $socialPermissionSet['awaiting_permissions']);
        $rootNode->setAttribute('invited_permissions', $socialPermissionSet['invited_permissions']);
        $rootNode->setAttribute('guest_permissions', $socialPermissionSet['guest_permissions']);
        $rootNode->setAttribute('member_permissions', $socialPermissionSet['member_permissions']);
        $rootNode->setAttribute('moderator_permissions', $socialPermissionSet['moderator_permissions']);
        $rootNode->setAttribute('creator_permissions', $socialPermissionSet['creator_permissions']);
        $rootNode->setAttribute('description', $this->getSocialPermissionSetMasterDescriptionPhraseValue($socialPermissionSetId));
        $rootNode->setAttribute('title', $this->getSocialPermissionSetMasterTitlePhraseValue($socialPermissionSetId));

        $document->appendChild($rootNode);

        return $document;
    } /* END getSocialPermissionSetXml */

    /**
     * Imports a social permission set XML file.
     *
     * @param SimpleXMLElement $document
     */
    public function importSocialPermissionSetXml(SimpleXMLElement $document)
    {
        if ($document->getName() != 'social_permission_set') {
            throw new XenForo_Exception(new XenForo_Phrase('th_provided_file_is_not_valid_social_permission_set_xml_socialperms'), true);
        }

        $title = (string) $document['title'];
        if ($title === '') {
            throw new XenForo_Exception(new XenForo_Phrase('th_provided_file_is_not_valid_social_permission_set_xml_socialperms'), true);
        }

        $description = (string) $document['description'];

        $input = array(
            'awaiting_permissions' => (string) $document['awaiting_permissions'],
            'invited_permissions' => (string) $document['invited_permissions'],
            'guest_permissions' => (string) $document['guest_permissions'],
            'member_permissions' => (string) $document['member_permissions'],
            'moderator_permissions' => (string) $document['moderator_permissions'],
            'creator_permissions' => (string) $document['creator_permissions'],
        );

        foreach ($input as &$_input) {
            if ($_input) {
                $_input = unserialize($_input);
            }
        }

        $dw = XenForo_DataWriter::create('ThemeHouse_SocialPerms_DataWriter_SocialPermissionSet');
        $dw->setExtraData(ThemeHouse_SocialPerms_DataWriter_SocialPermissionSet::DATA_TITLE, $title);
        $dw->setExtraData(ThemeHouse_SocialPerms_DataWriter_SocialPermissionSet::DATA_DESCRIPTION, $description);
        $dw->bulkSet($input);
        $dw->save();
    } /* END importSocialPermissionSetXml */

    /**
     *
     * @return XenForo_Model_Phrase
     */
    protected function _getPhraseModel()
    {
        return $this->getModelFromCache('XenForo_Model_Phrase');
    } /* END _getPhraseModel */
}