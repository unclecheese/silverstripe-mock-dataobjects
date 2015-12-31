<?php


/**
 * Injects functionality into the CMS to allow for populating a page with mock data
 *
 * @package silverstripe-mock-dataobjects
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockDataCMS extends DataExtension
{


    /**
     * A form action that takes the current page ID to populate it with mock data
     *
     * @param array $data The data passed in from the form
     * @param CMSForm $form The Form object that was used
     */
    public function addMockData($data, $form)
    {
        if ($page = SiteTree::get()->byID($data['ID'])) {
            $page->fill(array(
                'only_empty' => true,
                'include_relations' => false,
                'download_images' => false
            ));

            $this->owner->response->addHeader(
                'X-Status',
                'Added mock data'
            );
        }
        
        return $this->owner->getResponseNegotiator()->respond($this->owner->request);
    }
}
