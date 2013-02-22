<?php




class MockBooleanField extends DataExtension {

	public function getFakerData(Generator $faker) {
		return $faker->boolean();
	}

}

