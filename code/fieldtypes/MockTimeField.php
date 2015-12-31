<?php

/**
 * Defines the methods that are injected into the {@link Time} class for
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */

use Faker\Generator;

class MockTimeField extends DataExtension
{



    /**
     * Gets a random time value, e.g. 14:20:22
     *
     * @param Faker\Generator
     * @return string
     */
    public function getFakeData(Generator $faker)
    {
        return $faker->dateTimeThisMonth()->format('H:i:s');
    }
}
