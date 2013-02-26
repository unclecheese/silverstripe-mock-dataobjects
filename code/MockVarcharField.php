<?php




class MockVarcharField extends DataExtension {

	public function getFakeData(Generator $faker) {
		$candidates = array (
			'firstName',
			'lastName',
			'email',
			'city',
			'state',
			'address',
			'postcode',
			'countryCode',
			'phoneNumber',
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
