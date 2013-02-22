<?php


class MockPercentageField extends DataExtension {

	public function getFakerData(Generator $faker) {
		return $faker->randomFloat(2, 0, 1);
	}

}


