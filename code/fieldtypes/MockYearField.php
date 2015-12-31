<?php


/**
 * Defines the methods that are injected into the {@link Year} class for
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */


use Faker\Generator;

class MockYearField extends DataExtension
{


    /**
     * Gets a random year from this century
     *
     * @param Faker\Generator
     * @return string
     */
    public function getFakeData(Generator $faker)
    {
        return $faker->dateTimeThisCentury->format('Y');
    }
}
