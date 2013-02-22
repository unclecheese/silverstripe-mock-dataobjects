<?php




class MockIntField extends DataExtension {

	public function getFakerData(Generator $faker) {
		return $faker->randomNumber();
	}
}
