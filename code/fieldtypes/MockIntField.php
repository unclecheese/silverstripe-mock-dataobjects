<?php

/**
 * Defines the methods that are injected into the {@link Int} class for
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */

use Faker\Generator;

class MockIntField extends DataExtension
{


    /**
     * Gets a random integer
     *
     * @param Faker\Generator
     * @return int
     */
    public function getFakeData(Generator $faker)
    {
        return $faker->randomNumber();
    }
}
