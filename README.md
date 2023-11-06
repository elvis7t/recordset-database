# PHP Database Manager RecordSet

This is a simple library for managing database connections and building queries in PHP.

## Usage

To use this library just follow the examples below:

#### Database
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
$rs->select('table_name', 'where_clause', 'order_clause', 'limit_clause');

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
$id = $rs->insert([
    'name' => 'Elvis'
], 'user');

// UPDATE (returns a bool)
$rs = new RecordSetDatabase();
$result = $rs->update(['name' => 'Elvis Leite'], 'table_name', 'id = 1');

// DELETE (returns a bool)
$result = $rs->Delete('user', 'id = 4');

?>

Improved PHP Code for Database Operations
This code sample demonstrates how to use the RecordSetDatabase library to perform various database operations. Here's an overview of what each part of the code does:

Include the Library: You need to include the library's autoload file to access its functionality.

Import the RecordSetDatabase Class: Use the use statement to import the RecordSetDatabase class for easier access.

Database Credentials: Set up your database credentials (host, username, password, database name, and charset) in the provided variables.

Select Method: Use the select method to fetch data from a table based on conditions like a where clause, order by clause, and limit clause. Iterate over the data using the DataGenerator method and display the results.

Query Method: Execute custom SQL queries with the Execute method and retrieve results in a similar way.

Data Generator with DATE Formatting: Format date values with the formFld method and display the results.

INSERT Data: Insert a new record into the database and receive the inserted record's ID.

UPDATE Data: Update records in the database and get a boolean result indicating success or failure.

DELETE Data: Delete records from the database and get a boolean result indicating success or failure.

Requirements
This library requires PHP 8.0 or greater to function correctly. Make sure you meet this requirement before using the code.
