<?php


class MockTextField extends DataExtension {

	public function getFakerData(Generator $faker) {
		return $faker->paragraph(rand(1,3));
	}
}


