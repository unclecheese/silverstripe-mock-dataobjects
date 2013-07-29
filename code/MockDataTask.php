<?php



class MockDataTask extends BuildTask {


	protected $title = "Generate or populate records with mock data";



	protected $request;



	public function run($request) {
		$this->request = $request;
		$args = $request->getVar('args');
		if(count($args) < 2) {
			$this->showError("Usage: MockDataTask <generate|populate> <classname> [options]");
		}

		list($operation, $className) = $args;

		if(!class_exists($className) || !is_subclass_of($className, "DataObject")) {
			$this->showError("Please specify a valid DataObject descendant class.");
		}

		switch($operation) {
			case "generate":
				$this->doGenerate($className);
			break;

			case "populate":
				$this->doPopulate($className);
			break;

			default:
				$this->showError("Please specify a valid operation (\"generate\" or \"populate\")");
			break;
		}

	}



	protected function doGenerate($className) {
		$count = $this->request->getVar('count') ?: 10;
		$parent = $this->request->getVar('parent');
		$parentField = $this->request->getVar('parentfield') ?: "ParentID";

		$config = array ();
		if($this->request->getVar('onlyEmpty')) {
			$config['only_empty'] = true;
		}
		if($this->request->getVar('downloadImages')) {
			$config['download_images'] = true;
		}
		if($this->request->getVar('includeRelations')) {
			$config['include_relations'] = true;
		}


		if($parent) {
			$parentPage = SiteTree::get()->byID((int) $parent);
			if(!$parentPage) {
				$parentPage = SiteTree::get_by_link($parent);
			}
			if(!$parentPage) {
				$parentPage = SiteTree::get()->filter(array('Title' => trim($parent)))->first();
			}
			if(!$parentPage) {
				$this->showError("Could not find a page with ID, URLSegment, or Title \"$parent\"");
			}

			if(!$parentPage->hasField($parentField)) {
				$this->showError("$className has no field $parentField.");
			}

		}

		$sitetree = is_subclass_of($className, "SiteTree");
		$i = 0;
		while($i <= $count) {
			$obj = Injector::inst()->create($className);
			$obj->fill($config);
			if($parent && $parentPage) {
				$this->writeOut("Parent page is {$parentPage->ID}");
				$obj->$parentField = $parentPage->ID;
			}
			if($sitetree) {
				$obj->publish("Stage","Live");
				$obj->write();
			}
			$this->writeOut("Created $className record with ID {$obj->ID}.");
			$i++;
		}
	}





	protected function doPopulate($className) {


	}



	protected function showError($msg) {
		echo $msg."\n\n";
		die();
	}




	protected function writeOut($msg) {
		echo $msg."\n";		
	}
}