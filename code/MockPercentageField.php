<?php


class MockPercentageField extends DataExtension {

	public function getFakeData(Generator $faker) {
		return $faker->randomFloat(2, 0, 1);
	}

}


