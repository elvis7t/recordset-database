<?php

namespace ElvisLeite\RecordSetDatabase;

use Exception;
use InvalidArgumentException;
use ElvisLeite\RecordSetDatabase\Formatter;
use ElvisLeite\RecordSetDatabase\Connection;
use mysqli_stmt;

class RecordSet
{
	const DB_HOSTNAME = 'localhost';
	const DB_USERNAME = 'root';
	const DB_PASSWORD = '';
	const DB_DATABASE = 'mvc';
	const DB_CHARSET = 'utf8';

	private mixed $result;
	private mixed $regs;
	private Connection $link;

	public function __construct()
	{
		$this->link = new Connection(self::DB_HOSTNAME, self::DB_USERNAME, self::DB_PASSWORD, self::DB_DATABASE, self::DB_CHARSET);
		$this->link->openConnection();
	}

	public function execute(string $sql, array $params = [])
	{
		if (empty($sql)) {
			throw new InvalidArgumentException('SQL query cannot be empty');
		}
		try {
			$stmt = $this->prepareStatement($sql);
			if (!empty($params)) {
				$this->bindParams($stmt, $params);
			}
			$stmt->execute();
			$this->result = $stmt->get_result();
		} catch (\Exception $e) {
			throw new Exception('Database error: ' . $e->getMessage());
		}

		$this->link->closeConnection();
	}

	public function getDataGenerator(): mixed
	{
		return $this->regs = $this->result->fetch_array(MYSQLI_ASSOC);
	}

	public function getCountRows(string $sql, array $params = []): int
	{
		$this->execute($sql, $params);
		
		return $this->result->num_rows;
		
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

	public function insert(array $values, string $table)
	{
		if (empty($table) || empty($values)) {
			throw new Exception('Invalid table or values');
		}

		$fields = implode(',', array_keys($values));
		$placeholders = implode(',', array_fill(0, count($values), '?'));

		$sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";

		try {
			$stmt = $this->prepareStatement($sql);
			$this->bindParams($stmt, array_values($values));
			$stmt->execute();
		} catch (\Exception $e) {
			throw new Exception('Insert operation failed: ' . $e->getMessage());
		}
	}

	public function select(string $table, string $where = null, string $order = null, string $limit = null, string $fields = '*', array $params = [])
	{
		$whereClause = !is_null($where) ? 'WHERE ' . $where : '';
		$orderClause = !is_null($order) ? 'ORDER BY ' . $order : '';
		$limitClause = !is_null($limit) ? 'LIMIT ' . $limit : '';

		$sql = "SELECT $fields FROM $table $whereClause $orderClause $limitClause";

		try {
			$this->execute($sql, $params);
			$result = $this->result;  
			return $result;
		} catch (\Exception $e) {
			throw new Exception('Select operation failed: ' . $e->getMessage());
		}
	}

	public function update(array $fields, string $table, string $where, array $params = [])
	{
		if (empty($fields) || !is_array($fields)) {
			throw new InvalidArgumentException('Invalid or empty fields data');
		}

		$setFields = [];
		$placeholders = [];
		foreach ($fields as $field => $value) {
			$setFields[] = "$field = ?";
			$placeholders[] = $value;
		}

		$setFieldsStr = implode(', ', $setFields);
		$sql = "UPDATE $table SET $setFieldsStr WHERE $where";

		try {
			$stmt = $this->prepareStatement($sql);
			$this->bindParams($stmt, array_merge($placeholders, $params));
			$stmt->execute();
		} catch (\Exception $e) {
			throw new Exception('Update operation failed: ' . $e->getMessage());
		}
	}

	public function delete(string $table, string $conditions, array $params = [])
	{
		if (empty($table)) {
			throw new InvalidArgumentException('Table name cannot be empty.');
		}

		if (empty($conditions)) {
			throw new InvalidArgumentException('Conditions array cannot be empty.');
		}

		$sql = "DELETE FROM $table WHERE $conditions";

		try {
			$stmt = $this->prepareStatement($sql);
			$this->bindParams($stmt, $params);
			$stmt->execute();
		} catch (\Exception $e) {
			throw new Exception('Delete operation failed: ' . $e->getMessage());
		}
	}

	public function getField(string $fieldName, string $tableName, string $whereClause, array $params = [])
	{
		if (empty($fieldName) || empty($tableName) || empty($whereClause)) {
			throw new InvalidArgumentException('Invalid fieldname, tablename, or whereClause');
		}

		$sql = "SELECT $fieldName FROM $tableName WHERE $whereClause";

		try {
			$this->execute($sql, $params);
			$this->getDataGenerator();

			if (!is_null($this->regs)) {
				return $this->regs[$fieldName];
			}
			return null;
		} catch (\Exception $e) {
			throw new Exception('Database error: ' . $e->getMessage());
		}
	}

	public function setAutoCode(string $fieldName, string $tableName, array $params = []): int
	{
		if (empty($fieldName) || empty($tableName)) {
			throw new InvalidArgumentException('Invalid fieldname, tablename, or whereClause');
		}
		$sql  = "SELECT $fieldName FROM  $tableName ORDER BY $fieldName DESC";

		try {
			$this->execute($sql, $params);
			$this->getDataGenerator();
			$data = $this->regs[$fieldName];
			$cod = is_null($data) ? 1 : (int) $data + 1;
			return $cod;
		} catch (\Exception $e) {
			throw new Exception('Database error: ' . $e->getMessage());
		}
	}

	private function prepareStatement(string $sql): mysqli_stmt
	{
		$stmt = $this->link->getConnection()->prepare($sql);
		if ($stmt === false) {
			throw new Exception('Failed to prepare the statement');
		}
		return $stmt;
	}

	private function bindParams(mysqli_stmt $stmt, array $params)
	{
		if (empty($params)) {
			return;
		}

		$types = str_repeat('s', count($params));
		$stmt->bind_param($types, ...$params);
	}
}
