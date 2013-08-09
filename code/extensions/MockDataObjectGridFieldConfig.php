<?php



class MockDataObjectGridFieldConfig extends Extension {



	public function updateConfig() {
		$this->owner->addComponent(new MockDataGenerator());
	}
}