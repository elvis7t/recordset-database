# PHP Database Manager RecordSet

This is a simple library for managing database connections and building queries in PHP.

## Usage

To use this library just follow the examples below:

#### Database
```php
<?php

require 'vendor/autoload.php';

use RecordSetDatabase

//DATABASE CREDENTIALS IN CONNECTION CLASS
DB_HOSTNAME = 'localhost';
DB_USERNAME = 'database';
DB_PASSWORD = 'root';
DB_DATABASE = 'pass';
DB_CHARSET = 'utf8'


//TABLE INSTANCE
$rs = new Recordset();

//SELECT (return a PDOStatement object)
$results = $rs->select('table','id > 10','name ASC','1','*');

//INSERT (return inserted id)
$id = $rs->insert([
  'name' => 'Elvis'
]);

//UPDATE (return a bool)
$success = $rs->update('table','id = 1',[
  'name' => 'Elvis Leite'
]);

//DELETE (return a bool)
$success = $rs->delete('table''id = 1');

//EXECUTE QUERY
$sql = "select *from tabble";
$rs->Execute($sql);

//DATA GENERATOR
<?php
$rs->Select('table');
while($rs->DataGeneretor){
}
?>
<tr><td><?=rs->fld('table_fild')?></td></tr>
<?php
}
?>

//DATA GENERATOR WHITH FORMAT DATE
<?php
$rs->Select('table');
while($rs->DataGeneretor){
}
?>
<tr><td><?=rs->formFld('table_fild')?></td></tr>
<?php
}
?>
return <tr><td>25/10/2022 às 13:01:21</td></tr>

## Requirements

This library needs PHP 7.0 or greater.
