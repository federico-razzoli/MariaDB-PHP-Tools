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

?><!doctype html>
<html>
<head>
<title>QueryCompare Test</title>
<style>

div {
	display: block;
	padding-top: 5px;
	padding-bottom: 5px;
	color: white;
	font-family: sans-serif;
	font-size: 14px;
}

.y {
	background-color: green;
}

.n {
	background-color: red;
}

</style>
</head>
<body>
<h1>QueryCompare Test</h1>
<?php

require __DIR__ . '/../../conf/conf.php';
require __DIR__ . '/../query_compare.php';

$tests = array();
require __DIR__ . '/test_data.php';


function getArrayKey($arr, $key, $defaultValue)
{
	return (array_key_exists($key, $arr) === TRUE)
		? $arr[$key]
		: $defaultValue;
}

$conf = getConf();
$db = new mysqli($conf['host'], $conf['user'], $conf['password'], $conf['database']);

$pass = $fail = 0;
$qc = new QueryCompare();

foreach ($tests as $n => $t) {
	// drop & create tables with test data
	$t['sql'] =
		'DROP TABLE IF EXISTS `test`.`tab1`;' . "\n" .
		'DROP TABLE IF EXISTS `test`.`tab2`;' . "\n" .
		$t['sql'];
	$db->multi_query($t['sql']);
	
	// free all results to avoid "commands out of sync" error
	do {
		if ($res = $db->store_result()) {
			$res->fetch_all(MYSQLI_ASSOC);
			$res->free();
		}
	} while ($db->more_results() && $db->next_result());
	
	// get results from test.tab1 and test.tab2
	$res1 = $db->query('SELECT * FROM `test`.`tab1`;');
	$res2 = $db->query('SELECT * FROM `test`.`tab2`;');
	
	// set comparation params
	$algo         = getArrayKey($t,  'algo',        'md5');
	$rowOffset    = getArrayKey($t,  'rowOffset',    0);
	$rowLimit     = getArrayKey($t,  'rowLimit',     NULL);
	$fieldOffset  = getArrayKey($t,  'fieldOffset',  NULL);
	$maxlen       = getArrayKey($t,  'maxlen',       NULL);
	$fields       = getArrayKey($t,  'fields',       NULL);
	
	// compare results and trigger a Pass or a Fail
	$qc->setResultSets($res1, $res2);
	if (($ret = $qc->compareHash($algo, $fieldOffset, $maxlen, $fields, $rowOffset, $rowLimit)) !== $t['expect']) {
		echo '<p><strong>Fail (' . (string)$n . '):</strong> ' . $t['comment'] . '. ' .
			'Returned value: ' . (int)$ret . '; Expected: ' . (int)$t['expect'] . '</p>';
		$fail++;
	} else {
		$pass++;
	}
}

echo '<div class="' . ($fail === 0 ? 'y' : 'n') . '"><p>Pass: ' . (string)$pass . ', Fail: ' . (string)$fail . '</p></div>';

?>
</body>
</html>