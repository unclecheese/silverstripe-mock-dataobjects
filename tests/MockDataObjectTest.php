<?php


/**
 * @package silverstripe-mock-dataobjects
 * @subpackage tests
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockDataObjectTest extends SapphireTest {

	
	//protected static $fixture_file = 'MockDataObjects.yml';


	protected $extraDataObjects = array(
		'MockDataObjectTest_Person',
		'MockDataObjectTest_Certification',
		'MockDataObjectTest_ServiceArea'
	);



	public $record;

	

	public function setUpOnce() {
		parent::setUpOnce();
		error_reporting(E_ALL);
	}



	public function testDataObjectsCanFill() {		
		$obj = new MockDataObjectTest_Person();		
		$obj->fill(array(
			'include_relations' => false
		));
		$obj->write();				
		$this->assertEquals(1, $this->getList()->count());		
	}



	public function testGeneratorCreatesRecords() {
		MockDataBuilder::create("MockDataObjectTest_Person")
			->setCount(10)
			->setIncludeRelations(false)
			->generate();
		$this->assertEquals(11, $this->getList()->count());
	}


	public function testFieldsCreateGoodData() {
		$rec = new MockDataObjectTest_Person();
		$rec->fill();
		$rec->write();

		// Currency
		$this->assertNotNull($rec->Salary);		
		$this->assertTrue(is_numeric($rec->Salary));
		$this->assertGreaterThan(1, $rec->Salary);
		$this->assertLessThan(1000, $rec->Salary);

		// Date
		$this->assertNotNull($rec->DateStarted);
		$this->assertGreaterThan(1, strtotime($rec->DateStarted));

		// DateTime
		$this->assertNotNull($rec->LastLogin);
		$this->assertGreaterThan(1, strtotime($rec->LastLogin));

		// Decimal
		$this->assertNotNull($rec->Rating);
		$this->assertGreaterThan(0, $rec->Rating);
		$this->assertLessThan(1000, $rec->Rating);

		// Float
		$this->assertNotNull($rec->Level);
		$this->assertGreaterThan(0, $rec->Rating);
		$this->assertLessThan(1000, $rec->Rating);

		// HTMLText
		$this->assertTrue($rec->obj('Description')->exists());		
		$this->assertEquals('<p>',substr($rec->Description, 0, 3));
		$this->assertEquals('</p>',substr($rec->Description, -4, 4));
		$this->assertGreaterThan(7, strlen($rec->Description));

		// HTMLVarchar
		$this->assertTrue($rec->obj('Intro')->exists());		
		$this->assertEquals(1, preg_match('/[A-Za-z]+/', $rec->Intro));
		

		// Int
		$this->assertTrue(is_numeric($rec->Position));
		$this->assertGreaterThan(0, $rec->Position);


		// Percentage
		$this->assertGreaterThan(0, $rec->Accuracy);
		$this->assertLessThan(1, $rec->Accuracy);
		$this->assertEquals(4, strlen($rec->Accuracy));
	}



	protected function getList() {
		return DataList::create("MockDataObjectTest_Person");
	}
}




class MockDataObjectTest_Person extends DataObject implements TestOnly {


	private static $db = array (
		'IsMember' => 'Boolean',
		'Salary' => 'Currency',
		'DateStarted' => 'Date',
		'LastLogin' => 'Datetime',
		'Rating' => 'Decimal',		
		'Level' => 'Float',
		'Description' => 'HTMLText',
		'Intro' => 'HTMLVarchar',
		'Position' => 'Int',
		'Accuracy' => 'Percentage',
		'Biography' => 'Text',
		'StartTime' => 'Time',
		'FirstName' => 'Varchar',
		'LastName' => 'Varchar',
		'Address' => 'Varchar',
		'Website' => 'Varchar',
		'Email' => 'Varchar',
		'City' => 'Varchar',
		'State' => 'Varchar',
		'Zip' => 'Varchar',
		'CountryCode' => 'Varchar',
		'Phone' => 'Varchar',
		'Company' => 'Varchar',
		'YearStarted' => 'Year',
		'Latitude' => 'Float',

	);



	private static $has_many = array (
		'Certifications' => 'MockDataObjectTest_Certification'
	);



	private static $many_many = array (
		'ServiceAreas' => 'MockDataObjectTest_ServiceArea'
	);

}



class MockDataObjectTest_Certification extends DataObject implements TestOnly {


	private static $db = array (
		'Title' => 'Varchar'
	);


	private static $has_one = array (
		'Person' => 'MockDataObjectTest_Person'
	);
}


class MockDataObjectTest_ServiceArea extends DataObject implements TestOnly {


	private static $db = array (
		'Title' => 'Varchar'
	);


	private static $belongs_many_many = array (
		'Persons' => 'MockDataObjectTest_Person'
	);
}
