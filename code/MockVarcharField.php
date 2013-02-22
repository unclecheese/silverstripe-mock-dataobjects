<?php




class MockVarcharField extends DataExtension {

	public function getFakerData(Generator $faker) {
		$candidates = array (
			'firstName',
			'lastName',
			'city',
			'state',
			'address',
			'postcode',
			'countryCode',
			'phoneNumber',
			'email',
			'url'
		);
		foreach($candidates as $c) {
			if($this->owner->hook(strtoupper($c))) {
				return $faker->$c;
			}
		}
		if($this->owner->hook("FULLNAME")) {
			return $faker->name;
		}

		return $faker->sentence(rand(2,5));

	}
}
