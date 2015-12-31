<?php


/**
 * Defines the methods that are injected into the {@link HTMLVarchar} class for
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */


use Faker\Generator;

class MockHTMLVarcharField extends DataExtension
{


    /**
     * Gets a random sentence
     * 
     * @param Faker\Generator
     * @return string
     */
    public function getFakeData(Generator $faker)
    {
        return $faker->sentence(rand(2, 6));
    }
}
