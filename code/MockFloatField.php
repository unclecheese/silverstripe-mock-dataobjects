<?php




class MockFloatField extends DataExtension {

	public function getFakerData(Generator $faker) {
		if($this->owner->hook("LATITUDE")) {
			return $faker->latitude;
		}
		if($this->owner->hook("LONGITUDE")) {
			return $faker->longitude;
		}
		
		return $faker->randomFloat();
	}

}

