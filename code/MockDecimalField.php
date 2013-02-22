<?php


class MockDecimalField extends DataExtension {


	public function getFakerData(Generator $faker) {
		return DBField::create_field("Float", 0)->getFakerData($faker);
	}

}
