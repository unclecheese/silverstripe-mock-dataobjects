<?php


use Faker\Generator;

class MockFloatField extends DataExtension {

	public function getFakeData(Generator $faker) {
		if($this->owner->hook("LATITUDE")) {
			return $faker->latitude;
		}
		if($this->owner->hook("LONGITUDE")) {
			return $faker->longitude;
		}
		
		return $faker->randomFloat();
	}

}

