<?php


/**
 * Defines a database record of a mock data creation. Has references to the class 
 * of data created and its ID.
 *
 * @package silverstripe-mock-dataobjects
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockDataLog extends DataObject
{


    private static $db = array(
        'RecordClass' => 'Varchar',
        'RecordID' => 'Int'
    );



    private static $indexes = array(
        'RecordClass' => true,
        'RecordID' => true
    );
}
