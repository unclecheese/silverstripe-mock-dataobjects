# Mock DataObjects for SilverStripe

This module provides intelligent content generation functionality to all DataObjects. The object introspects its fields and assigns an example value based on the field type and the name of the field. It also provides a command line utility for generating mock data programatically as well as various UI features in the CMS to support the creating and populating DataObjects.

## Installation
Installation via Composer is highly recommended, as this module has external dependencies.

```
composer require unclecheese/mock-dataobjects:dev-master
```

## Example
```php
class StaffMember extends DataObject {

	private static $db = array (
		'FirstName' => 'Varchar(255)',
		'LastName' => 'Varchar(255)',
		'EmailAddress' => 'Varchar(255)',
		'Address' => 'Varchar(255)',
		'City' => 'Varchar(255)',
		'PostalCode' => 'Varchar(255)',
		'Country' => 'Varchar(255)',
		'Company' => 'Varchar(255)',
		'Website' => 'Varchar(255)',
		'PhoneNumber' => 'Varchar(255)',
	);


	private static $has_one = array (
		'Photo' => 'Image',
		'StaffHolder' => 'StaffHolder',
	);
}
```

```php
$staff = new StaffMember();
$staff->fill();
```

Result:


![Screenshot](http://i.cubeupload.com/bvMv42.png)

## Implementation
You can use the features of the MockDataObjects module in many ways, including executable code, a command-line interface, and from within the CMS.

### From the CMS
#### Adding mock children to a parent page:

Right click on the parent page and choose "Add mock children."

![Screenshot](http://i.cubeupload.com/F12GDf.png)

Choose options, and create

![Screenshot](http://i.cubeupload.com/f0JaZW.png)

#### Adding items to a grid
Just click on "add mock data" and set your options.

![Screenshot](http://i.cubeupload.com/MK0LMj.png)

#### Populating existing records
Click on "fill with mock data"

![Screenshot](http://i.cubeupload.com/ZKnUfa.png)

### In the execution pipeline

```php
$myDataObject->fill();
```
As demonstrated above, the ->fill() method populates a DataObject with mock data. There are a few options you can pass to this method.
```php
$myDataObject->fill(array(
	 'only_empty' => true, // only fill in empty fields
	 'include_relations' => true, // Include has_many and many_many relations
	 'relation_create_limit' => 5, // If no existing records for many_many or has_one relations, limit creation
	 'download_images' => false, // Don't download images from the web. Use existing.
));
```

### From the command line
Create 50 new records. Use existing files for file relationships.
```
mockdata generate Product -count 50 --no-downloads
```

Populate existing records with new data.
```
mockdata populate Product
```

Add new records to the has_many relation on a given page.
```
mockdata generate StaffMember -parent-field StaffPageID -parent "our-staff"
```

## Localisation

Mock data values are localised to the current locale as defined by ```i18n::get_locale()```.
```php
i18n::set_locale('en_US');
$staff = new StaffMember();
$staff->fill();
echo $staff->PhoneNumber; // (102) 806-3915

i18n::set_locale('fr_FR');
$staff = new StaffMember();
$staff->fill();
echo $staff->PhoneNumber; // +33 8 17 54 64 62
```

## Field name hooks

For generic database fields, such as Varchar, the mock data generator is informed by the field name in order to create more realistic data. These hooks are defined in the language file.

```
en:
  MockDataObject:
    FIRSTNAME: "Firstname, FirstName"
    LASTNAME: "Surname, LastName, Lastname"
    FULLNAME: "FullName"
    CITY: "City, Town"
    STATE: "State"
    ADDRESS: "Address, Address1, Address2"
    POSTCODE: "Zip, Zipcode, ZipCode"
    COUNTRYCODE: "Country, CountryCode"
    PHONENUMBER: "Phone, PhoneNumber, Fax, Cell, Mobile, Telephone, Phonenumber"
    EMAIL: "Email, EmailAddress"
    COMPANY: "Company, CompanyName, Organization"
    URL: "URL, Website, URI"
    LATITUDE: "Latitude, Lat"
    LONGITUDE: "Longitude, Lon"
```

A comma-separated list of possible field names are mapped to an entity, so that a field named "EmailAddress" or "Email" creates a fake email address, and "Phone" or "Telephone" creates a fake phone number.

An example language file for French might look like this:

```
fr:
  MockDataObject:
    FIRSTNAME: "Prenom"
    LASTNAME: "NomDeFamille, Nom"
    CITY: "Ville"
```


## Model-independent data generation

Sometimes it is useful to generate mock data before the model has been created, such as when frontend development is happening before backend development. For that purpose, every DataObject comes with a ```$Fake``` method to access the fake data generator.


```html
<h2>$Fake.Words</h2>
$Fake.Paragraphs(2,5) <!-- min, max -->
$Fake.Image.SetWidth(100)

<h3>What we can do for you</h3>
<ul>
  <% loop $Fake.Loop %>
    <li>$Fake.Number $Fake.Words</li>
  <% end_loop %>
</ul>

<h3>Contact Us</h3>
$Fake.Company<br />
$Fake.FullName<br />
$Fake.Address<br />
$Fake.Address<br />
$Fake.City, $Fake.State $Fake.PostalCode<br />
$Fake.PhoneNumber<br />
<a href="mailto:$Fake.Email">$Fake.Email</a>

<h3>Find us on a map!</h3>

$Fake.Latitude, $Fake.Longitude
```

## Cleaning up

Records of all mockdata creation are stored in the ```MockDataLog``` table, which maps a record ID to a class name. You can clean this table using the task ```dev/tasks/MockDataTask cleanup <classname>```. To clear all mock data, leave the class name argument null.

**Be very careful about converting mock data records into authentic records, as this task will clean them up without knowing that you have populated them with valid data!**

## Troubleshooting

Just ring Uncle Cheese.
