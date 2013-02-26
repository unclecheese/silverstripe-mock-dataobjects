<?php



class MockTimeField extends DataExtension {


	public function getFakeData(Generator $faker) {
		return $faker->dateTimeThisMonth;
	}
}