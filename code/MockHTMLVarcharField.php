<?php


class MockHTMLVarcharField extends DataExtension {

	public function getFakeData(Generator $faker) {
		return $faker->sentence(rand(2,6));
	}

}
