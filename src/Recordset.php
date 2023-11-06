<?php

namespace ElvisLeite\RecordSetDatabase;

use Exception;
use InvalidArgumentException;
use ElvisLeite\RecordSetDatabase\Formatter;
use ElvisLeite\RecordSetDatabase\Connection;

class RecordSet
{
	const DB_HOSTNAME = 'localhost';
	const DB_USERNAME = 'root';
	const DB_PASSWORD = '';
	const DB_DATABASE = 'mvc';
	const DB_CHARSET = 'utf8';

	private mixed $numRows;
	private mixed $result;
	private mixed $regs;
	private Connection $link;

	public function __construct()
	{
		$this->link = new Connection(self::DB_HOSTNAME, self::DB_USERNAME, self::DB_PASSWORD, self::DB_DATABASE, self::DB_CHARSET);
		$this->link->openConnection();
	}

	public function Execute(string $sql)
	{
		if (empty($sql)) {
			throw new Exception('SQL query cannot be empty');
		}
		try {
			$this->checkQuery($sql);
			$this->validateQuery($sql);
			$result = mysqli_query($this->link->getConnection(), $sql);
		} catch (\Exception $e) {
			throw new Exception('Database error: ' . $e->getMessage());
		}

		if (gettype($result) == "object") {
			$this->result = $result;
		}

		if (!$result) {
			throw new Exception('Query execution failed: ' . mysqli_error($this->link->getConnection()));
		}
		$this->link->closeConnection();
	}

	public function DataGenerator(): mixed
	{
		return $this->regs = mysqli_fetch_array($this->result);
	}

	public function getCountRows(string $sql): int
	{	
		$this->checkQuery($sql);
		$this->result = mysqli_query($this->link->getConnection(), $sql);
		$this->numRows = mysqli_num_rows($this->result);
		return $this->numRows;
	}

	public function fld(mixed $field): ?string
	{
		return $this->regs[$field] ?? null;
	}

	public function formatFld(string $field): string
	{
		return Formatter::setTimeDate($this->fld($field));
	}

	public function formatMonthWhithHourField($field): string
	{
		return Formatter::setDateTimeFormat($this->fld($field));
	}

	public function formatMonthField($field): string
	{
		return Formatter::setMonthformat($this->fld($field));
	}

	public function Insert(array $values, string $table): bool
	{
		if (empty($table) || empty($values)) {
			throw new Exception('Invalid table or values');
		}

		$escapedValues = array_map(function ($value) {
			return $value !== null ? $this->link->getConnection()->real_escape_string($value) : null;
		}, $values);

		$fields = implode(',', array_keys($escapedValues));
		$escapedValues = implode("','", $escapedValues);

		$sql = "INSERT INTO $table ($fields) VALUES ('$escapedValues')";
		
		try {
			$stmt = $this->link->getConnection()->prepare($sql);
			if ($stmt === false) {
				throw new Exception('Failed to prepare the statement');
			}

			if ($stmt->execute()) {
				return true;
			} else {
				throw new Exception('Failed to execute the statement');
			}
		} catch (Exception $e) {
			// Handle exceptions here, e.g., log or re-throw
			throw new Exception('Insert operation failed: ' . $e->getMessage());
		}
	}

	public function Select(string $table, string $where = null, string $order = null, string $limit = null, string $fields = '*')
	{
		$whereClause = !is_null($where) ? 'WHERE ' . $where : '';
		$orderClause = !is_null($order) ? 'ORDER BY ' . $order : '';
		$limitClause = !is_null($limit) ? 'LIMIT ' . $limit : '';

		$sql = "SELECT $fields FROM $table $whereClause $orderClause $limitClause";
		$this->checkQuery($sql);
		$this->validateQuery($sql);
		$result = $this->Execute($sql);

		return $result;
	}

	public function Update(array $fields, string $table, string $where): mixed
	{
		if (empty($fields) || !is_array($fields)) {
			throw new InvalidArgumentException('Invalid or empty fields data');
		}

		$setFields = [];
		foreach ($fields as $field => $value) {
			$escapedField = $this->link->getConnection()->real_escape_string($field);
			$escapedValue = is_string($value) ?
				"'" . $this->link->getConnection()->real_escape_string($value) . "'" :
				$this->link->getConnection()->real_escape_string($value);
			$setFields[] = "$escapedField = $escapedValue";
		}

		$escapedTable = $this->link->getConnection()->real_escape_string($table);
		$escapedWhere = $this->link->getConnection()->real_escape_string($where);
		$setFieldsString = implode(', ', $setFields);
		$this->validateCountQuery($table, $escapedWhere);

		$sql = "UPDATE $escapedTable SET $setFieldsString WHERE $escapedWhere";
		$this->checkQuery($sql);		
		
		try {
			$stmt = $this->link->getConnection()->prepare($sql);

			if ($stmt === false) {
				throw new Exception('Failed to prepare the statement');
			}

			if ($stmt->execute()) {
				return true;
			} else {
				throw new Exception('Failed to execute the statement');
			}
		} catch (Exception $e) {
			// Handle exceptions here, e.g., log or re-throw
			throw new Exception('Update operation failed: ' . $e->getMessage());
		}
	}

	public function Delete(string $table, string $conditions): mixed
	{
		if (empty($table)) {
			throw new InvalidArgumentException('Table name cannot be empty.');
		}

		if (empty($conditions)) {
			throw new InvalidArgumentException('Conditions array cannot be empty.');
		}

		$ensureRecordExists = "SELECT * FROM $table WHERE $conditions";
		$this->checkQuery($ensureRecordExists);
		$this->validateQuery($ensureRecordExists);

		$sql = "DELETE FROM $table WHERE $conditions";

		try {

			$stmt = $this->link->getConnection()->prepare($sql);
			if ($stmt === false) {
				throw new Exception('Failed to prepare the statement');
			}

			if ($stmt->execute()) {
				return true;
			} else {
				throw new Exception('Failed to execute the statement');
			}
		} catch (Exception $e) {
			// Handle exceptions here, e.g., log or re-throw
			throw new Exception('Delete operation failed: ' . $e->getMessage());
		}
	}

	public function getField(string $fieldname, string $tablename, string $whereClause): ?string
	{
		if (empty($fieldname)) {
			throw new InvalidArgumentException('fieldname name cannot be empty.');
		}
		if (empty($tablename)) {
			throw new InvalidArgumentException('tablename name cannot be empty.');
		}
		if (empty($whereClause)) {
			throw new InvalidArgumentException('whereClause name cannot be empty.');
		}
		$ensureRecordExists = "SELECT $fieldname FROM $tablename WHERE $whereClause";
		$this->checkQuery($ensureRecordExists);
		$this->validateQuery($ensureRecordExists);
		
		try {

			$this->Execute($ensureRecordExists);
			$this->Select($tablename, $whereClause);
			if (!is_null($this->DataGenerator())) {
				return $this->fld($fieldname);
			}
			return null;
		} catch (\Exception $e) {
			throw new Exception('Database error: ' . $e->getMessage());
		}
	}

	public function setAutoCode(string $field, string $table): int
	{
		$sql  = "SELECT $field FROM  $table ORDER BY $field DESC";		
		$this->Execute($sql);
		$this->DataGenerator();
		$data = $this->fld($field);
		if ($data !== null) {
			$cod = (int) $data + 1;
		} else {
			$cod = 1;
		}

		return $cod;
	}

	private function checkQuery(string $sql): bool
	{
		$this->result = mysqli_query($this->link->getConnection(), $sql);

		if (!$this->result) {
			throw new InvalidArgumentException('Erro na consulta SQL: ' . mysqli_error($this->link->getConnection()));
		}

		return true;
	}

	private function validateQuery(string $sql): bool
	{

		$result = $this->getCountRows($sql);

		if ($result === 0) {
			throw new InvalidArgumentException('Record does not exist.');
		}
		return true;
	}
	private function validateCountQuery(string $table, string $where): bool
	{
		$sqls = "SELECT COUNT(*) FROM $table WHERE $where";		
		$this->Execute($sqls);		
		$result = $this->DataGenerator();
		$data = (int) $result['COUNT(*)'];
		if ($data === 0) {
			throw new Exception('Record does not exist.');
		}
		return true;
	}
	
	
}
