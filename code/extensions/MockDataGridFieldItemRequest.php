<?php


/**
 * Injects functionality into {@link GridField} to show a button that adds mock data to
 * the record when in detail view.
 *
 * @package silverstripe-mock-dataobjects
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockDataGridFieldItemRequest extends DataExtension
{


    /**
     * Updates the edit form to add a new form action
     *
     * @param Form $form The item edit form
     */
    public function updateItemEditForm(Form $form)
    {
        $form->Actions()->push(FormAction::create("doAddMockData", _t('MockData.FILLWITHMOCKDATA', 'Fill with mock data')));
    }



    /**
     * A form action that handles populating the record with mock data
     *
     * @param array $data The data that as passed in from the form
     * @param Form $form The Form object that was used
     * @return SSViewer
     */
    public function doAddMockData($data, $form)
    {
        $this->owner->record->fill(array(
            'only_empty' => true,
            'include_relations' => false,
            'download_images' => false
        ));

        Controller::curr()->getResponse()->addHeader("X-Pjax", "Content");
        $link = Controller::join_links($this->owner->gridField->Link(), "item", $this->owner->record->ID);
        return Controller::curr()->redirect($link);
    }
}
