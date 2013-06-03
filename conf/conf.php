<?php

/*
	MadConfigurator for MariaDB
	
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
 *	Get MariaDB client Configuration options.
 *	Understands MariaDB conf files, taking into account
 *	only [client] section. Also understands files that
 *	uses parameters with the same names, even if they're
 *	not in the [client] section.
 *	@param		string				$file		File path + name.
 *	@return		string[string]
 */
function getConf($file = NULL, $conf = array())
{
	if ($file === NULL) {
		$file = __DIR__ . '/conf.ini';
	}
	$conf = @parse_ini_file($file, TRUE);
	
	// if [client] section exist, exclude everything else
	if (array_key_exists('client', $conf)) {
		$conf = $conf['client'];
	}
	
	// if another conf file is requested,
	// its options will be added to conf recursively.
	// but new options won't overwite the ones we have.
	if (array_key_exists('extends_file', $conf)) {
		$conf = array_merge(getConf($conf['extends_file']), $conf);
	}
	
	// return all we collected
	return $conf;
}

?>