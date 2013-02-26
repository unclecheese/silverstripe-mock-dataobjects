<?php




class MockIntField extends DataExtension {

	public function getFakeData(Generator $faker) {
		return $faker->randomNumber();
	}
}
