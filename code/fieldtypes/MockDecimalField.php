<?php

use Faker\Generator;


class MockDecimalField extends DataExtension {


	public function getFakeData(Generator $faker) {
		return DBField::create_field("Float", 0)->getFakeData($faker);
	}

}
