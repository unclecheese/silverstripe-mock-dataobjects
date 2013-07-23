<?php


use Faker\Generator;


class MockDBField extends DataExtension {

	public function getFakeData(Generator $faker) {
		return "";
	}


	public function hook($name) {
		if($list = _t('MockDataObject.'.$name)) {
			$candidates = explode(",",$list);
			$fieldName = $this->owner->getName();
			foreach($candidates as $c) {
				$c = trim($c);				
				if(preg_match('/^'.$c.'[A-Z0-9]*/', $fieldName) || preg_match('/'.$c.'$/', $fieldName)) {					
					return true;
				}
				else {
					//echo $fieldName . " does not match pattern: " . '/^'.$c.'[A-Z0-9]*/'."<br />";
				}
			}
		}
		return false;
	}

}
