<?php

/* 
	AnyField 1.0
	A PHP tool to search values that could be anywhere in a MariaDB database.
	
	Copyright:
		Federico Razzoli  2011, 2012
	Contacts:
		santec@riseup.net
	
	This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, version 3 of the License.
	
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.
	
    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 


/**
 *	\class		AnyField
 *	\brief		Search a value into any field of any table in a db.
 *	\details	Useful to search a configuration key or value when you don't know
 *				where it is stored (table, column).
 *				If you are searching for a value and you know at least
 *				in which table is is, you can use searchInTable().
 *				If you don't, you can use search().
 *				Example:
 *				$af = new AnyField('localhost', 'root', 'root', 'her_db');
 *				$af->searchAll('login_timeout');
 *				$af->searchInTable('index.html', 'her_tab');
 */
class AnyField
{
	//! Array of existing tables.
	private /*. mysqli .*/  $db     = NULL; 
	//! Search type to be used to search the value.
	private /*. string .*/  $sType  = ''; 
	
	
	/**
	 *	Quote a MariaDB identifier with backticks.
	 *	@param		string		$id			MariaDB identifier.
	 *	@return		string
	 */
	static private function quoteIdentifier($id)
	{
		return '`' . str_replace('`', '``', $id) . '`';
	}
	
	/**
	 *	Quote a string value with single quotes.
	 *	@param		string		$val			String value.
	 *	@return		string
	 */
	static private function quoteValue($val, $before = '', $after = '')
	{
		return "'" . $before . str_replace("'", "\\'", $val) . $after . "'";
	}
	
	/**
	 *	The constructor establishes a connection with the DB.
	 *	@param		string		$host		Hostname.
	 *	@param		string		$user		DB username.
	 *	@param		string		$pass		DB password.
	 *	@param		string		$db			DB name.
	 *	@return		void
	 */
	public function __construct($host, $user, $pass, $db)
	{
		// try to load mysqli
        if (function_exists('dl') === TRUE && extension_loaded('mysqli') !== TRUE) {
			dl('mysqli');
		}
		
		// try to instantiate mysqli
		try {
			$this->db = @new mysqli($host, $user, $pass, $db);
		}
		catch (Exception $e) {
			if (extension_loaded('mysqli') !== TRUE) {
				trigger_error('mysqli extension is not loaded and can not be loaded dynamically', E_USER_ERROR);
			}
			return;
		}
		
		// connect
		if ($this->db->connect_error) {
			$this->SQLError = $this->db->connect_error;
			trigger_error($this->SQLError, E_USER_ERROR);
		}
	}
	
	/**
	 *	Set the type of search to be used to find value.
	 *	Valid types:
	 *	'is' - use = operator;
	 *	'is_not' - use = operator;
	 *	'contains' - use LIKE;
	 *	'starts' - use LIKE;
	 *	'ends' - use LIKE.
	 *	Default is 'contains'.
	 *	@param		string		$sType			Type of search.
	 *	@return		void
	 */
	public function setSearchType($sType)
	{
		$this->sType = $sType;
	}
	
	/**
	 *	Return the SQL expression to search $val with the selected search type.
	 *	@param		string		$val			Value to search.
	 *	@return		void
	 */
	public function getSearch($val)
	{
		switch ($this->sType) {
			case 'is' :
				return '= ' . self::quoteValue($val);
				break;
			case 'is_not' :
				return '<> ' . self::quoteValue($val);
				break;
			case NULL :
			case '' :
			case 'contains' :
				return 'LIKE ' . self::quoteValue($val, '%', '%');
				break;
			case 'starts' :
				return 'LIKE ' . self::quoteValue($val, '', '%');
				break;
			case 'ends' :
				return '= ' . self::quoteValue($val, '%', '');
				break;
		}
		
		trigger_error('Invalid Search Type: ', E_USER_ERROR);
	}
	
	/**
	 *	Search for specified value in the specified table.
	 *	@param		string		$needle			Value to be found.
	 *	@param		string		$tabName		Table in which the value will be searched.
	 *	@return		void
	 */
	public function searchInTable($needle, $dbName, $tabName)
	{
		/*. string .*/ $sql = 'SELECT `COLUMN_NAME` FROM `information_schema`.`COLUMNS` WHERE `TABLE_NAME` = ' . self::quoteValue($tabName) . ';';
		/*. object .*/ $cols = $this->db->query($sql);
		
		/*. object .*/        $search  = NULL;
		/*. mixed[mixed] .*/  $field   = array();
		
		while ($field = mysqli_fetch_row($cols)) {
			$sql = 'SELECT TRUE FROM ' . self::quoteIdentifier($dbName) . '.' . self::quoteIdentifier($tabName) . ' WHERE `' . $field[0] . '` ' . $this->getSearch($needle) . ';';
			$search = $this->db->query($sql);
			
			if ($search === FALSE) {
				echo '<p><strong style="color:red;">Invalid Query: ' . htmlspecialchars($sql) . '</strong></p>';
			} elseif (mysqli_fetch_row($search)) {
				echo '<p>Found in:&nbsp;&nbsp;&nbsp;&nbsp;' .
					htmlspecialchars($tabName) .'.' . htmlspecialchars($field[0]) . '</p>';
			}
		}
	}
	
	/**
	 *	Search for specified value in all DBs.
	 *	@param		string		$needle			Value to be found.
	 *	@return		void
	 */
	public function searchAll($needle)
	{
		/*. string        .*/  $sql     = '';
		/*. object        .*/  $tables  = NULL;
		/*. mixed[mixed]  .*/  $tab     = array();
		
		echo '<h3>Searching for: \'' . htmlspecialchars($needle) . '\'</h3>';
		
		$sql = 'SELECT `TABLE_SCHEMA`, `TABLE_NAME` FROM `information_schema`.`TABLES` WHERE `TABLE_TYPE` = \'BASE TABLE\';';
		$tables = $this->db->query($sql);
		
		while ($tab = mysqli_fetch_row($tables)) {
			$this->searchInTable($needle, $tab[0], $tab[1]);
		}
		
		echo '<p><strong>End Of Search</strong></p><hr/>';
	}
}

?>