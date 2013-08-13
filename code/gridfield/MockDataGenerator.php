<?php


class MockDataGenerator implements GridField_HTMLProvider, GridField_DataManipulator, GridField_ActionProvider {


	public function getHTMLFragments($gridField) {
		Requirements::javascript(MOCK_DATAOBJECTS_DIR.'/javascript/mock_dataobjects.js');
		Requirements::css(MOCK_DATAOBJECTS_DIR.'/css/mock_dataobjects.css');

		$forTemplate = new ArrayData(array());
		$forTemplate->Colspan = count($gridField->getColumns());
		$forTemplate->CountField = TextField::create('mockdata[Count]','','10')
			->setAttribute('maxlength', 2)
			->setAttribute('size', 2);
		$forTemplate->RelationsField = new CheckboxField('mockdata[IncludeRelations]','', false);
		$forTemplate->DownloadsField = new CheckboxField('mockdata[DownloadImages]','', false);
		$forTemplate->Cancel = GridField_FormAction::create($gridField, 'cancel', _t('MockData.CANCEL','Cancel'), 'cancel', null)
					->setAttribute('id', 'action_mockdata_cancel' . $gridField->getModelClass())
					->addExtraClass('mock-data-generator-btn cancel');										

		$forTemplate->Action = GridField_FormAction::create($gridField, 'mockdata', _t('MockData.CREATE','Create'), 'mockdata', null)
					->addExtraClass('mock-data-generator-btn create ss-ui-action-constructive')					
					->setAttribute('id', 'action_mockdata_' . $gridField->getModelClass());

		return array(
			'before' => $forTemplate->renderWith('MockDataGenerator')
		);


	}


	public function getManipulatedData(GridField $gridField, SS_List $dataList) {
		$state = $gridField->State->MockDataGenerator;
		$count = (string) $state->Count;
		if(!$count) return $dataList;
		$generator = new MockDataBuilder($gridField->getModelClass());
		$ids = $generator
			->setCount($count)
			->setIncludeRelations($state->IncludeRelations)
			->setDownloadImages($state->DownloadImages)
			->generate();

		foreach($ids as $id) {
			$dataList->add($id);
		}

		return $dataList;
	}


	/**
	 * Return a list of the actions handled by this action provider.
	 *
	 * Used to identify the action later on through the $actionName parameter 
	 * in {@link handleAction}.
	 *
	 * There is no namespacing on these actions, so you need to ensure that 
	 * they don't conflict with other components.
	 * 
	 * @param GridField
	 * @return Array with action identifier strings. 
	 */
	public function getActions($gridField) {
		return array ('mockdata');
	}
	
	/**
	 * Handle an action on the given {@link GridField}.
	 *
	 * Calls ALL components for every action handled, so the component needs 
	 * to ensure it only accepts actions it is actually supposed to handle.
	 * 
	 * @param GridField
	 * @param String Action identifier, see {@link getActions()}.
	 * @param Array Arguments relevant for this 
	 * @param Array All form data
	 */
	public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
		if($actionName !== "mockdata") return;
		$state = $gridField->State->MockDataGenerator;
		$state->Count = $data['mockdata']['Count'];
		$state->IncludeRelations = isset($data['mockdata']['IncludeRelations']);
		$state->DownloadImages = isset($data['mockdata']['DownloadImages']);		

	}





}
