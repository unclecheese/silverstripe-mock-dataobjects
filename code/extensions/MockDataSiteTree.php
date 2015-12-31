<?php


/**
 * Decorates the SiteTree class to add new functionality for mock data management
 *
 * @package silverstripe-mock-dataobjects
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockDataSiteTree extends DataExtension
{


    /**
     * Adds a new button to the form actions to fill the page with mock data
     *
     * @param FieldList
     */
    public function updateCMSActions(FieldList $actions)
    {
        $actions->addFieldToTab("ActionMenus.MoreOptions", FormAction::create("addMockData", _t('MockData.FILLWITHMOCKDATA', 'Fill with mock data')));
    }
}
