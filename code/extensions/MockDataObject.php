<?php


/**
 * Injects functionality into every {@link DataObject} subclass to populate its
 * database fields and data relations with mock data.
 *
 * @package silverstripe-mock-dataobjects
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MockDataObject extends DataExtension
{


    /**
     * Stores an instance of a {@link MockViewableData} object
     * @var MockViewableData
     */
    protected $fakeInstance;

    private static $mock_blacklist = array();

    /**
     * An accessor to get all of the stock files that ship with the package
     *
     * @return DataList
     */
    public static function get_mock_files()
    {
        return File::get()->filter("ParentID", self::get_mock_folder()->ID);
    }



    /**
     * An accessor to get the folder where all of the stock files are stored
     *
     * @return Folder
     */
    public static function get_mock_folder()
    {
        return Folder::find_or_make("mock-files");
    }



    /**
     * Copies stock files from the module directory to the filesytem and updates the database.
     */
    public static function install_mock_files()
    {
        $sample_path = Director::baseFolder().'/'.MOCK_DATAOBJECTS_DIR.'/lib';
        $sample_files = glob($sample_path.'/*.jpeg');
        $folder = self::get_mock_folder();
        $installed_sample_files = self::get_mock_files();
        if (sizeof($sample_files) <= $installed_sample_files->count()) {
            return;
        }

        foreach ($sample_files as $file) {
            copy($file, $folder->getFullPath().basename($file));
        }
        $folder->syncChildren();
    }



    /**
     * Downloads a random image from a public website and installs it into the filesystem
     *
     * @todo This should really be an injectable service. It locks the user into a specific URL.
     * @return Image
     */
    public static function download_lorem_image()
    {
        $url = 'http://lorempixel.com/1024/768?t='.uniqid();
        $img_filename = "mock-file-".uniqid().".jpeg";

        $img = self::get_mock_folder()->getFullPath().$img_filename;

        if (ini_get('allow_url_fopen')) {
            file_put_contents($img, file_get_contents($url));
        } else {
            $ch = curl_init($url);
            $fp = fopen($img, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }

        if (!file_exists($img) || !filesize($img)) {
            return false;
        }

        $i = Image::create();
        $i->Filename = self::get_mock_folder()->Filename.$img_filename;
        $i->Title = $img_filename;
        $i->Name = $img_filename;
        $i->ParentID = self::get_mock_folder()->ID;
        $i->write();

        return $i;
    }



    /**
     * Gets a random image that already exists in the filesystem
     * @return File
     */
    public static function get_random_local_image()
    {
        self::install_mock_files();
        return self::get_mock_files()->sort(DB::get_conn()->random())->first();
    }



    /**
     * Template accessor for {@link MockViewableData}
     * @return  MockViewableData
     */
    public function Fake()
    {
        return $this->fakeInstance ? $this->fakeInstance : ($this->fakeInstance = MockViewableData::create());
    }



    /**
     * Populates all of the native database fields and optionally fills in data relations.
     * Accepts an array of settings, ex:
     *
     * array(
     *	'only_empty' => true, // only fill in empty fields
     *	'include_relations' => true, // Include has_many and many_many relations
     *	'relation_create_limit' => 5, // If there aren't any existing records for many_many or has_one relations, limit creation to this number
     *	'download_images' => false, // Don't download images from the web. Use existing.
     * );
     *
     * @param array $config The configuration options
     * @return DataObject
     */
    public function fill($config = array())
    {
        $faker = Faker\Factory::create(i18n::get_locale());
        $defaults = Config::inst()->get("MockDataObject", "fill_options");
        $create_limit = Config::inst()->get("MockDataObject", "relation_create_limit");
        $settings = array_merge($defaults, $config);

        // Anything that is a core SiteTree field, e.g. "URLSegment", "ShowInMenus", "ParentID",  we don't care about.
        $omit = Injector::inst()->get("SiteTree")->db();
        $omit = array_merge($omit, $this->owner->config()->mock_blacklist);

        // Except these two.
        unset($omit['Title']);
        unset($omit['Content']);

        $db = $this->owner->db();

        foreach ($db as $fieldName => $fieldType) {
            if (in_array($fieldName, $omit)) {
                continue;
            }
            if ($settings['only_empty'] && $this->owner->obj($fieldName)->exists()) {
                continue;
            }
            $value = $this->owner->obj($fieldName)->getFakeData($faker);
            $this->owner->setField($fieldName, $value);
        }


        foreach ($this->owner->has_one() as $relation => $className) {
            $idField = $relation."ID";
            $sitetree = ($className == "SiteTree") || (is_subclass_of($className, "SiteTree"));
            if ($sitetree && $relation == "Parent") {
                continue;
            }
            $create_limit = Config::inst()->get("MockDataObject", "relation_create_limit");

            if (($className == "File") || (is_subclass_of($className, "File"))) {
                if ($settings['only_empty'] && $this->owner->$relation()->exists()) {
                    continue;
                }
                if ($settings['download_images']) {
                    if ($image = self::download_lorem_image()) {
                        $this->owner->$idField = $image->ID;
                    }
                } else {
                    if ($random_file = self::get_random_local_image()) {
                        $this->owner->$idField = $random_file->ID;
                    }
                }
            } elseif ($className == "Subsite") {
                continue;
            } else {
                $random_record = DataList::create($className)->sort(DB::get_conn()->random())->first();
                if (!$random_record && !$sitetree) {
                    $i = 0;
                    while ($i <= $create_limit) {
                        $r = new $className();
                        $r->fill($settings);
                        $r->write();
                        $random_record = $r;
                        $i++;
                    }
                }
                $this->owner->$idField = $random_record->ID;
            }
        }

        $this->owner->write();

        if ($settings['include_relations']) {
            $SNG = Injector::inst()->get("SiteTree");
            $skip = array_merge(array_keys($SNG->has_many()), array_keys($SNG->many_many()));
            foreach ($this->owner->has_many() as $relation => $className) {
                if (in_array($relation, $skip)) {
                    continue;
                }
                $idField = Injector::inst()->get($className)->getReverseAssociation($this->owner->class);
                if (!$idField) {
                    continue;
                }
                $idField.="ID";

                $count = rand(1, 10);
                $i = 0;
                while ($i <= $count) {
                    $r = new $className();
                    $r->fill($settings);
                    $r->$idField = $this->owner->ID;
                    $r->write();
                    $i++;
                }
            }

            foreach ($this->owner->many_many() as $relation => $className) {
                if (in_array($relation, $skip)) {
                    continue;
                }
                $records = DataList::create($className)->limit($create_limit);
                $diff = $records->count() - $create_limit;
                while ($diff < 0) {
                    $r = new $className();
                    $r->fill($settings);
                    $r->write();
                    $diff++;
                }
                $random_records = DataList::create($className)->sort(DB::get_conn()->random())->limit(rand(0, $create_limit));
                $this->owner->$relation()->setByIDList($random_records->column('ID'));
            }
        }



        // Create a record of this mock data so that we can delete it later
        $log = MockDataLog::create();
        $log->RecordClass = $this->owner->ClassName;
        $log->RecordID = $this->owner->ID;
        $log->write();

        return $this->owner;
    }



    /**
     * Cleans up the {@link MockDataLog} records. This is kind of expensive to attach to every
     * DataObject for every delete, but fortunately this module is never used in production.
     */
    public function onBeforeDelete()
    {
        $log = MockDataLog::get()->filter(array(
            "RecordClass" => $this->owner->ClassName,
            "RecordID" => $this->owner->ID
        ))->first();
        if ($log) {
            $log->delete();
        }
    }
}
