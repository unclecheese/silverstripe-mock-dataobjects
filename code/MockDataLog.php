<?php


class MockDataLog extends DataObject {


	static $db = array (
		'RecordClass' => 'Varchar',
		'RecordID' => 'Int'
	);



	static $indexes = array (
		'RecordClass' => true,
		'RecordID' => true
	);
}