<?php


class MockDoubleField extends DataExtension {


	public function getFakerData(Generator $faker) {
		return $faker->randomFloat();
	}

}

