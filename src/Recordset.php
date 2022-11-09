<?php

namespace ElvisLeite\RecordsetDatabase;
 use ElvisLeite\RecordsetDatabase\Connection;
 
class Recordset
{
	/**
	 * Result 	 
	 * @var string
	 */
	private $result;

	/**	 
	 *Registres
	 * @var array
	 */
	private $regs;

	/**
	 * Link of the connection
	 * @var string
	 */
	private $link;

	/**
	 * Method responsible for set Connection
	 */
	function __construct()
	{
		$this->link = Connection::setConnect();
		return $this->link;
	}

	/**
	 * Method responsible for runing the sql query
	 * @param mysqli $sql
	 * @return void	 
	 */
	public function Execute($sql): void
	{
		$this->result = mysqli_query($this->link, $sql) or die(mysqli_error($this->link));
	}

	/**
	 * Method responsible for generating the datas	 
	 * @return void	 
	 */
	public function DataGenerator()
	{
		return $this->regs = mysqli_fetch_array($this->result);
		//CLOSE CONNECTION
		$ob =  new Connection();
		$ob->getDesconnect($this->link);
	}

	/**
	 * Method responsible for selectioning the table's filds	 *
	 * @param array $field
	 * @return mixed
	 */
	public function fld($field): mixed
	{
		return $this->regs[$field];
	}

	/**
	 * Method responsible for Insert data into the table
	 * @param array $values
	 * @param string $table
	 * @return sql
	 */
	public function Insert($values, $table)
	{
		//QUERY DATA
		$fields = array_keys($values);

		//MOONT A QUERY
		$sql = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES ('" . implode("','", $values) . "')";

		//RETURN RUN SQL
		return self::Execute($sql);
	}

	/**	 
	 *Method responsible for selectioning the datas
	 * @param string $table
	 * @param string $where
	 * @param string $order
	 * @param string $limit
	 * @param string $fields
	 * @return sql
	 */
	public function Select($table, $where = null, $order = null, $limit = null, $fields = '*')
	{
		//DATES OF THE QUERY
		$where = strlen($where) ? 'WHERE ' . $where : '';
		$order = strlen($order) ? 'ORDER BY ' . $order : '';
		$limit = strlen($limit) ? 'LIMIT ' . $limit : '';

		//SET UP A QUERY		
		$sql = "SELECT $fields FROM $table $where $order $limit";

		//RUN DE QUERY
		$this->result = mysqli_query($this->link, $sql);

		//RUN DE QUERY
		return $this->result;
	}

	/**	 
	 * Method responsible for updating the datas
	 * @param array $filds
	 * @param string $table
	 * @param string $where
	 * @return bol
	 */
	public function Update($filds, $table, $where)
	{
		//MOUNT QUERY
		$sql = "UPDATE $table SET ";
		foreach ($filds as $fild => $date) {
			if (is_string($date))
				$sql .= $fild . ' = "' . $date . '", ';
			else
				$sql .= $fild . " = " . $date . ", ";
		}
		$sql = substr($sql, 0, -2);
		$sql .= " WHERE " . $where;

		//RUN QUERY
		$this->Execute($sql);

		//RETURN SUCCESS
		return true;
	}

	/**
	 * Method responsible for deleting the data
	 * @param mixed $table	
	 * @param mixed $whr
	 * @return void
	 */
	public function Delete($table, $whr): void
	{
		$sql = "DELETE FROM $table WHERE $whr";

		// RUN SQL		
		$this->Execute($sql);
	}

	/**
	 * Method responsible for selectioning a fild of the table
	 * @param string $table
	 * @param string $where
	 * @param string $field
	 * @return string
	 */
	public function getFild($table, $where, $field)
	{
		self::Select($table, $where, '', '', $field);
		self::DataGenerator();
		return $this->fld($field);
	}

	/**
	 * Method responsible for generated a auto-increment on the table
	 * @param mixed $fild
	 * @param mixed $table
	 * @return int $cod
	 */
	public function setAutoCode($fild, $table)
	{
		$this->Execute("SELECT " . $fild . " FROM " . $table . " ORDER BY " . $fild . " DESC");
		$this->DataGenerator();
		$cod = $this->fld($fild) + 1;

		//RETURN ID
		return $cod;
	}
}
