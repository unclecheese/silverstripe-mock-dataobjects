<?php

/**
 * Defines the methods that are injected into the {@link Currency} class for
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */

use Faker\Generator;

class MockCurrencyField extends DataExtension
{


    /**
     * Gets a random currency value
     * 
     * @param Faker\Generator
     * @return float
     */
    public function getFakeData(Generator $faker)
    {
        return mt_rand(1, 100000)/100;
    }
}
