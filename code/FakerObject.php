<?php


class MockDataObject extends DataExtension {


	public function populate($deep = true) {
		$faker = Faker\Factory::create(i18n::get_locale());
		$core_db = Config::inst()->get("DataObject", "db", Config::UNINHERITED);
		foreach($this->owner->db() as $fieldName => $fieldType) {
			// Skip ID, ClassName, etc
			if(array_key_exists($fieldName, $core_db)) continue;

			$fakerField = Injector::inst()->get($fieldType)->getFakerField($faker);

		}
	}
}