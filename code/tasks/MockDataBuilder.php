<?php


/**
 * This class creates a process that generates mock data records. It accepts an assortment
 * of options to customise its output.
 *
 * @package silverstripe-mock-data
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockDataBuilder extends Object
{


    /**
     * @var array An arbitrary list of messages
     */
    protected $log = array();



    /**
     * @var string The class of records to create
     */
    protected $subjectClass;



    /**
     * @var DataObject The parent record, if applicable
     */
    protected $parentObj;



    /**
     * @var mixed The ID or URLSegment of the parent record
     */
    protected $parentIdentifier;



    /**
     * @var string The foreign key on the child record
     */
    protected $parentField = "ParentID";



    /**
     * @var int The number of records to create
     */
    protected $count = 10;



    /**
     * @var bool Only populate empty fields
     */
    protected $onlyEmpty = true;



    /**
     * @var int The number of records to create for a has_many or many_many relation
     */
    protected $relationCreateLimit = 5;



    /**
     * @var bool Download images from the web to populate the record's file relations
     */
    protected $downloadImages = true;



    /**
     * @var bool Populate has_many and many_many relations
     */
    protected $includeRelations = true;



    /**
     * @var bool If the subject class is a SiteTree descendant
     */
    protected $isSiteTree = false;



    /**
     * Constructor.
     *
     * @param string The class of mock records to create
     * @return MockDataBuilder
     */
    public function __construct($className)
    {
        $this->subjectClass = $className;
        if (!class_exists($className) || !is_subclass_of($className, "DataObject")) {
            throw new Exception("$className doesn't exist, or it is not a DataObject.");
        }
        if (!Injector::inst()->get($className)->hasExtension("MockDataObject")) {
            throw new Exception("$className does not have the MockDataObject extension applied.");
        }

        $this->isSiteTree = is_subclass_of($className, "SiteTree");

        return $this;
    }




    /**
     * Generates new records for the subject class
     *
     * @return array A list of the new ids inserted to the table
     */
    public function generate()
    {
        if ($this->parentIdentifier && !$this->parentObj) {
            $this->determineParentObj();
        }


        $i = 0;
        $ids = array();
        $parentField = $this->parentField;
        while ($i < $this->count) {
            $obj = Injector::inst()->create($this->subjectClass);
            if ($this->parentObj) {
                $obj->$parentField = $this->parentObj->ID;
            }
            $obj->fill(array(
                'only_empty' => $this->onlyEmpty,
                'include_relations' => $this->includeRelations,
                'download_images' => $this->downloadImages,
                'relation_create_limit' => $this->relationCreateLimit
            ));
            if ($this->parentObj) {
                $obj->write();
            }
            if ($this->isSiteTree) {
                $ids[] = $obj->write();
                $obj->publish("Stage", "Live");
            }
            $this->log("Created {$this->subjectClass} \"{$obj->getTitle()}\".");
            $i++;
        }

        return $ids;
    }



    /**
     * Populates existing records with mock data
     */
    public function populate()
    {
        if ($this->parentIdentifier && !$this->parentObj) {
            $this->determineParentObj();
        }

        $set = DataList::create($this->subjectClass);
        if ($this->parentObj) {
            $set = $set->filter(array(
                $this->parentField => $this->parentObj->ID
            ));
        }
        foreach ($set as $obj) {
            $obj->fill(array(
                'only_empty' => $this->onlyEmpty,
                'include_relations' => $this->includeRelations,
                'download_images' => $this->downloadImages,
                'relation_create_limit' => $this->relationCreateLimit
            ));

            if ($this->isSiteTree) {
                $obj->write();
                $obj->publish("Stage", "Live");
            }

            $this->log("Updated {$this->subjectClass} \"{$obj->getTitle()}\".");
        }
    }



    /**
     * Given a {@link $parentIdentifier} value, figure out what the parent record is.
     * Parent identifier could be a numeric ID or a URLSegment.
     */
    protected function determineParentObj()
    {
        $parent = $this->parentIdentifier;
        $parentPage = SiteTree::get()->byID((int) $parent);
        if (!$parentPage) {
            $parentPage = SiteTree::get_by_link($parent);
        }
        if (!$parentPage) {
            $parentPage = SiteTree::get()->filter(array('Title' => trim($parent)))->first();
        }
        if (!$parentPage) {
            throw new Exception("Could not find a page with ID, URLSegment, or Title \"$parent\"");
        }
        if (!Injector::inst()->get($this->subjectClass)->hasField($this->parentField)) {
            throw new Exception("{$this->subjectClass} has no field {$this->parentField}.");
        }

        $this->parentObj = $parentPage;
        $this->log("Parent page is #{$parentPage->ID} {$parentPage->getTitle()}");
    }



    /**
     * Sets the parent object that will own the created mock records
     *
     * @param DataObject
     * @return MockDataBuilder
     */
    public function setParentObj(DataObject $obj)
    {
        $this->parentObj = $obj;
        return $this;
    }



    /**
     * Sets the foreign key field for the created records, e.g. "MyPageHolderID"
     *
     * @param string The field name
     * @param MockDataBuilder
     */
    public function setParentField($field)
    {
        $this->parentField = $field;
        return $this;
    }



    /**
     * Sets the parent identifier for the parent record. Could be a numeric ID or URLSegment
     *
     * @param mixed The identifier
     * @param MockDataBuilder
     */
    public function setParentIdentifier($id)
    {
        $this->parentIdentifier = $id;
        return $this;
    }



    /**
     * Sets the number of records to create
     *
     * @param int
     * @param MockDataBuilder
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }



    /**
     * If set to true, only populate fields that are empty
     *
     * @param boolean
     * @param MockDataBuilder
     */
    public function setOnlyEmpty($bool)
    {
        $this->onlyEmpty = (bool) $bool;
        return $this;
    }



    /**
     * Sets the foreign key field for the created records, e.g. "MyPageHolderID"
     *
     * @param string The field name
     * @param MockDataBuilder
     */
    public function setRelationCreateLimit($num)
    {
        $this->relationCreateLimit = $num;
        return $this;
    }



    /**
     * If set to true, download images from the web to populate file relatiosn
     *
     * @param boolean
     * @param MockDataBuilder
     */
    public function setDownloadImages($bool)
    {
        $this->downloadImages = (bool) $bool;
        return $this;
    }


    /**
     * If set to true, populate has_many and many_many relations
     *
     * @param boolean
     * @param MockDataBuilder
     */
    public function setIncludeRelations($bool)
    {
        $this->includeRelations = (bool) $bool;
        return $this;
    }




    /**
     * Logs a message. Either output to console or store internally
     *
     * @param string The message
     */
    protected function log($msg)
    {
        if (Director::is_cli()) {
            echo "$msg\n";
        } else {
            $this->log[] = $msg;
        }
    }
}
