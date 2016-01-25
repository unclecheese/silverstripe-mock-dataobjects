<?php

/**
 * Defines the methods that are injected into the {@link Varchar} class for
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */

use Faker\Generator;

class MockVarcharField extends DataExtension
{


    /**
     * Gets a random text value.
     *
     * @param Faker\Generator
     * @return string
     */
    public function getFakeData(Generator $faker)
    {
        if ($data = $this->getDataByFieldName($faker)) {
            return $data;
        }

        return $faker->sentence(rand(2, 5));
    }



    /**
     * Tries a number of hooks to determine what type of text
     * to generate, e.g. a person's name, an address, email, URL, etc.
     * @param  Generator $faker
     * @return mixed
     */
    public function getDataByFieldName(Generator $faker)
    {
        $candidates = array(
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
        foreach ($candidates as $c) {
            if ($this->owner->hook(strtoupper($c))) {
                return $faker->$c;
            }
        }
        if ($this->owner->hook("FULLNAME")) {
            return $faker->name;
        }


        return false;
    }
}
