<?php


class MockHTMLVarcharField extends DataExtension {

	public function getFakerData(Generator $faker) {
		return $faker->sentence(rand(2,6));
	}

}
