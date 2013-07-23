<?php

use Faker\Generator;

class MockDatetimeField extends DataExtension {


	public function getFakeData(Generator $faker) {
		return $faker->dateTimeThisYear()->format('Y-m-d H:i:s');
	}

}
