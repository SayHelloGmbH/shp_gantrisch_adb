<?php
/*
|---------------------------------------------------------------
| parks.swiss API
| Netzwerk Schweizer Pärke
|---------------------------------------------------------------
|
| MySQL connection and queries
|
*/


class ParksMySQL {


	/**
	 * MySQL connection
	 */
	private $connection;


	/**
	 * MySQL Host
	 */
	protected $hostname;


	/**
	 * MySQL Username
	 */
	protected $username;


	/**
	 * MySQL Password
	 */
	protected $password;


	/**
	 * MySQL Database
	 */
	protected $database;


	/**
	 * Last error message
	 */
	protected $last_error;



	/**
	 * Constructor
	 *
	 * @access public
	 * @param  string
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return void
	 */
	function __construct($hostname, $username, $password, $database) {
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;

		$this->connect();
		$this->connection->set_charset('utf8');
	}



	/**
	 * Connect to database
	 *
	 * @access private
	 * @return boolean
	 */
	private function connect() {
		$this->connection = new mysqli($this->hostname, $this->username, $this->password, $this->database);

		if ($this->connection->connect_error) {
			die('Connect Error (' . $this->connection->connect_errno . ') ' . $this->connection->connect_error);
		}

		return TRUE;
	}



	/**
	 * Get last mysql error
	 *
	 * @access public
	 * @return string
	 */
	public function get_last_error() {
		return mysqli_error($this->connection);
	}



	/**
	 * Query database
	 *
	 * @access public
	 * @param  string
	 * @return resource
	 */
	public function query($sql) {
		$result = $this->connection->query($sql);

		if ($result == FALSE) {
			$this->last_error = $this->get_last_error();
			return FALSE;
		}

		return $result;
	}



	/**
	 * Get rows from table
	 *
	 * @access public
	 * @param  string
	 * @param  array
	 * @param  array
	 * @param  array
	 * @param  integer
	 * @param  integer
	 * @return resource
	 */
	public function get($table, $where = NULL, $joins = NULL, $select = NULL, $limit = NULL, $offset = NULL, $left_outer_joins = NULL, $order_by = NULL) {

		// Select
		$db_select = "*";
		if ($select && is_array($select)) {
			$db_select = "*";
			foreach ($select as $key => $value) {
				if (!is_numeric($key)) {
					$db_select .= ", ".$key." AS ".$value;
				}
				else {
					$db_select .= ", ".$value;
				}
			}
		}

		// Inner joins
		$db_joins = "";
		if ($joins && is_array($joins)) {
			foreach ($joins as $key => $value) {
				$db_joins .= " INNER JOIN `".$key."` ON ".$this->connection->real_escape_string($value);
			}
			$db_joins .= " ";
		}

		// Left outer joins
		if ($left_outer_joins && is_array($left_outer_joins)) {
			foreach ($left_outer_joins as $key => $value) {
				$db_joins .= " LEFT OUTER JOIN `".$key."` ON ".$this->connection->real_escape_string($value);
			}
		}

		// Where
		$db_where = "";
		if ($where && is_array($where)) {
			$db_where = " WHERE ";
			foreach ($where as $key => $value) {
				$db_where .= $key." = '".$this->connection->real_escape_string($value)."' AND ";
			}
			$db_where = substr($db_where, 0, -4);
		}

		$db_limit = "";
		if ($limit && is_numeric($limit)) {
			$db_limit = " LIMIT ".(isset($offset) && is_numeric($offset) ? intval($offset).', ' : '').intval($limit);
		}

		// Order by
		if (!empty($order_by)) {
			$order_by = " ORDER BY ".$order_by;
		}

		return $this->query("SELECT ".$db_select." FROM `".$table."`".$db_joins.$db_where.$order_by.$db_limit.";");
	}



	/**
	 * Insert new row into database table
	 *
	 * @access public
	 * @param  string
	 * @param  array
	 * @param  boolean
	 * @return resource
	 */
	public function insert($table, $fields, $escape = FALSE) {
		$db_fields = '';
		$db_values = '';
		foreach ($fields as $key => $value) {

			if (!empty($value)) {
				if ($escape) {
					$value = str_replace("'", "\'", $value);
				}
				else {
					$value = str_replace("\'", "'", $value); // Replace all right '
					$value = str_replace("'", "\'", $value); // Do escaping again (for all entries, also for right)
				}
			}

			$db_fields .= "`".$key."` , ";

			if ($value == NULL) {
				$db_values .= "NULL, ";
			}
			else {
				$db_values .= "'".$value."' , ";
			}
		}
		$db_fields = substr($db_fields, 0, -2);
		$db_values = substr($db_values, 0, -2);

		return $this->query("INSERT INTO `".$table."` (".$db_fields.") VALUES (".$db_values.");");
	}



	/**
	 * Update existing rows in database table
	 *
	 * @access public
	 * @param  string
	 * @param  array
	 * @param  boolean
	 * @param  boolean
	 * @return resource
	 */
	public function update($table, $fields, $where = NULL, $escape = FALSE) {
		$db_fields = '';
		foreach ($fields as $key  => $value) {

			if (!empty($value)) {
				if ($escape) {
					$value = str_replace("'", "\'", $value);
				}
				else{
					$value = str_replace("\'", "'", $value); // Replace all right '
					$value = str_replace("'", "\'", $value); // Do escaping again (for all entries, also for right)
				}
			}

			if ($value == NULL) {
				$db_fields .= "`".$key."` = NULL, ";
			}
			else {
				$db_fields .= "`".$key."` = '".$value."', ";
			}
		}
		$db_fields = substr($db_fields, 0, -2);

		$db_where = '';
		if ($where && is_array($where)) {
			$db_where = " WHERE ";
			foreach ($where as $key => $value) {
				$db_where .= "`".$key."` = '".$this->connection->real_escape_string($value)."' AND ";
			}
			$db_where = substr($db_where, 0, -4);
		}

		return $this->query("UPDATE `".$table."` SET ".$db_fields.$db_where.";");
	}



	/**
	 * Delete rows from database table
	 *
	 * @access public
	 * @param  string
	 * @param  array
	 * @param  boolean
	 * @return resource
	 */
	public function delete($table, $where = FALSE, $limit = FALSE) {
		$db_where = '';
		if (!empty($where) && is_array($where)) {
			$db_where = " WHERE ";
			foreach ($where as $key  => $value) {
				$db_where .= "`".$key."` = '".$value."' AND ";
			}
			$db_where = substr($db_where, 0, -4);
		}

		$db_limit = '';
		if($limit && is_numeric($limit)) {
			$db_limit = " LIMIT ".$limit;
		}

		return $this->query("DELETE FROM `".$table."`".$db_where.$db_limit.";");
	}



	/**
	 * Truncate database table
	 *
	 * @access public
	 * @param  string
	 * @param  array
	 * @param  boolean
	 * @return resource
	 */
	public function truncate($table) {
		if (!empty($table)) {
			return $this->query("TRUNCATE TABLE `".$table."`;");
		}

		return FALSE;
	}


}