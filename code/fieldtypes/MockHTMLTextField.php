<?php

/**
 * Defines the methods that are injected into the {@link HTMLText} class for
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */


use Faker\Generator;

class MockHTMLTextField extends DataExtension
{


    /**
     * Gets a random set of paragraphs
     *
     * @param Faker\Generator
     * @return string
     */
    public function getFakeData(Generator $faker)
    {
        $paragraphs = rand(1, 5);
        $i = 0;
        $ret = "";
        while ($i < $paragraphs) {
            $ret .= "<p>".$faker->paragraph(rand(2, 6))."</p>";
            $i++;
        }
        return $ret;
    }
}
