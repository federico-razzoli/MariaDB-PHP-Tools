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


require __DIR__ . '/../conf/conf.php';
require __DIR__ . '/query_compare.php';

if (!empty($_POST['sent'])) {
	$db = new mysqli(@$_POST['db_host'], @$_POST['db_user'], @$_POST['db_pass'], @$_POST['db_name']);
	$qc = new QueryCompare();
	$qc->setQueries($db, @$_POST['query1'], @$_POST['query2']);
	
	if ($qc->compareHash('md5', 0, 3) === TRUE) {
		$resp_class = 'y';
		$resp_text = 'ResultSets are identical';
	} else {
		$resp_class = 'n';
		$resp_text = 'ResultSets are different';
	}
} else {
	$conf = getConf();
	$_POST['db_host'] = $conf['host'];
	$_POST['db_user'] = $conf['user'];
	$_POST['db_pass'] = $conf['password'];
	$_POST['db_name'] = $conf['database'];
}

?>
<!doctype>
<html>
<head>
<title>QueryCompare GUI</title>
<style>
hr {
	clear:left;
}

label {
	font-family: monospace;
	margin-right: 10px;
}

.resp {
	display: block;
	color: white;
	font-family: sans-serif;
	font-size: 16px;
	text-align: center;
	padding-top: 10px;
	padding-bottom: 10px;
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
<form action="gui.php" method="post">
<h1>QueryCompare GUI</h1>
<h2>DB Access</h2>
<p style="float:left;">
	<label>HostName/IP:<br/>
	<input type="text" name="db_host" size="20" value="<?php echo @$_POST['db_host']; ?>" />
	</label>
</p>
<p style="float:left;">
	<label>UserName:<br/>
	<input type="text" name="db_user" size="20" value="<?php echo @$_POST['db_user']; ?>" />
	</label>
</p>
<p style="float:left;">
	<label>Password:<br/>
	<input type="text" name="db_pass" size="20" value="<?php echo @$_POST['db_pass']; ?>" />
	</label>
</p>
<p style="float:left;">
	<label>DB Name:<br/>
	<input type="text" name="db_name" size="20" value="<?php echo @$_POST['db_name']; ?>" />
	</label>
</p>
<hr />
<h2>Queries</h2>
<p style="float:left;">
	<label>Query 1:<br/>
	<textarea name="query1" cols="80" rows="5"><?php echo @$_POST['query1']; ?></textarea>
	</label>
</p>
<p style="float:left;">
	<label>Query 2:<br/>
	<textarea name="query2" cols="80" rows="5"><?php echo @$_POST['query2']; ?></textarea>
	</label>
</p>
<hr/>
<p>
	<input type="submit" name="sent" value="Compare" />
</p>
<?php

if (isset($resp_text)) {
	echo '<p class="resp ' . $resp_class . '">' . $resp_text . '</p>';
}

?>
</form>
</body>
</html>