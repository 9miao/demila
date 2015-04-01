<?PHP
// +----------------------------------------------------------------------
// | Demila [ Beautiful Digital Content Trading System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://demila.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Email author@demila.org
// +----------------------------------------------------------------------


class mysql {
	public $conn = "";
	public $debug = 0;
	public $queries;
	
	function mysql($dbUser = 'user', $dbPass = 'pass', $dbName = 'database', $dbHost = 'localhost') {
		global $config;
		
		$this->user = $dbUser;
		$this->pass = $dbPass;
		$this->name = $dbName;
		$this->host = $dbHost;
		
		if ($this->debug == 1) {
			$this->queries = array ();
			$this->comments = array ();
		}
		$this->last_result = FALSE;
		
		$this->debug = $config ['debug'];
		
		return true;
	}
	function connect() {
		//			$this->conn = mysql_connect($this->host, $this->user, $this->pass) or die(show_sql_error(mysql_error()));
		$this->conn = mysql_connect ( $this->host, $this->user, $this->pass ) or die ( 'ERR_DB_CONNECT' );
		$this->select_db ( $this->name );
		
		return $this->conn;
	}
	function select_db($db) {
		mysql_select_db ( $db, $this->conn ) or die ( 'ERR_MYSQL_SELECT_DB' ); //die(show_sql_error(mysql_error()));
		

		$this->query ( 'set names utf8' );
	}
	function query($query, $comment = "") {
		if (! $this->conn)
			$this->conn = $this->connect ();
		$start = microtime ();
		//			$result = mysql_query($query, $this->conn) or die(show_sql_error(' - '.$query.' - '.mysql_error()));
		

		$result = mysql_query ( $query, $this->conn ) or die ( mysql_error () );
		
		$end = microtime ();
		if ($this->debug == 1) {
			list ( $usec1, $sec1 ) = explode ( ' ', $start );
			list ( $usec2, $sec2 ) = explode ( ' ', $end );
			$diff = round ( $sec2 - $sec1 + $usec2 - $usec1, 5 );
			$this->queries [] = $query;
			$this->comments [] = $comment;
			$this->queries ['time'] [] = $diff;
		}
		$this->last_result = $result;
		return $result;
	}
	function fetch_object($res = FALSE) {
		$res = $res ? $res : $this->last_result;
		return mysql_fetch_object ( $res );
	}
	function fetch_array($res = FALSE, $type = 'name') {
		$res = $res ? $res : $this->last_result;
		if ($type == 'name') {
			return mysql_fetch_array ( $res, MYSQL_ASSOC );
		} else {
			return mysql_fetch_array ( $res, MYSQL_NUM );
		}
	}
	function num_rows($res = FALSE) {
		$res = $res ? $res : $this->last_result;
		return mysql_num_rows ( $res );
	}
	function insert_id() {
		return mysql_insert_id ();
	}
	function affected_rows() {
		return mysql_affected_rows ();
	}
	function close() {
		if ($this->conn)
			mysql_close ( $this->conn );
	}
	function print_queries() {
		
		$sumTime = 0;
		if (is_array ( $this->queries ['time'] )) {
			$sumTime = array_sum ( $this->queries ['time'] );
		}
		
		@ $html = '<hr style="clear: both"/><div style="margin-left: 10px;">';
		@ $html .= '<div style="color: red; text-decoration: underline; margin-bottom: 5px;">Queries: <b>' . count ( $this->queries ['time'] ) . '</b>; queriesExecuteTime: <b>' . $sumTime . '</b></div>';
		if (! empty ( $this->queries ['time'] )) {
			foreach ( $this->queries ['time'] as $key => $value ) {
				$key2 = $key + 1;
				$html .= nl2br ( '#' . $key2 . ' - ' . $this->queryColor ( $this->queries [$key] ) ) . ';( <b><font color=yellow>' . $this->comments [$key] . '</b></font> ) <b>�����: <font color=blue>' . sprintf ( "%.5f", $this->queries ['time'] [$key] ) . '</font></b><hr>';
			}
		}
		$html .= '</div>';
		return $html;
	}
	
	function getFoundRows() {
		$this->query ( "
			SELECT FOUND_ROWS() AS f_rows
		" );
		
		$d = $this->fetch_array ();
		
		return $d ['f_rows'];
	}
	
	/*
	 * examples:
	 * mysql> INSERT INTO t VALUES(1),(2),(3);
	 * mysql> SELECT ROW_COUNT();
	 * 
	 * mysql> DELETE FROM t WHERE i IN(1,2);
	 * mysql> SELECT ROW_COUNT();
	 */
	function getRowCount() {
		$this->query ( "
			SELECT ROW_COUNT() AS f_rows
		" );
		
		$d = $this->fetch_array ();
		
		return $d ['f_rows'];
	}
	
	function queryColor($query) {
		
		//[dv] this has to come first or you will have goofy results later.
		$query = preg_replace ( "/['\"]([^'\"]*)['\"]/i", "'<FONT COLOR='#FF6600'>$1</FONT>'", $query, - 1 );
		
		$query = str_ireplace ( array (
				'*', 
				'SELECT', 
				'UPDATE ', 
				'DELETE ', 
				'INSERT ', 
				'INTO', 
				'VALUES', 
				'FROM', 
				'LEFT', 
				'JOIN', 
				'WHERE', 
				'LIMIT', 
				'ORDER BY', 
				'GROUP BY', 
				'AND', 
				'OR ',  //[dv] note the space. otherwise you match to 'COLOR' ;-)
				'DESC', 
				'ASC', 
				'ON ' 
		), array (
				"<FONT COLOR='#FF6600'><B>*</B></FONT>", 
				"<FONT COLOR='#00AA00'><B>SELECT</B> </FONT>", 
				"<FONT COLOR='#00AA00'><B>UPDATE</B> </FONT>", 
				"<FONT COLOR='#00AA00'><B>DELETE</B> </FONT>", 
				"<FONT COLOR='#00AA00'><B>INSERT</B> </FONT>", 
				"<FONT COLOR='#00AA00'><B>INTO</B></FONT>", 
				"<FONT COLOR='#00AA00'><B>VALUES</B></FONT>", 
				"<FONT COLOR='#00AA00'><B>FROM</B></FONT>", 
				"<FONT COLOR='#00CC00'><B>LEFT</B></FONT>", 
				"<FONT COLOR='#00CC00'><B>JOIN</B></FONT>", 
				"<FONT COLOR='#00AA00'><B>WHERE</B></FONT>", 
				"<FONT COLOR='#AA0000'><B>LIMIT</B></FONT>", 
				"<FONT COLOR='#00AA00'><B>ORDER BY</B></FONT>", 
				"<FONT COLOR='#00AA00'><B>GROUP BY</B></FONT>", 
				"<FONT COLOR='#0000AA'><B>AND</B></FONT>", 
				"<FONT COLOR='#0000AA'><B>OR</B> </FONT>", 
				"<FONT COLOR='#0000AA'><B>DESC</B></FONT>", 
				"<FONT COLOR='#0000AA'><B>ASC</B></FONT>", 
				"<FONT COLOR='#00DD00'><B>ON</B> </FONT>" 
		), $query );
		
		return $query;
	
	} //SQL_DEBUG 
	

	//addOns
	//Get first row from sql select query
	function getRow($query) {
		
		$result = $this->query ( $query );
		
		if (mysql_num_rows ( $result ) == 0) {
			return false;
		} else {
			return $this->fetch_array ();
			//return $rows;
		}
	}
	
	//Get first column from sql select query
	function getCol($query) {
		
		$result = $this->query ( $query );
		
		if (mysql_num_rows ( $result ) == 0) {
			return false;
		} else {
			while ( $row = mysql_fetch_array ( $result ) ) {
				$rows [] = $row [0];
			}
			return $rows;
		}
	}
	
	//Get first cell from first row from sql select query
	function getValue($query) {
		
		$result = $this->query ( $query );
		
		if (mysql_num_rows ( $result ) == 0) {
			return false;
		} else {
			$row = mysql_fetch_array ( $result );
			return $row [0];
		}
	}
	
	//Prepare your POST or GET array for insert or update functions
	/*function prepareArray($array) {
		
		if (is_array ( $array )) {
			
			$arrayKeys = array_keys ( $array );
			
			foreach ( $arrayKeys as $value ) {
				
				$type = substr ( $value, 0, 2 );
				if ($type != 'iu' && $type != 'pk') {
					$dbColumnName = $value;
				} else {
					$dbColumnName = substr ( $value, 3 );
				}
				
				if (strtolower ( $type ) == 'pk') {
					$preparedArray ['pk'] [$dbColumnName] = $array [$value];
				} elseif (strtolower ( $type ) == 'iu') {
					$preparedArray ['iu'] [$dbColumnName] = $array [$value];
				} else {
					$preparedArray ['temp'] [$dbColumnName] = $array [$value];
				}
			}
			
			return $preparedArray;
		} else {
			return false;
		}
	}
	*/
	
	//Insert into database
	function insert($tableName, $array) {
		
		if (is_array ( $array )) {
			
			$arrayKeys = array_keys ( $array );
			
			foreach ( $arrayKeys as $value ) {
				
				if (strtolower ( $value ) == 'pk') {
					
					$arrayKeys2 = array_keys ( $array [$value] );
					
					foreach ( $arrayKeys2 as $value2 ) {
						$insertArray [$value2] = $array [$value] [$value2];
					}
				} elseif (strtolower ( $value ) == 'iu') {
					
					$arrayKeys2 = array_keys ( $array [$value] );
					
					foreach ( $arrayKeys2 as $value2 ) {
						$insertArray [$value2] = $array [$value] [$value2];
					}
				}
			}
			
			$columnName = '';
			$columnValue = '';
			$arrayKeys = array_keys ( $insertArray );
			
			foreach ( $arrayKeys as $value ) {
				/*
				if ($columnName == '') {
					$columnName = '`' . $value . '`';
					$columnValue = "'" . $insertArray [$value] . "'";
				} else {
					$columnName .= ', `' . $value . '`';
					$columnValue .= ", '" . $insertArray [$value] . "'";
				}*/
				
				if ($columnName != '') {
					$columnName .= ', ';
					$columnValue .= ", ";
				}
				
				if ($insertArray [$value] != "NOW()" && $insertArray [$value] != "NULL") {
					$columnValue .= " '" . sql_quote ( $insertArray [$value] ) . "' ";
				} else {
					$columnValue .= " " . $insertArray [$value] . " ";
				}
				$columnName .= ' `' . sql_quote ( $value ) . '` ';
			}
			
			$this->query ( "INSERT INTO `$tableName`($columnName) VALUES($columnValue)" );
			
			return true;
		} else {
			return false;
		}
	}
	
	//Update table
	function update($tableName, $array) {
		
		if (is_array ( $array )) {
			
			$setQuery = '';
			$whereQuery = '';
			
			$arrayKeys = array_keys ( $array );
			
			foreach ( $arrayKeys as $value ) {
				
				if (strtolower ( $value ) == 'pk') {
					
					$arrayKeys2 = array_keys ( $array [$value] );
					
					foreach ( $arrayKeys2 as $value2 ) {
						
						if ($whereQuery == '') {
							$whereQuery = "`$value2` = '" . $array [$value] [$value2] . "'";
						} else {
							$whereQuery .= " AND `$value2` = '" . $array [$value] [$value2] . "'";
						}
					}
				} elseif (strtolower ( $value ) == 'iu') {
					
					$arrayKeys2 = array_keys ( $array [$value] );
					
					foreach ( $arrayKeys2 as $value2 ) {
						
						if ($array [$value] [$value2] == 'NULL' || $array [$value] [$value2] == 'NOW()') {
							$updateValue = sql_quote ( $array [$value] [$value2] );
						} else {
							$updateValue = "'" . sql_quote ( $array [$value] [$value2] ) . "'";
						}
						
						if ($setQuery == '') {
							$setQuery = "`$value2` = $updateValue";
						} else {
							$setQuery .= ", `$value2` = $updateValue";
						}
					}
				}
			}
			
			$this->query ( "UPDATE `$tableName` SET $setQuery WHERE $whereQuery" );
			
			return true;
		} else {
			return false;
		}
	}
	
	function getAll($query, $field = '') {
		$this->query ( $query, __FUNCTION__ );
		if ($this->num_rows () < 1) {
			return false;
		}
		$return = array ();
		while ( $d = $this->fetch_array () ) {
			if ($field == '') {
				$return [] = $d;
			} else {
				$return [$d [$field]] = $d;
			}
		}
		return $return;
	}
}

?>