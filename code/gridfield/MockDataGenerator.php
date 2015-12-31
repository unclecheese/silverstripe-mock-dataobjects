<?php


/**
 * Defines the component for {@link GridField} that allows for populating the record set
 * with mock records.
 *
 * @package silverstripe-mock-dataobjects
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockDataGenerator implements GridField_HTMLProvider, GridField_DataManipulator, GridField_ActionProvider
{



    /**
     * Adds the HTML to the GridField that includes options for the mock data as well as the action button
     *
     * @param GridField
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        Requirements::javascript(MOCK_DATAOBJECTS_DIR.'/javascript/mock_dataobjects.js');
        Requirements::css(MOCK_DATAOBJECTS_DIR.'/css/mock_dataobjects.css');

        $forTemplate = new ArrayData(array());
        $forTemplate->Colspan = count($gridField->getColumns());
        $forTemplate->CountField = TextField::create('mockdata[Count]', '', '10')
            ->setAttribute('maxlength', 2)
            ->setAttribute('size', 2);
        $forTemplate->RelationsField = new CheckboxField('mockdata[IncludeRelations]', '', false);
        $forTemplate->DownloadsField = new CheckboxField('mockdata[DownloadImages]', '', false);
        $forTemplate->Cancel = GridField_FormAction::create($gridField, 'cancel', _t('MockData.CANCEL', 'Cancel'), 'cancel', null)
                    ->setAttribute('id', 'action_mockdata_cancel' . $gridField->getModelClass())
                    ->addExtraClass('mock-data-generator-btn cancel');

        $forTemplate->Action = GridField_FormAction::create($gridField, 'mockdata', _t('MockData.CREATE', 'Create'), 'mockdata', null)
                    ->addExtraClass('mock-data-generator-btn create ss-ui-action-constructive')
                    ->setAttribute('id', 'action_mockdata_' . $gridField->getModelClass());

        return array(
            'before' => $forTemplate->renderWith('MockDataGenerator')
        );
    }



    /**
     * Adds the records to the database and returns a new {@link DataList}
     *
     * @param GridField
     * @param SS_List
     * @return SS_List
     */
    public function getManipulatedData(GridField $gridField, SS_List $dataList)
    {
        $state = $gridField->State->MockDataGenerator;
        $count = (string) $state->Count;
        if (!$count) {
            return $dataList;
        }
        $generator = new MockDataBuilder($gridField->getModelClass());
        $ids = $generator
            ->setCount($count)
            ->setIncludeRelations($state->IncludeRelations)
            ->setDownloadImages($state->DownloadImages === true)
            ->generate();

        foreach ($ids as $id) {
            $dataList->add($id);
        }

        return $dataList;
    }


    /**
     * Return a list of the actions handled by this action provider.
     *
     * @param GridField
     * @return Array with action identifier strings.
     */
    public function getActions($gridField)
    {
        return array('mockdata');
    }




    /**
     * Handle an action on the given {@link GridField}.
     *
     * @param GridField
     * @param String Action identifier, see {@link getActions()}.
     * @param Array Arguments relevant for this
     * @param Array All form data
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName !== "mockdata") {
            return;
        }
        $state = $gridField->State->MockDataGenerator;
        $state->Count = $data['mockdata']['Count'];
        $state->IncludeRelations = isset($data['mockdata']['IncludeRelations']);
        $state->DownloadImages = isset($data['mockdata']['DownloadImages']);
    }
}
