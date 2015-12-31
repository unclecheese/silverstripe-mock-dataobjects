<?php

/**
 * Defines the methods that are injected into the {@link Date} class for
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */

use Faker\Generator;

class MockDateField extends DataExtension
{


    /**
     * Gets a random date from this year
     *
     * @param Faker\Generator
     * @return string
     */
    public function getFakeData(Generator $faker)
    {
        return $faker->dateTimeThisYear()->format('Y-m-d');
    }
}
