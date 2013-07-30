<?php



class MockDataBuilder extends Object {


	protected $log = array ();



	protected $subjectClass;



	protected $parentObj;



	protected $parentIdentifier;



	protected $parentField = "ParentID";



	protected $count = 10;



	protected $onlyEmpty = true;



	protected $relationCreateLimit = 5;



	protected $downloadImages = true;



	protected $includeRelations = true;



	protected $isSiteTree = false;	



	public function __construct($className) {
		$this->subjectClass = $className;
		if(!class_exists($className) || !is_subclass_of($className, "DataObject")) {
			throw new Exception("$className doesn't exist, or it is not a DataObject.");
		}
		if(!Injector::inst()->get($className)->hasExtension("MockDataObject")) {
			throw new Exception("$className does not have the MockDataObject extension applied.");
		}

		$this->isSiteTree = is_subclass_of($className, "SiteTree");

		return $this;
	}




	public function generate() {
		if($this->parentIdentifier && !$this->parentObj) {
			$this->determineParentObj();
		}

		
		$i = 0;
		$parentField = $this->parentField;
		while($i < $this->count) {
			$obj = Injector::inst()->create($this->subjectClass);
			$obj->fill(array(
				'only_empty' => $this->onlyEmpty,
				'include_relations' => $this->includeRelations,
				'download_images' => $this->downloadImages,
				'relation_create_limit' => $this->relationCreateLimit
			));
			if($this->parentObj) {				
				$obj->$parentField = $this->parentObj->ID;
			}
			if($this->isSiteTree) {
				$obj->write();
				$obj->publish("Stage","Live");
			}
			$this->log("Created {$this->subjectClass} \"{$obj->getTitle()}\".");
			$i++;
		}



	}



	public function populate() {
		if($this->parentIdentifier && !$this->parentObj) {
			$this->determineParentObj();
		}

		$set = DataList::create($this->subjectClass);
		if($this->parentObj) {
			$set = $set->filter(array(
				$this->parentField => $this->parentObj->ID
			));
		}
		foreach($set as $obj) {
			$obj->fill(array(
				'only_empty' => $this->onlyEmpty,
				'include_relations' => $this->includeRelations,
				'download_images' => $this->downloadImages,
				'relation_create_limit' => $this->relationCreateLimit
			));

			if($this->isSiteTree) {
				$obj->write();
				$obj->publish("Stage","Live");
			}

			$this->log("Updated {$this->subjectClass} \"{$obj->getTitle()}\".");
		}		
	}




	protected function determineParentObj() {
		$parent = $this->parentIdentifier;
		$parentPage = SiteTree::get()->byID((int) $parent);
		if(!$parentPage) {
			$parentPage = SiteTree::get_by_link($parent);
		}
		if(!$parentPage) {
			$parentPage = SiteTree::get()->filter(array('Title' => trim($parent)))->first();
		}
		if(!$parentPage) {
			throw new Exception("Could not find a page with ID, URLSegment, or Title \"$parent\"");
		}
		if(!Injector::inst()->get($this->subjectClass)->hasField($this->parentField)) {			
			throw new Exception("{$this->subjectClass} has no field {$this->parentField}.");
		}

		$this->parentObj = $parentPage;
		$this->log("Parent page is #{$parentPage->ID} {$parentPage->getTitle()}");			

	}



	public function setParentObj(DataObject $obj) {
		$this->parentObj = $obj;
		return $this;
	}



	public function setParentField($field) {
		$this->parentField = $field;
		return $this;
	}



	public function setParentIdentifier($id) {
		$this->parentIdentifier = $id;
		return $this;
	}


	public function setCount($count) {
		$this->count = $count;
		return $this;		
	}


	public function setOnlyEmpty($bool) {
		$this->onlyEmpty = (bool) $bool;
		return $this;
	}


	public function setRelationCreateLimit($num) {
		$this->relationCreateLimit = $num;
		return $this;
	}


	public function setDownloadImages($bool) {
		$this->downloadImages = (bool) $bool;
		return $this;
	}


	public function setIncludeRelations($bool) {
		$this->includeRelations = (bool) $bool;
		return $this;
	}




	protected function log($msg) {
		if(Director::is_cli()) {
			echo "$msg\n";
		}
		else {
			$this->log[] = $msg;	
		}
	}





}