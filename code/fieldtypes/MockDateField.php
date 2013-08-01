<?php

use Faker\Generator;


class MockDateField extends DataExtension {


	public function getFakeData(Generator $faker) {
		return $faker->dateTimeThisYear()->format('Y-m-d');
	}

}

