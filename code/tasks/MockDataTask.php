<?php



class MockDataTask extends BuildTask {


	protected $title = "Generate or populate records with mock data";



	protected $request;



	public function run($request) {		
		$this->request = $request;
		$args = $request->getVar('args');
		if($args[0] == "cleanup") {
			if(!isset($args[1])) {
				$args[1] = "__all__";
			}
			if($args[1] != "__all__") {
				if(!class_exists($args[1]) || !is_subclass_of($args[1], "DataObject")) {
					$this->showError("Please specify a valid DataObject descendant class.");				
				}
			}	

			return $this->cleanup($args[1]);
		}
		else {
			if(count($args) < 2) {
				$this->showError("Usage: MockDataTask <generate|populate|cleanup> <classname> [options]");
			}

			list($operation, $className) = $args;

			if(!class_exists($className) || !is_subclass_of($className, "DataObject")) {
				$this->showError("Please specify a valid DataObject descendant class.");
			}

			if(!in_array($operation, array('generate','populate', 'cleanup'))) {
				$this->showError("Please specify a valid operation (\"generate\", \"populate\", or \"cleanup\")");
			}


			$this->runBuilderCommand($operation, $className);
		}

	}



	protected function runBuilderCommand($cmd, $className) {
		$count = $this->request->getVar('count') ?: 10;
		$parent = $this->request->getVar('parent');
		$parentField = $this->request->getVar('parentField') ?: "ParentID";

		try {
			$builder = MockDataBuilder::create($className);
		}
		catch(Exception $e) {
			echo $e->getMessage();
			die();
		}

		$builder
			->setOnlyEmpty($this->request->getVar('onlyEmpty') === "false" ? false : true)
			->setDownloadImages($this->request->getVar('download_images') === "false" ? false : true)
			->setCount($count)
			->setParentIdentifier($parent ?: null)
			->setParentField($parentField)
		;

		try {
			$builder->$cmd();
		}
		catch(Exception $e) {
			echo $e->getMessage()."\n\n";
			die();
		}		
	}




	protected function cleanup($className) {
		$classes = ($className == "__all__") ? MockDataLog::get()->column('RecordClass') : array($className);
		foreach($classes as $recordClass) {
			$logs = MockDataLog::get()->filter(array('RecordClass' => $recordClass));
			$ids = $logs->column('RecordID');
			$list = DataList::create($recordClass)->byIDs($ids);
			$this->writeOut("Deleting " . $list->count() . " $recordClass records");
			$list->removeAll();
			$this->writeOut("Done.");
			$logs->removeAll();
		}
	}





	protected function showError($msg) {
		echo $msg."\n\n";
		die();
	}




	protected function writeOut($msg) {
		echo $msg."\n";		
	}
}