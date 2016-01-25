<?php


/**
 * Defines the methods inherited by all {@link DBField} classes to support
 * generating mock data
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
use Faker\Generator;

class MockDBField extends DataExtension
{


    /**
     * Ensures that every DBField can have getFakeData() called on it. Future proofing.
     *
     * @param Faker\Generator
     * @return string
     */
    public function getFakeData(Generator $faker)
    {
        return "";
    }



    /**
     * For field types that can contain a variety of data, e.g. Varchar, this method
     * informs the Faker\Generator object what kind of data to generate based on the name of the field.
     * For instance, a field named "FirstName" should generate a person's name. A field named "Country" should
     * generate a country name.
     *
     * Field names can be matched at the beginning or end of the string, for instance:
     * "DoctorFirstName" and "Address2" will generate a first name and an address, respectively.
     *
     * This logic can be refined in the lang file so that database fields can be named in the local language,
     * e.g. "Prenom" or "Pays" and still be mapped to the correct data type.
     *
     * @param string $name The name of the data type to examine
     * @return boolean
     */
    public function hook($name)
    {
        $list = false;
        $current_locale = i18n::get_locale();
        $default_lang = Config::inst()->forClass("MockDBField")->default_lang;
        $default_locale = i18n::get_locale_from_lang($default_lang);

        i18n::set_locale($default_locale);
        $core_list = _t('MockDataObject.'.$name);

        i18n::set_locale($current_locale);
        $user_list = _t('MockDataObject.'.$name);

        $list = $user_list ?: $core_list;

        if ($list) {
            $candidates = explode(",", $list);
            $fieldName = $this->owner->getName();
            foreach ($candidates as $c) {
                $c = trim($c);
                if (preg_match('/^'.$c.'[A-Z0-9]*/', $fieldName) || preg_match('/'.$c.'$/', $fieldName)) {
                    return true;
                }
            }
        }
        return false;
    }
}
