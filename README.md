# PHP Database Manager

This is a simple library for managing database connections, results pagination and building queries in PHP.

## Usage

To use this library just follow the examples below:

#### Database
```php
<?php

require 'vendor/autoload.php';

use RecordSetDatabase

//DATABASE CREDENTIALS
$dbHost = 'localhost';
$dbName = 'database';
$dbUser = 'root';
$dbPass = 'pass';
$dbPort = 3306;

//CONFIG DATABASE CLASS
Database::config($dbHost,$dbName,$dbUser,$dbPass,$dbPort);

//TABLE INSTANCE
$obDatabase = new Database('table_name');

//SELECT (return a PDOStatement object)
$results = $obDatabase->select('id > 10','name ASC','1','*');

//INSERT (return inserted id)
$id = $obDatabase->insert([
  'name' => 'William Costa'
]);

//UPDATE (return a bool)
$success = $obDatabase->update('id = 1',[
  'name' => 'William Costa2'
]);

//DELETE (return a bool)
$success = $obDatabase->delete('id = 1');

```

## Requirements

This library needs PHP 7.0 or greater.
