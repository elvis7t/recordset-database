# PHP Database Manager RecordSet

This is a simple library for managing database connections and building queries in PHP.

## Usage

To use this library just follow the examples below:

#### Database
```php
<?php

// Include the library's autoload file
require 'vendor/autoload.php';

// Import the RecordSetDatabase class
use RecordSetDatabase\RecordSetDatabase;

// Database Credentials
$DB_HOSTNAME = 'localhost';
$DB_USERNAME = 'database';
$DB_PASSWORD = 'root';
$DB_DATABASE = 'pass';
$DB_CHARSET = 'utf8';

// Creating a RecordSetDatabase instance
$rs = new RecordSetDatabase();

// Generating data with the Select method
$rs->Select('table_name', 'where_clause', 'order_clause', 'limit_clause');

while ($rs->DataGenerator()) {
    echo '<tr><td>' . $rs->fld('table_field') . '</td></tr>';
}

// Generating data with a Query
$rs = new RecordSetDatabase();
$sql = "select * from table_name";
$rs->Execute($sql);

while ($rs->DataGenerator()) {
    echo '<tr><td>' . $rs->fld('table_field') . '</td></tr>';
}

// Data Generator with DATE formatting
$rs = new RecordSetDatabase();
$rs->Select('table_name');

while ($rs->DataGenerator()) {
    echo '<tr><td>' . $rs->formFld('table_field') . '</td></tr>';
}

// This would return: <tr><td>25/10/2022 às 13:01:21</td></tr>

// INSERT data
$rs = new RecordSetDatabase();
$data = [
        'id' => (string) $rs->setAutoCode('id', 'user'),
        'name' => 'Tatys',
        'mail' => 'tatiss@gmail.com',
        'pass' => 's$2y$10$zKdjHmKbmJ6GVOIrApOiTO5sOpZSZkbHiscY9Kab/CnsKF.2dVt3S'
    ];
$rs->Insert($data, 'user');

// UPDATE 
$rs = new RecordSetDatabase();
$result = $rs->Update(['name' => 'Elvis Leite'], 'table_name', 'id = 1');

// DELETE 
$result = $rs->Delete('user', 'id = 4');

// Get Field
$rs = new RecordSet();
echo $rs->getField('id', 'user', 'id = 1');

?>
