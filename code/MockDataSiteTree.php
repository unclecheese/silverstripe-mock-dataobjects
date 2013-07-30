<?php



class MockDataSiteTree extends DataExtension {



	public function updateCMSActions(FieldList $actions) {
		$actions->addFieldToTab("ActionMenus.MoreOptions", FormAction::create("addMockData","Fill with mock data"));
	}
}