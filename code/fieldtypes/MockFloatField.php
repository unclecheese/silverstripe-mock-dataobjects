<?php

/**
 * Defines the methods that are injected into the {@link Float} class for
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */

use Faker\Generator;

class MockFloatField extends DataExtension
{


    /**
     * Gets a random float value. Checks hooks for lat/long
     *
     * @param Faker\Generator
     * @return float
     */
    public function getFakeData(Generator $faker)
    {
        if ($this->owner->hook("LATITUDE")) {
            return $faker->latitude;
        }
        if ($this->owner->hook("LONGITUDE")) {
            return $faker->longitude;
        }

        return mt_rand(1, 999)/mt_rand(1, 999);
    }
}
