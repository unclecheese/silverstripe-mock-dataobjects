<?php



class MockDatetimeField extends DataExtension {


	public function getFakerData(Generator $faker) {
		return $faker->dateTimeThisYear()->format('Y-m-d H:i:s');
	}

}
