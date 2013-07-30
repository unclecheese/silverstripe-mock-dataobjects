<?php



class MockDataTask extends BuildTask {


	protected $title = "Generate or populate records with mock data";



	protected $request;



	public function run($request) {
		$builder = new MockDataBuilder("NewsPage");
		$this->request = $request;
		$args = $request->getVar('args');
		if(count($args) < 2) {
			$this->showError("Usage: MockDataTask <generate|populate> <classname> [options]");
		}

		list($operation, $className) = $args;

		if(!class_exists($className) || !is_subclass_of($className, "DataObject")) {
			$this->showError("Please specify a valid DataObject descendant class.");
		}

		if(!in_array($operation, array('generate','populate'))) {
			$this->showError("Please specify a valid operation (\"generate\" or \"populate\")");
		}

		$this->runCommand($operation, $className);

	}



	protected function runCommand($cmd, $className) {
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





	protected function showError($msg) {
		echo $msg."\n\n";
		die();
	}




	protected function writeOut($msg) {
		echo $msg."\n";		
	}
}