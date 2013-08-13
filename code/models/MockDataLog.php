<?php


class MockDataLog extends DataObject {


	private static $db = array (
		'RecordClass' => 'Varchar',
		'RecordID' => 'Int'
	);



	private static $indexes = array (
		'RecordClass' => true,
		'RecordID' => true
	);



	private static $summary_fields = array (
		'RecordClass' => 'Type',
		'NumberOfRecords' => 'Number of records'
	);


	private static $searchable_fields = array (
		'RecordClass'
	);



	public function getNumberOfRecords() {
		return MockDataLog::get()->filter(array(
			'RecordClass' => $this->RecordClass
		))->count();
	}
}