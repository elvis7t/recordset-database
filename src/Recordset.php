<?php

namespace ElvisLeite\RecordSetDatabase;

use ElvisLeite\RecordSetDatabase\Connection;
use ElvisLeite\RecordSetDatabase\Formatter;

class Recordset
{
	/**
	 * Number rows	 
	 * @var int
	 */
	private static $numRows;

	/**
	 * Result 	 
	 * @var string
	 */
	private static $result;

	/**	 
	 *Registres
	 * @var array
	 */
	private static $regs;

	/**
	 * Link of the connection
	 * @var mysqli
	 */
	private static $link;

	/**
	 * Method responsible for create connection to database
	 */
	function __construct()
	{
		// Set connection and store it in self::$link
		self::$link = Connection::setConnect();
	}

	/**
	 * Method responsible for runing the sql query
	 * @param string $sql
	 * @return void	 
	 */
	public function Execute(string $sql): void
	{
		if (!mysqli_query(self::$link, $sql)) {
			throw new Exception(mysqli_error(self::$link));
		}
		self::$result = mysqli_query(self::$link, $sql);
	}


	/**
	 * Method responsible for generating the datas	 
	 * @return mixed 
	 */
	public function DataGenerator(): mixed
	{
		return self::$regs = mysqli_fetch_array(self::$result);
		//CLOSE CONNECTION			
		Connection::setDesconnect();
	}

	/**
	 * Method responsible for the number of rows in the table
	 * @param string $sql
	 * @return int	 
	 */
	public function getCountRows(string $sql): int
	{
		self::$result = mysqli_query(self::$link, $sql);

		//RETURN NUMBER OF TABLE ROWS
		return self::$numRows  = mysqli_num_rows(self::$result);
	}

	/**
	 * Method responsible for selectioning the table's filds	
	 * @param mixed $field
	 * @return mixed
	 */
	public function fld(mixed $field): mixed
	{
		return self::$regs[$field];
	}

	/**
	 * Method responsible for handling the date field in the format date: time Brazil
	 * @param string $field
	 * @return string
	 */
	public function formFld(string $field): string
	{
		return Formatter::setTimeDate(self::fld($field));
	}

	/**
	 * Method responsible for handling the date field in the format date: time Brazil
	 * @param string $field
	 * @return string
	 */
	public static function formMonthFld($field): string
	{
		return Formatter::setMonthformat($field);
	}

	/**
	 * Method responsible for Insert data into the table
	 * @param mixed $values
	 * @param string $table
	 * @return void
	 */
	public function Insert(mixed $values, string $table): void
	{
		// Validate table name
		if (!$table) {
			die('Invalid table specified');
		}
		// Sanitize input
		foreach ($values as &$value) {
			$value = htmlspecialchars(preg_replace('/[^A-Za-z0-9\-]/', '', $value), ENT_QUOTES);
		}
		$fields = array_keys($values);
		$sql = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES ('" . implode("','", $values) . "')";
		self::Execute($sql);
	}


	/**	 
	 *Method responsible for selectioning the datas
	 * @param string $table
	 * @param string $where
	 * @param string $order
	 * @param string $limit
	 * @param string $fields
	 * @return string
	 */
	public function Select(string $table, string $where = null, string $order = null, string $limit = null, string $fields = '*')
	{
		$where = !is_null($where) ? 'WHERE ' . self::$link->real_escape_string($where) : '';
		$order = !is_null($order) ? 'ORDER BY ' . self::$link->real_escape_string($order) : '';
		$limit = (!is_null($limit) && (strlen($limit) < 256)) ? 'LIMIT ' . self::$link->real_escape_string($limit) : '';
		$sql = "SELECT $fields FROM $table $where $order $limit";

		return self::$result = mysqli_query(self::$link, $sql);
	}


	/**	 
	 * Method responsible for updating the datas
	 * @param mixed $filds
	 * @param string $table
	 * @param string $where
	 * @return void
	 */
	public function Update(mixed $filds, string $table, string $where): void
	{
		if ($filds === null) {
			return;
		}
		$setFields = [];
		foreach ($filds as $field => $value) {
			if (is_string($value)) {
				$setFields[] = $field . '="' . $value . '"';
			} else {
				$setFields[] = $field . '='  . $value;
			}
		}
		$sql = "UPDATE $table SET " . implode(', ', $setFields) . ' WHERE ' . $where;
		self::Execute($sql);
	}


	/**
	 * Method responsible for deleting the data
	 * @param string $table	
	 * @param string $whr
	 * @return void
	 */
	public function Delete(string $table, string $whr): void
	{
		$sql = "DELETE FROM $table WHERE $whr";

		// RUN SQL		
		self::Execute($sql);
	}

	/**
	 * Method responsible for selectioning a fild of the table
	 * @param string $table
	 * @param string $where
	 * @param mixed $field
	 * @return string
	 */
	public function getField(string $fieldname, string $tablename, string $whereClause): string
	{
		self::Select($tablename, $whereClause);
		self::DataGenerator();
		return self::fld($fieldname);
	}


	/**
	 * Method responsible for generated a auto-increment on the table
	 * @param mixed $fild
	 * @param string $table
	 * @return int $cod
	 */

	public function setAutoCode(mixed $fild, string $table): int
	{
		self::Execute("SELECT " . $fild . " FROM " . $table . " ORDER BY " . $fild . " DESC");
		self::DataGenerator();
		$data = self::fld($fild);
		if (is_int($data)) {
			$cod = $data + 1;
		} else {
			$cod = intval($data) + 1;
		}
		return $cod;
	}
}
