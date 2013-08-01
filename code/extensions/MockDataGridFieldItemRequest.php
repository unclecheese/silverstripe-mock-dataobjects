<?php



class MockDataGridFieldItemRequest extends DataExtension {


	public function updateItemEditForm(Form $form) {		
		$form->Actions()->push(FormAction::create("doAddMockData",_t('MockData.FILLWITHMOCKDATA','Fill with mock data')));
	}



	public function doAddMockData($data, $form) {
		$this->owner->record->fill(array(
			'only_empty' => true,
			'include_relations' => false,
			'download_images' => false
		));

		Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
		$link = Controller::join_links($this->owner->gridField->Link(),"item", $this->owner->record->ID);
		return Controller::curr()->redirect($link);

	}
}