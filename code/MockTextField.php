<?php


class MockTextField extends DataExtension {

	public function getFakeData(Generator $faker) {
		return $faker->paragraph(rand(1,3));
	}
}


