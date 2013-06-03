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


require __DIR__ . '/any_field.php';
require __DIR__ . '/../conf/conf.php';

// parse configuration file into $conf
$conf = getConf();
// connect, using configuration options
$af = new AnyField($conf['host'], $conf['user'], $conf['password'], '');
// search type 'is' means =
$af->setSearchType('is');
// search in all tables, in all databases
$af->searchAll('tony_iommi@blacksabbath.info');
// search in 'her_tab' table
$af->searchInTable('index.html', 'her_db', 'her_tab');

?>