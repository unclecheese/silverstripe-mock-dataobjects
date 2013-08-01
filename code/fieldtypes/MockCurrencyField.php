<?php


use Faker\Generator;


class MockCurrencyField extends DataExtension {


	public function getFakeData(Generator $faker) {
		return $faker->randomFloat(2, 1, 1000);
	}

}
