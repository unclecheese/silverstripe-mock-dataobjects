<?php


/**
 * Defines the methods that are injected into the {@link Double} class for
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */


use Faker\Generator;

class MockDoubleField extends DataExtension
{


    /**
     * Gets a random float value
     *
     * @param Faker\Generator
     * @return float
     */
    public function getFakeData(Generator $faker)
    {
        return mt_rand(1, 999)/1000;
    }
}
