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

//Generating data with the Select method
$rs = new RecosdSet();
$rs->select('table_name','where_clause','order_clause','limit_clause');
while($rs->DataGenerator(){
?>
<tr><td><?=$rs->fld('table_fild')?></td></tr>
<?php
}
?>

//Generating data with the Quary
$rs = new RecosdSet();
$sql = "select * from table_name";
$rs->Execute($sql);
while($rs->DataGenerator()){
?>
<tr><td><?=$rs->fld('table_fild')?></td></tr>
<?php
}

//DATA GENERATOR WHITH FORMAT DATE
<?php
$rs = new RecosdSet();
$rs->Select('table_name');
while($rs->DataGenerator()){
?>
<tr><td><?=$rs->formFld('table_fild')?></td></tr>
<?php
}
?>
return <tr><td>25/10/2022 às 13:01:21</td></tr>

//INSERT 
$rs = new RecosdSet();
$id = $rs->insert([
  'name' => 'Elvis'
], 'user');

//UPDATE (return a bool)
$rs = new RecosdSet();
$rs->update(['name' => 'Elvis Leite'], 'table_name','id = 1');

//DELETE (return a bool)
$rs->Delete('user','id = 4');



## Requirements

This library needs PHP 8.0 or greater.
