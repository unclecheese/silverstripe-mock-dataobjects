<?php



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