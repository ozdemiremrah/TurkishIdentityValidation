# TurkishIdentityValidation

Validator for Turkish Republic's citizen ID numbers (TC Kimlik No).

It's supports id document validation.

Verify an id number:

```php
<?php
require_once("TurkishIdentityValidation.php");

$validator = new TurkishIdentityValidation();

$onlyAlgorithmValidation = $validator->IdValidation(34720987280);
if($onlyAlgorithmValidation)
	echo "Id number is correct.";
else
	echo "Incorrect id number";
```

Verify a person basic info:
 // id number, name, surname, birth year

```php
<?php
require_once("TurkishIdentityValidation.php");

$validator = new TurkishIdentityValidation();

 $data = [ 
		"identity" => "34720987280"
		"name" => "Emrah" 
		"surname" => "Özdemir"
		"year" => 1995
	];

$basicValidation = $validator->IdentityVerification($data);
if($basicValidation)
	echo "Person data is correct.";
else
	echo "Incorrect data";
```

Verify a person id document:
 // id number, document serial, document no, name, surname, birth date

```php
<?php
require_once("TurkishIdentityValidation.php");

$validator = new TurkishIdentityValidation();

$data = [
		"type" => 1, // old identity document
		"identity" => "12345678901",
		"name" => "Emrah",
		"surname" => "Özdemir",
		"day" => "01",
		"month" => "01",
		"year" => "1995",
		"document_serial" => "A01",
		"document_no" => "415496"
	];

	// or identity card

$data = [
	"type" => 2, // identity card
	"identity" => "12345678901",
	"name" => "Emrah",
	"surname" => "Özdemir",
	"day" => "01",
	"month" => "01",
	"year" => "1995",
	"document_serial" => "A01"
];

$documentValidation = $validator->IdentityDocumentVerification($data);
if($documentValidation)
	echo "Person data is correct.";
else
	echo "Incorrect data";
```