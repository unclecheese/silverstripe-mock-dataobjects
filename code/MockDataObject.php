<?php 


class MockDataObject extends DataExtension {


	public function fill($deep = true) {
		$faker = Faker\Factory::create();
		foreach($this->owner->db() as $fieldName => $fieldType) {
			$value = $this->owner->obj($fieldName)->getFakerData($faker);
			$this->owner->$fieldName = $value;

		}
		$this->owner->Created = DBField::create_field("SS_Datetime", Config::inst()->forClass("MockDataObject")->fake_creation_date);
	}
}