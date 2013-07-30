<?php


use Faker\Generator;


class MockTimeField extends DataExtension {


	public function getFakeData(Generator $faker) {
		return $faker->dateTimeThisMonth()->format('H:i:s');
	}
}