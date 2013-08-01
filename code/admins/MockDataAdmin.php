<?php


class MockDataAdmin extends LeftAndMain implements PermissionProvider {

	static $menu_title = "Mock Data";

	static $url_segment = "mock-data";


	static $allowed_actions = array (
		'CreateForm',
		'metadata'
	);

	public function init() {
		parent::init();
		Requirements::javascript(MOCK_DATAOBJECTS_DIR.'/javascript/mock_dataobjects.js');
		Requirements::css(MOCK_DATAOBJECTS_DIR.'/css/mock_dataobjects.css');
	}



	public function CreateForm() {
		$map = array('' => '-- '._t('MockData.PLEASESELECT','Please select').' --');
		$dataobjects = SS_ClassLoader::instance()->getManifest()->getDescendantsOf("DataObject");
		sort($dataobjects);
		foreach($dataobjects as $subclass) {			
			if(in_array("TestOnly", class_implements($subclass))) continue;
			$map[$subclass] = $subclass;
		}
		unset($map["SiteTree"]);		
		$f = Form::create(
			$this,
			"CreateForm",
			FieldList::create(
				DropdownField::create("ClassName", _t('MockData.SELECTDATAOBJECT','Which content type do you want to create?'))
					->setSource($map)
					->addExtraClass("required")
					->setAttribute("data-url",$this->Link('metadata')),					
				DropdownField::create("ParentID", _t('MockData.SELECTPARENTPAGE','The content type you have chosen is managed on a page. With which page should these records be associated?'))
					->setSource(array()),
				NumericField::create("Count", _t('MockData.HOWMANYRECORDS','How many records do you want to create?'))
					->addExtraClass("required"),
				OptionsetField::create("IncludeRelations", _t('MockData.RELATEDDATA','Related data'))
					->setSource(array(
						'0' => _t('MockData.ONLYNATIVEFIELDS','Only fill native fields'),
						'1' => _t('MockData.BUILDRELATIONS','Create mock data for all has_many/many_many relations')
					)),

				OptionsetField::create("DownloadImages", _t('MockData.FILEATTACHMENTS','File attachments'))
					->setSource(array(
						'0' => _t('MockData.USEEXISTINGFILES','Use existing stock files and images'),
						'1' => _t('MockData.USENEWFILES','Download new stock files and images')
					))
			),
			FieldList::create(FormAction::create('doCreate','')->addExtraClass("ss-ui-button ss-ui-action-constructive"))
		);
		return $f;
	}


	public function doCreate($data, $form) {
		if(class_exists($data['ClassName']) && is_subclass_of($data['ClassName'], "DataObject")) {
			$i = 0;
			$params = array (
				'include_relations' => (boolean) $data['IncludeRelations'],
				'download_images' => (boolean) $data['DownloadImages']
			);
			$parentField = false;
			if($data['ParentID']) {
				if($record = DataList::create("SiteTree")->byID((int) $data['ParentID'])) {
					$parentName = Injector::inst()->get($data['ClassName'])->getReverseAssociation($record->ClassName);
					$parentField = $parentName."ID";
				}
			}

			while($i < (int) $data['Count']) {
				$record = Object::create($data['ClassName']);
				$record->fill($params);
				if($parentField) {
					$record->$parentField = (int) $data['ParentID'];
				}
				$record->write();
				$i++;
			}

			return new SS_HTTPResponse("$i records created");
		}
		return new SS_HTTPResponse("Invalid class");
	}



	public function metadata(SS_HTTPRequest $r) {
		if($class = $r->requestVar('ClassName')) {
			$json = array ();
			$SNG = Injector::inst()->get($class);
			$related_classes = array ();
			$files = false;
			foreach($SNG->has_one() as $relationName => $relationClass) {
				if(is_subclass_of($relationClass, "SiteTree")) {
					$has_many = Config::inst()->get($relationClass, "has_many", Config::UNINHERITED);
					if(in_array($class, array_values($has_many))) {						
						foreach(DataList::create($relationClass) as $record) {
							$related_classes[$record->ID] = $record->Title;
						}
					}
				}
				elseif($relationClass == "File" || is_subclass_of($relationClass, "File")) {
					$files = true;
				}
			}
			if(!empty($related_classes)) {
				$json['related_classes'] = $related_classes;
			}
			$json['files'] = $files;
			$has_many = $SNG->has_many();
			$many_many = $SNG->many_many();
			$json['relations'] = (!empty($has_many) && !empty($many_many));

			return Convert::array2json($json);
		}
	}


}	