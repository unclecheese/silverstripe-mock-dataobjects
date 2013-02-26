<?php


class MockDoubleField extends DataExtension {


	public function getFakeData(Generator $faker) {
		return $faker->randomFloat();
	}

}

