<?php


/**
 * A wrapper class for {@link Faker\Generator} that provides easy template
 * accessors for generating mock data.
 *
 * @package  silverstripe-mock-dataobjects
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 *
 */
class MockViewableData extends ViewableData
{


    /**
     * Stores a {@link Faker\Generator} instance
     * @var Faker\Generator
     */
    protected $faker;



    /**
     * Gets a Faker\Generator instance
     * @return Faker\Generator
     */
    protected function getFaker()
    {
        return $this->faker ? $this->faker : ($this->faker = Faker\Factory::create(i18n::get_locale()));
    }



    /**
     * Compares two min values and assures a usable range
     * @param  integer $min   The minimum value
     * @param  integer $max   The maximum value
     * @param  integer $adder The amount to add to the min value to create a good range
     * @return array
     */
    protected function resolveMinMax($min, $max, $adder)
    {
        if (!$max || $min > $max) {
            $max = $min + $adder;
        } elseif (!$min || $min > $max) {
            $min = $max - $adder;
            if ($min < 0) {
                $min = 0;
            }
        }

        return array($min, $max);
    }



    /**
     * A wildcard method to handle calling specially named varchar fields, e.g. Email, URL.
     * @param  string $method The method name
     * @return mixed
     */
    public function obj($fieldName, $arguments = null, $forceReturnedObject = true, $cache = false, $cacheName = null)
    {
        $varchar = DBField::create_field("Varchar", null, $fieldName);
        if ($data = $varchar->getDataByFieldName($this->getFaker())) {
            $varchar->setValue($data);
            return $varchar;
        }

        return parent::obj($fieldName);
    }


    /**
     * Generates a sentence
     * @param integer $minWords The minimum number of words
     * @param integer $maxWords The maximum number of words
     * @return  Varchar
     */
    public function Sentence($minWords = 5, $maxWords = null)
    {
        list($min, $max) = $this->resolveMinMax($minWords, $maxWords, 10);
        $words = rand($min, $max);
        return DBField::create_field("Varchar", $this->getFaker()->sentence($words));
    }



    /**
     * Generates a series of words
     * @param integer $minWords The minimum number of words
     * @param integer  $maxWords The minimum number of words
     * @return  Varchar
     */
    public function Words($minWords = 1, $maxWords = null)
    {
        return $this->Sentence($minWords, $maxWords);
    }


    /**
     * Generates a series of sentences
     * @param integer $minWords The minimum number of words
     * @param integer  $maxWords The maximum number of words
     * @return  Text
     */
    public function Sentences($minSentences = 1, $maxSentences = null)
    {
        list($min, $max) = $this->resolveMinMax($minSentences, $maxSentences, 5);
        return DBField::create_field("Text", $this->getFaker()->paragraph(rand($min, $max)));
    }


    /**
     * Generates a random number
     * @param integer $minNum The minimum value
     * @param integer $maxNum The maximum value
     * @return  Int
     */
    public function Number($minNum = 0, $maxNum = null)
    {
        list($min, $max) = $this->resolveMinMax($minNum, $maxNum, 1000);
        return DBField::create_field("Int", rand($min, $max));
    }


    /**
     * Generates a random boolean value
     * @param integer $chanceOfTrue The chance that the boolean will be true
     * @return  Boolean
     */
    public function Boolean($chanceOfTrue = 50)
    {
        return DBField::create_field("Boolean", $this->getFaker()->boolean($chanceOfTrue));
    }


    /**
     * Generates a random price
     * @param integer $minPrice The minimum price
     * @param integer $maxPrice The maximum price
     * @return  Currency
     */
    public function Currency($minPrice = 1, $maxPrice = null)
    {
        list($min, $max) = $this->resolveMinMax($minPrice, $maxPrice, 1000);
        $rand = mt_rand($min*100, $max*100);
        return DBField::create_field("Currency", $rand/100);
    }


    /**
     * Generates a random date
     * @return Date
     */
    public function Date()
    {
        return DBField::create_field("Date", $this->getFaker()->dateTimeThisYear()->format('Y-m-d'));
    }


    /**
     * Generates a random date and time.
     * @return  SS_DateTime
     */
    public function DateAndTime()
    {
        return DBField::create_field("SS_DateTime", $this->getFaker()->dateTimeThisYear()->format('Y-m-d H:i:s'));
    }



    /**
     * Returns a random float
     * @param integer $min The minimum value
     * @param integer $max The maximum value
     * @return  Float
     */
    public function Float()
    {
        return DBField::create_field("Float", mt_rand(1, 999)/mt_rand(1, 999));
    }


    /**
     * Generates a random number of Paragraphs
     * @param integer $min The minimum number of paragraphs
     * @param integer $max The maximum number of paragraphs
     * @return  HTMLText
     */
    public function Paragraphs($minVal = 1, $maxVal = null)
    {
        list($min, $max) = $this->resolveMinMax($minVal, $maxVal, 5);
        $paragraphs = rand($min, $max);
        $faker = $this->getFaker();
        $i = 0;
        $html = "";
        while ($i < $paragraphs) {
            $html .= "<p>".$faker->paragraph(rand(2, 6))."</p>";
            $i++;
        }

        return DBField::create_field("HTMLText", $html);
    }


    /**
     * Generates a random percentage
     * @return  Percentage
     */
    public function Percentage()
    {
        return DBField::create_field("Percentage", mt_rand(1, 99)/100);
    }


    /**
     * Generates a random year
     * @return Year
     */
    public function Year()
    {
        return DBField::create_field("Year", $this->getFaker()->dateTimeThisCentury->format('Y'));
    }


    /**
     * Generates a random immage
     * @param boolean $download If true, download a new image
     * @return  Image
     */
    public function Image($download = false)
    {
        return $download ? MockDataObject::download_lorem_image() : MockDataObject::get_random_local_image();
    }


    /**
     * Generates a mock latitude value
     * @return  Float
     */
    public function Latitude()
    {
        return DBField::create_field("Float", $this->getFaker()->latitude);
    }


    /**
     * Generates a mock longitude value
     * @return  Float
     */
    public function Longitude()
    {
        return DBField::create_field("Float", $this->getFaker()->longitude);
    }


    /**
     * Build a set of empty DataObjects that can be iterated over
     * @param integer $min The minumum size of the set
     * @param integer $max The maximum size of the set
     */
    public function Loop($minSize = 1, $maxSize = null)
    {
        list($min, $max) = $this->resolveMinMax($minSize, $maxSize, 10);
        $limit = rand($min, $max);
        $list = ArrayList::create(array());
        $i = 0;
        while ($i < $limit) {
            $list->push(new DataObject());
            $i++;
        }

        return $list;
    }
}
