<?php

namespace ElvisLeite\RecordSetDatabase;

use Exception;
use ElvisLeite\RecordSetDatabase\Formatter;
use ElvisLeite\RecordSetDatabase\Connection;
use mysqli;

class Recordset
{
	/**
	 * Number rows	 
	 * @var mixed
	 */
	private mixed $numRows;

	/**
	 * Result 	 
	 * @var mysqli_result
	 */
	private $result;

	/**	 
	 *Registres
	 * @var array
	 */
	private $regs;

	/**
	 * Link of the connection
	 * @var mysqli
	 */
	private mysqli $link;

	/**
	 * Method responsible for create connection to database
	 */
	function __construct()
	{
		// Set connection and store it in self::$link
		$this->link = Connection::setConnect();
	}

	/**
	 * Method responsible for runing the sql query
	 * @param string $sql
	 * @return void	 
	 */
	public function Execute(string $sql): bool
	{
		if (empty($sql)) {
			throw new Exception('SQL query cannot be empty');
		}

		if (gettype(mysqli_query($this->link, $sql)) == "object") {
			$this->result = mysqli_query($this->link, $sql);
			return true;
		}

		if (!$this->result) {
			throw new Exception(mysqli_error($this->link));
		}

		return false;
	}

	/**
	 * Method responsible for generating the datas	 
	 * @return mixed 
	 */
	public function DataGenerator(): mixed
	{
		Connection::setDesconnect();
		//CLOSE CONNECTION			
		return $this->regs = mysqli_fetch_array($this->result);
	}

	/**
	 * Method responsible for the number of rows in the table
	 * @param string $sql
	 * @return int	 
	 */
	public function getCountRows(string $sql): int
	{
		$this->result = mysqli_query($this->link, $sql);

		//RETURN NUMBER OF TABLE ROWS
		return $this->numRows  = mysqli_num_rows($this->result);
	}

	/**
	 * Method responsible for selectioning the table's filds	
	 * @param string $field
	 * @return string
	 */
	public function fld(mixed $field): string
	{
		return $this->regs[$field];
	}

	/**
	 * Method responsible for handling the date field in the format date: time Brazil
	 * @param string $field
	 * @return string
	 */
	public function formFld(string $field): string
	{
		return Formatter::setTimeDate($this->fld($field));
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
	 * @param array $values
	 * @param string $table
	 * @return void
	 */
	public function Insert(array $values, string $table): bool
	{
		if (empty($table) || empty($values)) {
			throw new Exception('Invalid table or values');
		}

		$escapedValues = array_map(function ($value) {
			return $value !== null ? $this->link->real_escape_string($value) : null;
		}, $values);

		$fields = implode(',', array_keys($escapedValues));
		$escapedValues = implode("','", $escapedValues);

		$sql = "INSERT INTO $table ($fields) VALUES ('$escapedValues')";

		if ($this->Execute($sql)) {
			return true;
		}
		return false;
	}

	/**
	 * Selects data from a database table based on the given parameters.
	 * @param string $table The name of the table to select data from.
	 * @param string $where (optional) The WHERE clause to filter the data.
	 * @param string $order (optional) The ORDER BY clause to sort the data.
	 * @param string $limit (optional) The LIMIT clause to limit the number of rows returned.
	 * @param string $fields (optional) The fields to select from the table.
	 * @throws Some_Exception_Class A description of the exception that may be thrown.
	 * @return mixed The result of the select query.
	 */
	public function Select(string $table, string $where = null, string $order = null, string $limit = null, string $fields = '*')
	{
		$whereClause = !is_null($where) ? 'WHERE ' . $where : '';
		$orderClause = !is_null($order) ? 'ORDER BY ' . $order : '';
		$limitClause = !is_null($limit) ? 'LIMIT ' . $limit : '';
		$sql = "SELECT $fields FROM $table $whereClause $orderClause $limitClause";
		$result = $this->Execute($sql);

		return $result;
	}
	
	/**
	 * Updates the specified fields in the given table based on the provided condition.
	 * @param array $fields The fields to be updated and their new values.
	 * @param string $table The name of the table to update.
	 * @param string $where The condition that determines which rows to update.
	 * @throws Exception If the fields data is empty or not an array.
	 * @return void
	 */
	public function Update(array $fields, string $table, string $where): void
	{
		if (empty($fields) || !is_array($fields)) {
			throw new Exception('Invalid or empty fields data');
		}

		$setFields = [];
		foreach ($fields as $field => $value) {
			$escapedField = $this->link->real_escape_string($field);
			$escapedValue = is_string($value) ?
				"'" . $this->link->real_escape_string($value) . "'" :
				$this->link->real_escape_string($value);
			$setFields[] = "$escapedField = $escapedValue";
		}

		$escapedTable = $this->link->real_escape_string($table);
		$escapedWhere = $this->link->real_escape_string($where);
		$setFieldsString = implode(', ', $setFields);

		$sql = "UPDATE $escapedTable SET $setFieldsString WHERE $escapedWhere";

		$this->Execute($sql);
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
		$this->Execute($sql);
	}

	/**
	 * Method responsible for selectioning a fild of the table
	 * @param string $table
	 * @param string $where
	 * @param mixed $field
	 * @return string
	 */
	public function getField(string $fieldname, string $tablename, string $whereClause): ?string
	{
		$this->Select($tablename, $whereClause);
		if (!is_null($this->DataGenerator())) {
			return $this->fld($fieldname);
		}
		return null;
	}


	/**
	 * Method responsible for generated a auto-increment on the table
	 * @param mixed $fild
	 * @param string $table
	 * @return int $cod
	 */

	public function setAutoCode(string $fild, string $table): int
	{
		$this->Execute("SELECT $fild FROM  $table ORDER BY $fild DESC");
		$this->DataGenerator();
		$data = $this->fld($fild);
		if ($data !== null) {
			$cod = (int) $data + 1;
		} else {
			$cod = 1;
		}

		return $cod;
	}
}
