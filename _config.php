<?php

if(!class_exists("Faker\Generator")) {
    throw new RuntimeException("The silverstripe-mock-dataobjects module requires the Faker PHP library. You can install it by running 'composer require fzaninotto/faker:1.1.*@dev' in your web root. If you installed this module via composer, make the directory fzaninotto/faker exists in vendor/.");
}

define('MOCK_DATAOBJECTS_DIR',basename(dirname(__FILE__)));
$e = Config::inst()->get("DBField", "extensions");
$e[] = "MockDBField";
Config::inst()->update("DBField", "extensions", $e);
foreach(SS_ClassLoader::instance()->getManifest()->getDescendantsOf("DBField") as $class) {
	$mockClass = "Mock{$class}Field";
	if(class_exists($mockClass)) {
		$e = Config::inst()->get($class, "extensions");
		$e[] = $mockClass;
		Config::inst()->update($class, "extensions", $e);
	}
}


CMSMenu::remove_menu_item('MockChildrenController');