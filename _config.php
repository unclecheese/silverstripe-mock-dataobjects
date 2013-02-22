<?php

foreach(SS_ClassLoader::instance()->getManifest()->getDescendantsOf("DBField") as $class) {
	$mockClass = "Mock{$class}Field";
	if(class_exists($mockClass)) {
		Object::add_extension($class, $mockClass);
	}
}

Object::add_extension("DBField", "MockDBField");
Object::add_extension("DataObject", "MockDataObject");