<?php


class MockDateField extends DataExtension {


	public function getFakerData(Generator $faker) {
		return $faker->dateTimeThisYear()->format('Y-m-d');
	}

}

