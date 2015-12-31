<?php


/**
 * Defines the task that creates, populates, or cleans up mock data.
 *
 * ex:
 * /dev/tasks/MockDataTask?args[]=generate&args[]=MyDataObject&count=10
 * /dev/tasks/MockDataTask?args[]=cleanup&args[]=MyDataObject
 *
 * For command line usage, use the "mockdata" executable contained in the root
 * of the module directory.
 *
 * mockdata generate MyDataObject -count 10
 * mockdata populate MyDataObject --no-downloads
 * mockdata cleanup
 *
 * @package silverstripe-mock-dataobjects
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockDataTask extends BuildTask
{


    /**
     * @var string The title of the task
     */
    protected $title = "Generate or populate records with mock data";



    /**
     * @var SS_HTTPRequest
     */
    protected $request;



    /**
     * Runs the task
     *
     * @param SS_HTTPRequest
     */
    public function run($request)
    {
        $this->request = $request;
        $args = $request->getVar('args');

        // The "cleanup" task has a different argument signature. Because it doesn't require the specification of a class.
        // This block normalizes that.
        if ($args[0] == "cleanup") {
            if (!isset($args[1])) {
                $args[1] = "__all__";
            }
            if ($args[1] != "__all__") {
                if (!class_exists($args[1]) || !is_subclass_of($args[1], "DataObject")) {
                    $this->showError("Please specify a valid DataObject descendant class.");
                }
            }

            return $this->cleanup($args[1]);
        } else {
            if (count($args) < 2) {
                $this->showError("Usage: MockDataTask <generate|populate|cleanup> <classname> [options]");
            }

            list($operation, $className) = $args;

            if (!class_exists($className) || !is_subclass_of($className, "DataObject")) {
                $this->showError("Please specify a valid DataObject descendant class.");
            }

            if (!in_array($operation, array('generate', 'populate', 'cleanup'))) {
                $this->showError("Please specify a valid operation (\"generate\", \"populate\", or \"cleanup\")");
            }


            $this->runBuilderCommand($operation, $className);
        }
    }



    /**
     * Runs a command on the {@link MockDataBuilder} object using options
     * defined in the request.
     *
     * @param string The command to run
     * @param string The class to create or update
     */
    protected function runBuilderCommand($cmd, $className)
    {
        $count = $this->request->getVar('count') ?: 10;
        $parent = $this->request->getVar('parent');
        $parentField = $this->request->getVar('parentField') ?: "ParentID";

        try {
            $builder = MockDataBuilder::create($className);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }

        $builder
            ->setOnlyEmpty($this->request->getVar('onlyEmpty') === "false" ? false : true)
            ->setDownloadImages($this->request->getVar('downloadImages') === "false" ? false : true)
            ->setCount($count)
            ->setParentIdentifier($parent ?: null)
            ->setParentField($parentField)
        ;

        try {
            $builder->$cmd();
        } catch (Exception $e) {
            echo $e->getMessage()."\n\n";
            die();
        }
    }



    /**
     * Deletes mock data records using references in {@link MockDataLog}
     *
     * @param string The class of records to delete
     */
    protected function cleanup($className)
    {
        $classes = ($className == "__all__") ? MockDataLog::get()->column('RecordClass') : array($className);
        foreach ($classes as $recordClass) {
            $logs = MockDataLog::get()->filter(array('RecordClass' => $recordClass));
            $ids = $logs->column('RecordID');
            $list = DataList::create($recordClass)->byIDs($ids);
            $this->writeOut("Deleting " . $list->count() . " $recordClass records");
            $list->removeAll();
            $this->writeOut("Done.");
            $logs->removeAll();
        }
    }



    /**
     * Present an error to the client
     *
     * @param string The error message
     */
    protected function showError($msg)
    {
        echo $msg."\n\n";
        die();
    }




    /**
     * Present a message to the client
     *
     * @param string The message
     */
    protected function writeOut($msg)
    {
        echo $msg."\n";
    }
}
