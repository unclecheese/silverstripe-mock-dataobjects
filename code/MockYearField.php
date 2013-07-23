<?php


use Faker\Generator;


class MockYearField extends DataExtension {

	public function getFakeData(Generator $faker) {
		return $faker->dateTimeThisCentury->format('Y');
	}
}

