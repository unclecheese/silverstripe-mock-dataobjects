 <?php


class MockDataCMSMainExtension extends DataExtension {


	private static $allowed_children = array (
		'addmockchildren'
	);
	

	public function addMockData($data, $form) {
		if($page = SiteTree::get()->byID($data['ID'])) {
			$page->fill(array(
				'only_empty' => true,
				'include_relations' => false,
				'download_images' => false
			));

			$this->owner->response->addHeader(
				'X-Status',
				'Added mock data'
			);
			
			return $this->owner->getResponseNegotiator()->respond($this->owner->request);

		}
	}




	public function addmockchildren(SS_HTTPRequest $r) {

	}

	
}