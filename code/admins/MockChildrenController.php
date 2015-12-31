<?php


/**
 * Displays a page for creating mock children in the CMS
 *
 * @package silverstripe-mock-dataobjects
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockChildrenController extends CMSMain
{


    /**
     * @var string
     */
    private static $url_segment = 'pages/addmockchildren';


    /**
     * @var string
     */
    private static $url_rule = '/$Action/$ID/$OtherID';


    /**
     * @var int
     */
    private static $url_priority = 50;


    /**
     * @var string
     */
    private static $menu_title = 'Add mock children';


    /**
     * @var string
     */
    private static $required_permission_codes = 'CMS_ACCESS_CMSMain';

    
    /**
     * @var array
     */
    private static $allowed_actions = array(
        'MockChildrenForm',
        'doAddMockChildren',
        'node'
    );



    /**
     * The default action to show the page. Accepts the ID of the page in the ID param
     *
     * @param SS_HTTPRequest
     * @return SSViewer
     */
    public function node(SS_HTTPRequest $r)
    {
        return $this->getResponseNegotiator()->respond($r);
    }



    /**
     * Builds the form for creating mock children.
     *
     * @return CMSForm
     */
    public function MockChildrenForm()
    {
        $pageTypes = array();
        $parentID = $this->request->param('ID') ?: $this->request->requestVar('ID');
        $parentPage = SiteTree::get()->byID((int) $parentID);
        if (!$parentPage) {
            return false;
        }

        $allowed_children = $parentPage->allowedChildren();
        foreach ($this->PageTypes() as $type) {
            if (!empty($allowed_children) && !in_array($type->getField('ClassName'), $allowed_children)) {
                continue;
            }

            $html = sprintf('<span class="page-icon class-%s"></span><strong class="title">%s</strong><span class="description">%s</span>',
                $type->getField('ClassName'),
                $type->getField('AddAction'),
                $type->getField('Description')
            );
            $pageTypes[$type->getField('ClassName')] = $html;
        }
        // Ensure generic page type shows on top
        if (isset($pageTypes['Page'])) {
            $pageTitle = $pageTypes['Page'];
            $pageTypes = array_merge(array('Page' => $pageTitle), $pageTypes);
        }

        $numericLabelTmpl = '<span class="step-label"><span class="flyout">%d</span><span class="arrow"></span><span class="title">%s</span></span>';

        $keys = array_keys($pageTypes);
        $fields = new FieldList(
            $typeField = new OptionsetField(
                "PageType",
                sprintf($numericLabelTmpl, 1, _t('MockData.CHOOSEPAGETYPE', 'Choose the type of page to create')),
                $pageTypes,
                reset($keys)
            ),

            new LiteralField('optionsheader', sprintf($numericLabelTmpl, 2, _t('MockData.CHOOSEOPTIONS', 'Choose options'))),
            new NumericField('Count', _t('MockData.HOWMANYPAGES', 'How many pages do you want to create?'), 10),
            new DropdownField('IncludeRelations', _t('MockData.RELATEDDATA', 'Related data'), array(
                0 => _t('MockData.CREATEMOCKRELATED', 'Create mock data for relations'),
                1 => _t('MockData.NATIVEONLY', 'Only populate native fields')
            )),
            new DropdownField('DownloadImages', _t('MockData.FILESANDIMAGES', 'Files and Images'), array(
                0 => _t('MockData.USEEXISTING', 'Use existing files and images'),
                1 => _t('MockData.DOWNLOADNEW', 'Download new files and images')
            )),
            new HiddenField('ID', '', $parentPage->ID)
        );

        
        $actions = new FieldList(
            FormAction::create("doAddMockChildren", _t('CMSMain.Create', "Create"))
                ->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept')
                ->setUseButtonTag(true)
        );
                
        
        $form = CMSForm::create(
            $this, "MockChildrenForm", $fields, $actions
        )->setHTMLID('Form_MockChildrenForm');
        $form->setResponseNegotiator($this->getResponseNegotiator());
        $form->addExtraClass(' stacked cms-content center cms-edit-form ' . $this->BaseCSSClasses());
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

        return $form;
    }



    /**
     * Handles the creation of mock children with {@link MockDataBuilder}
     *
     * @param array $data The data passed in from the form
     * @param CMSForm $form The Form object that was used
     * @return SSViewer
     */
    public function doAddMockChildren($data, $form)
    {
        $parentPage = SiteTree::get()->byID((int) $data['ID']);
        if (!$parentPage) {
            return false;
        }
        
        $className = isset($data['PageType']) ? $data['PageType'] : "Page";
        $builder = new MockDataBuilder($className);
        $builder
            ->setCount((int) $data['Count'])
            ->setIncludeRelations((bool) $data['IncludeRelations'])
            ->setDownloadImages((bool) $data['DownloadImages'])
            ->setParentField("ParentID")
            ->setParentObj($parentPage);
        try {
            $ids = $builder->generate();
        } catch (Exception $e) {
            $form->sessionMessage($e->getMessage(), "bad");
            return $this->redirectBack();
        }
        $this->response->addHeader(
            'X-Status',
            _t('MockData.CREATESUCCESS', 'Created {count} mock children under {title}', array('count' => $data['Count'], 'title' => $parentPage->Title))
        );
        $this->redirect(Controller::join_links(singleton('CMSPagesController')->Link()));
        
        return $this->getResponseNegotiator()->respond($this->request);
    }
}
