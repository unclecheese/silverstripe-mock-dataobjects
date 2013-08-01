<?php


use Faker\Generator;

class MockBooleanField extends DataExtension {

	public function getFakeData(Generator $faker) {
		return $faker->boolean();
	}

}

