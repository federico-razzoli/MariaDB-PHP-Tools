<?php

/*
	QueryCompare 1.0
	Check that 2 resultsets are identical.
	
	Copyright:
		Federico Razzoli  2013
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
 *	\class		QueryCompare
 *	\brief		Compare 2 mysqli_result objects.
 *	\details	
 */
class QueryCompare
{
	//! Debug mode.
	private /*. bool .*/  $dbug   = FALSE; 
	
	//! First resultset to compare.
	private /*. mysqli .*/  $resOne = NULL; 
	//! Second resultset to compare.
	private /*. mysqli .*/  $resTwo = NULL; 
	
	
	
	/**
	 *	Returns checksum for $resultSet.
	 *	@param		mysqli_result		$resultSet		Result set to be checksummed.
	 *	@param		string				$algo			Checksum algorythm.
	 *	@param		int					$fieldOffset	Examine all fields dropping this offset.
	 *	@param		int					$maxlen			Truncate all fields to this length, NULL to disable.
	 *	@param		fieldNames[int]		$fields			Names/of fields to compare, NULL to compare them all.
	 *	@param		int					$rowOffset		How many rows will skip at beginning of RecordSet.
	 *	@param		int					$rowLimit		How many rows will examine.
	 *	@return		string
	 */
	static public function getHash($resultSet, $algo, $fieldOffset, $maxlen, $fieldNames, $rowOffset, $rowLimit)
	{
		// result buffer
		/*. string .*/ $buffer = '';
		// current row
		/*. mixed[string] .*/ $row = '';
		
		$useSubstr = ($fieldOffset !== NULL || $maxlen !== NULL)
			? TRUE
			: FALSE;
		
		// skip rows offset
		for ( ; $rowOffset > 0; $rowOffset--) {
			$resultSet->fetch_row();
		}
		
		// go on while there are results and we dont reach limit (or there is no limit)
		while (($row = $resultSet->fetch_array(MYSQLI_ASSOC)) && ($rowLimit === NULL || $rowLimit > 0)) {
			foreach ($row as $key => $val) {
				if ($fieldNames === NULL || in_array($key, $fieldNames)) {
					$buffer .= ($useSubstr)
						? substr((string)$val, $fieldOffset, ($maxlen !== NULL ? $maxlen : strlen((string)$val)))
						: (string)$val;
				}
			}
			
			// if there is a limit, decrement
			if ($rowLimit !== NULL) {
				$rowLimit--;
			}
		}
		
		// return hash
		return hash($algo, $buffer, TRUE);
	}
	
	
	/**
	 *	Checks if $algo value is valid. If it is not, throws an Exception.
	 *	@param		string		$algo
	 *	@return		void
	 *	@throws		Exception
	 */
	private function checkAlgo($algo)
	{
		// check $algo
		if (!in_array($algo, hash_algos())) {
			throw Exception('[' . __CLASS__ . '.compare] : invalid $algo param. Must be one of the values returned by hash_algos()');
		}
	}
	
	
	/**
	 *	Checks if $maxlen value is valid. If it is not, throws an Exception.
	 *	@param		int		$maxlen
	 *	@return		void
	 *	@throws		Exception
	 */
	private function checkNatural($maxlen)
	{
		// check $maxlen
		if ($maxlen !== NULL && !(is_int($maxlen) && $maxlen > 0)) {
			throw Exception('[' . __CLASS__ . '.maxlen] : invalid $maxlen param. Must be NULL or natural number');
		}
	}
	
	
	/**
	 *	Compare hashes computer from 2 resultsets (mysqli objects).
	 *	@param		string			$algo			Algorythm to be used for checksum. Default: 'md5'.
	 *												Must be one of the values returned by hash_algos().
	 *	@param		int				$fieldOffset	Examine all fields dropping this offset.
	 *	@param		int				$maxlen			Truncate all fields to this length, NULL to disable.
	 *	@param		string[int]		$fields			Names of fields to compare, NULL to compare them all.
	 *	@param		int				$rowOffset		How many rows will skip at beginning of RecordSet.
	 *	@param		int				$rowLimit		How many rows will examine.
	 *	@return		bool
	 *	@throws		Exception
	 */
	public function compareHash($algo = 'md5', $fieldOffset = NULL, $maxlen = NULL, $fields = NULL,
		$rowOffset = 0, $rowLimit = NULL)
	{
		// check integrity
		$this->checkAlgo($algo);
		$this->checkNatural($maxlen);
		
		// if number of fields is different, resultsets are different even if they're empty;
		// also, if field num is different we dont need to compare everything
		if ($this->resOne->field_count !== $this->resTwo->field_count) {
			return FALSE;
		}
		
		// if number of rows is different, save time
		if ($this->resOne->num_rows !== $this->resTwo->num_rows) {
			return FALSE;
		}
		
		// compute and compare checksums
		return self::getHash($this->resOne, $algo, $fieldOffset, $maxlen, $fields, $rowOffset, $rowLimit)
			=== self::getHash($this->resTwo, $algo, $fieldOffset, $maxlen, $fields, $rowOffset, $rowLimit);
	}
	
	
	/**
	 *	Set ResultSets to compare.
	 *	@param		mysqli_result		$resOne		First resultset to compare.
	 *	@param		mysqli_result		$resTwo		Second resultset to compare.
	 *	@return		void
	 *	@throws		Exception
	 */
	public function setResultSets(mysqli_result $resOne = NULL, mysqli_result $resTwo = NULL)
	{
		// check integrity
		if (!(is_object($resOne) && get_class($resOne) === 'mysqli_result')) {
			throw new Exception('[' . __CLASS__ . '.__construct] : invalid $resOne param. Must be a mysqli_result object');
		}
		if (!(is_object($resTwo) && get_class($resTwo) === 'mysqli_result')) {
			throw new Exception('[' . __CLASS__ . '.__construct] : invalid $resTwo param. Must be a mysqli_result object');
		}
		
		// assign props
		$this->resOne = $resOne;
		$this->resTwo = $resTwo;
	}
	
	
	/**
	 *	Convert a query into a ResultSet or throw error.
	 *	@param		mysqli		$dbLink		DB connection.
	 *	@param		string		$sql		SQL query.
	 *	@return		mysqli_result
	 *	@throws		Exception
	 */
	private function toResultSet(mysqli $dbLink, $sql)
	{
		$res = $dbLink->query($sql);
		
		if ($dbLink->sqlstate != '00000') {
			throw new Exception('<strong>Database Error.</strong> SQLSTATE: ' . $db->sqlstate
				. '; Error: ' . $db->errno . ' - ' . $db->error);
		}
		
		return $res;
	}
	
	
	/**
	 *	Set queries to compare. 2 ResultSets are immediatly retreived.
	 *	@param		mysqli		$dbLink		DB connection.
	 *	@param		string		$sql1		First SQL query.
	 *	@param		string		$sql2		Second SQL query.
	 *	@return		void
	 *	@throws		Exception
	 */
	public function setQueries(mysqli $dbLink, $sql1, $sql2)
	{
		// assign ResultSets
		$this->resOne = $this->toResultSet($dbLink, $sql1);
		$this->resTwo = $this->toResultSet($dbLink, $sql2);
	}
	
	
	/**
	 *	Set tables to compare. 2 ResultSets are immediatly retreived.
	 *	Table and database names must not be quoted or escaped.
	 *	@param		mysqli		$dbLink		DB connection.
	 *	@param		string		$db1		First table's DB. NULL to use default DB.
	 *	@param		string		$tab1		First table's name.
	 *	@param		string		$db2		Second table's DB.
	 *	@param		string		$tab2		Second table's name. NULL to use default DB.
	 *	@return		void
	 *	@throws		Exception
	 */
	public function setTables(mysqli $dbLink, $db1, $tab1, $db2, $tab2)
	{
		// prepare db names
		if ($db1 !== NULL) {
			$db1 = '`' . str_replace('`', '``', $db1) . '`.';
		}
		if ($db2 !== NULL) {
			$db2 = '`' . str_replace('`', '``', $db2) . '`.';
		}
		
		// prepare table names
		$tab1 = '`' . str_replace('`', '``', $tab1) . '`';
		$tab2 = '`' . str_replace('`', '``', $tab2) . '`';
		
		// get ResultSets
		$sql = 'SELECT * FROM ' . $db1 . $tab1 . ';';
		$this->resOne = $this->toResultSet($dbLink, $sql);
		$sql = 'SELECT * FROM ' . $db2 . $tab2 . ';';
		$this->resTwo = $this->toResultSet($dbLink, $sql);
	}
}

?>