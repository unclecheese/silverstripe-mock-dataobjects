<?php



class MockTimeField extends DataExtension {


	public function getFakerData(Generator $faker) {
		return $faker->dateTimeThisMonth;
	}
}