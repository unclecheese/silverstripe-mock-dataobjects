<?php



class MockYearField extends DataExtension {

	public function getFakerData(Generator $faker) {
		return $faker->dateTimeThisCentury->format('Y');
	}
}

