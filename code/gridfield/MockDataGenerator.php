<?php


class MockDataGenerator implements GridField_HTMLProvider, GridField_DataManipulator, GridField_ActionProvider {


	public function getHTMLFragments($gridField) {		
		$forTemplate = new ArrayData(array());
		$forTemplate->Colspan = count($gridField->getColumns());
		$forTemplate->Fields = new ArrayList();
		$forTemplate->Fields->push(
			new FieldGroup(
				new LabelField('mockdata_create',_t('MockDataGenerator.CREATE','Create').'&nbsp;'),
				new TextField('mockdata[Count]','','10'),
				new LabelField('mockdata_records',_t('MockDataGenerator.MOCKRECORDS','&nbsp;mock records')),
				GridField_FormAction::create($gridField, 'mockdata', 'hi', 'mockdata', null)
					->addExtraClass('mock-data-generator-btn')					
					->setAttribute('id', 'action_mockdata_' . $gridField->getModelClass())
			)
		);
		return array(
			'header' => $forTemplate->renderWith('MockDataGeneratorHeader_Row')
		);


	}


	public function getManipulatedData(GridField $gridField, SS_List $dataList) {

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

	}





}
