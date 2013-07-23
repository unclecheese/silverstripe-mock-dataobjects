<?php 


class MockDataObject extends DataExtension {


	public static function get_mock_files() {
		return File::get()->filter("ParentID", self::get_mock_folder()->ID);
	}


	public static function get_mock_folder() {
		return Folder::find_or_make("mock-files");
	}


	public static function install_mock_files() {        
        $sample_path = Director::baseFolder().'/'.MOCK_DATAOBJECTS_DIR.'/lib';
        $sample_files = glob($sample_path.'/*.jpeg');
        $folder = self::get_mock_folder();
        $installed_sample_files = self::get_mock_files();                        
        if(sizeof($sample_files) <= $installed_sample_files->count()) return;
        
        foreach($sample_files as $file) {
        	copy($file, $folder->getFullPath().basename($file));
        }
        $folder->syncChildren();
	}


	public static function download_lorem_image() {
		$url = 'http://lorempixel.com/1024/768?t='.uniqid();
		$img_filename = "mock-file-".uniqid().".jpeg";

		$img = self::get_mock_folder()->getFullPath().$img_filename;
		
		if(ini_get('allow_url_fopen')) {									
			file_put_contents($img, file_get_contents($url));			
		}
		else {
			$ch = curl_init($url);
			$fp = fopen($img, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);			
		}

		if(!file_exists($img) || !filesize($img)) return false;

		$i = Image::create();
		$i->Filename = self::get_mock_folder()->Filename.$img_filename;
		$i->Title = $img_filename;
		$i->Name = $img_filename;
		$i->ParentID = self::get_mock_folder()->ID;		
		$i->write();

		return $i;
	}


	public function fill($config = array ()) {
		$faker = Faker\Factory::create();
		$defaults = Config::inst()->get("MockDataObject", "fill_options");
		$settings = array_merge($defaults, $config);
		foreach($this->owner->db() as $fieldName => $fieldType) {
			if($settings['only_empty'] && $this->owner->obj($fieldName)->exists()) continue;
			
			$value = $this->owner->obj($fieldName)->getFakeData($faker);
			$this->owner->$fieldName = $value;

		}


		foreach($this->owner->has_one() as $relation => $className) {
			$idField = $relation."ID";
			$sitetree = ($className == "SiteTree") || (is_subclass_of($className, "SiteTree"));
			$create_limit = Config::inst()->get("MockDataObject","relation_create_limit");

			if( ($className == "File") || (is_subclass_of($className, "File"))) {				
				if($settings['only_empty'] && $this->owner->$relation()->exists()) continue;
				if($settings['download_images']) {
					if($image = self::download_lorem_image()) {
						$this->owner->$idField = $image->ID;
					}
				}
				else {					
					self::install_mock_files();
					if($random_file = self::get_mock_files()->sort("RAND()")->first()) {
						$this->owner->$idField = $random_file->ID;
					}
				}				
			}
			else {
				$random_record = DataList::create($className)->sort("RAND()")->first();
				if(!$random_record && !$sitetree) {		
					$i = 0;					
					while($i <= $create_limit) {
						$r = new $className();
						$r->fill();
						$r->write();
						$random_record = $r;
						$i++;
					}
				}
				$this->owner->$idField = $random_record->ID;
			}
		}

		$this->owner->write();

		if($settings['include_relations']) {
			$SNG = Injector::inst()->get("SiteTree");
			$skip = array_merge(array_keys($SNG->has_many()), array_keys($SNG->many_many()));
			foreach($this->owner->has_many() as $relation => $className) {
				if(in_array($relation, $skip)) continue;
				$idField = Injector::inst()->get($className)->getReverseAssociation($this->owner->class);
				if(!$idField) continue;
				$idField.="ID";

				$count = rand(1, 10);
				$i = 0;
				while($i <= $count) {
					$r = new $className();
					$r->fill();					
					$r->$idField = $this->owner->ID;
					$r->write();
					$i++;
				}
			}
			
			foreach($this->owner->many_many() as $relation => $className) {
				if(in_array($relation, $skip)) continue;			
				$records = DataList::create($className)->limit($create_limit);
				$diff = $records->count() - $create_limit;
				while($diff < 0) {
					$r = new $className();
					$r->fill();
					$r->write();
					$diff++;
				}
				$random_records = DataList::create($className)->sort("RAND()")->limit(rand(0,$create_limit));
				$this->owner->$relation()->setByIDList($random_records->column('ID'));
			}

		}


		$log = MockDataLog::create();
		$log->RecordClass = $this->owner->ClassName;
		$log->RecordID = $this->owner->ID;
		$log->write();

		return $this->owner;

	}



	public function onBeforeDelete() {
		$log = MockDataLog::get()->filter(array(
			"RecordClass" => $this->owner->ClassName,
			"RecordID" => $this->owner->ID
		))->first();
		if($log) $log->delete();
	}


}