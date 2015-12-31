<?php


/**
 * Updates a {@link GridFieldConfig} object to contain functionality for adding mock data
 * to a {@link GridField}.
 *
 * NOTE: This class will no have any effect until decoration of GridFieldConfig objects
 * is supported. See: https://github.com/silverstripe/silverstripe-framework/pull/2311
 *
 * @package silverstripe-mock-dataobjects
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockDataObjectGridFieldConfig extends Extension
{


    /**
     * Updates the config to contain a {@link MockDataGenerator} component.
     * Careful to install it ahead of {@link GridFieldPaginator}, which depends
     * on the size of the DataList.
     */
    public function updateConfig()
    {
        $pagers = $this->owner->getComponentsByType("GridFieldPaginator");
        $before = ($pagers->count()) ? "GridFieldPaginator" : null;
        $this->owner->addComponent(new MockDataGenerator(), $before);
    }
}
