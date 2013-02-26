<?php


class MockMoneyField extends DataExtension {

	public function getFakeData(Generator $faker) {
		return $faker->randomFloat(2, 1, 1000);
	}

}

