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


/*
	These are Test Cases for QueryCompare. Each Test Case is a hash containing
	the following values:
	* 'sql'		-> SQL statements to create and populate test.tab1 and test.tab2
	* 'expect'	-> TRUE if tables are identical, FALSE if they aren't
	* 'comment'	-> Error message if QueryCompare result is different from expect
*/


array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` TINYINT UNSIGNED,
				`b` TINYINT UNSIGNED
			);
			
			CREATE TABLE `test`.`tab2`
			(
				`a` BIGINT UNSIGNED,
				`b` TINYINT SIGNED
			);
			
			INSERT INTO `tab1` VALUES (1,1);
			INSERT INTO `tab2` VALUES (1,1);
SQL
		,
		'expect' => TRUE,
		'comment' => 'Data Types are different, but values are identical'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` TINYINT UNSIGNED,
				`b` TINYINT UNSIGNED
			);
			
			CREATE TABLE `test`.`tab2`
			(
				`a` BIGINT UNSIGNED,
				`b` TINYINT SIGNED
			);
			
			INSERT INTO `tab1` VALUES (1,1);
			INSERT INTO `tab2` VALUES (1,1);
SQL
		,
		'maxlen' => 1,
		'fieldOffset' => 0,
		'expect' => TRUE,
		'comment' => 'Data Types are different, but values are identical, using maxlen & fieldOffset'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
SQL
		,
		'expect' => TRUE,
		'comment' => 'Both tables are empty'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
SQL
		,
		'maxlen' => 10,
		'fieldOffset' => 0,
		'expect' => TRUE,
		'comment' => 'Both tables are empty, using maxlen & fieldOffset'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			INSERT INTO `test`.`tab1` VALUE (NULL);
SQL
		,
		'expect' => FALSE,
		'comment' => 'Only one table is empty'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			INSERT INTO `test`.`tab1` VALUE (NULL);
SQL
		,
		'maxlen' => 1,
		'fieldOffset' => 0,
		'expect' => FALSE,
		'comment' => 'Only one table is empty, using maxlen & fieldOffset'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT
			);
			
			CREATE TABLE `test`.`tab2`
			(
				`a` INT
			);
SQL
		,
		'expect' => FALSE,
		'comment' => 'Tables are empty but have different number of columns'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(0,0,0),
				(1,2,3),
				(4,5,6),
				(7,8,9),
				(100,200,300);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(0,0,0),
				(1,2,3),
				(4,5,6),
				(7,8,9),
				(100,200,300);
SQL
		,
		'expect' => TRUE,
		'comment' => 'Tables are identical (no NULLs)'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(NULL,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(NULL,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
SQL
		,
		'expect' => TRUE,
		'comment' => 'Tables are identical (with NULLs)'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(NULL,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(NULL,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
SQL
		,
		'expect' => TRUE,
		'fieldOffset' => 0,
		'maxlen' => 1,
		'comment' => 'Tables are identical (with NULLs), using maxlen & fieldOffset'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(0,0,0),
				(1,2,3),
				(4,5,6),
				(7,8,9),
				(9,200,300);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(0,0,0),
				(1,2,3),
				(4,5,6),
				(7,8,9),
				(100,200,300);
SQL
		,
		'expect' => FALSE,
		'comment' => 'Tables are different (no NULLs)'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(NULL,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(NULL,2,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
SQL
		,
		'expect' => FALSE,
		'comment' => 'Tables are different (with NULLs)'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(NULL,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(NULL,2,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
SQL
		,
		'fieldOffset' => 0,
		'maxlen' => 1,
		'expect' => FALSE,
		'comment' => 'Tables are different (with NULLs), using maxlen & fieldOffset'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` CHAR(4)
			);
			
			INSERT INTO `test`.`tab1` (`a`) VALUES
				('aaa0'),
				('aaa1'),
				('aaa2'),
				('aaa3'),
				('aaa4');
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			INSERT INTO `test`.`tab2` (`a`) VALUES
				('aaa5'),
				('aaa6'),
				('aaa7'),
				('aaa8'),
				('aaa9');
SQL
		,
		'maxlen' => 3,
		'expect' => TRUE,
		'comment' => '3 leftmost chars are always identical, and we have maxlen = 3'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` CHAR(4)
			);
			
			INSERT INTO `test`.`tab1` (`a`) VALUES
				('5aaa'),
				('6aaa'),
				('7aaa'),
				('8aaa'),
				('9aaa');
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			INSERT INTO `test`.`tab2` (`a`) VALUES
				('0aaa'),
				('1aaa'),
				('2aaa'),
				('3aaa'),
				('4aaa');
SQL
		,
		'fieldOffset' => 1,
		'expect' => TRUE,
		'comment' => 'Only 1 lestmost chars is always identical, and we have fieldOffset = 1'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` CHAR(4)
			);
			
			INSERT INTO `test`.`tab1` (`a`) VALUES
				('5aaa5'),
				('6aaa6'),
				('7aaa7'),
				('8aaa8'),
				('9aaa9');
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			INSERT INTO `test`.`tab2` (`a`) VALUES
				('0aaa0'),
				('1aaa1'),
				('2aaa2'),
				('3aaa3'),
				('4aaa4');
SQL
		,
		'fieldOffset' => 1,
		'maxlen' => 2,
		'expect' => TRUE,
		'comment' => 'This combination of fieldOffset & maxlen should exclude differences'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` CHAR(4)
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`) VALUES
				('aa0a'),
				('aa1a'),
				('aa2a'),
				('aa3a'),
				('aa4a'),
				('aa5a'),
				('aa6a'),
				('aa7a'),
				('aa8a'),
				('aa9a');
			
			INSERT INTO `test`.`tab1` (`a`) VALUES
				('aa9a'),
				('aa8a'),
				('aa7a'),
				('aa6a'),
				('aa5a'),
				('aa5a'),
				('aa4a'),
				('aa3a'),
				('aa2a'),
				('aa1a');
SQL
		,
		'maxlen' => 3,
		'expect' => FALSE,
		'comment' => '3rd char is always different and we have maxlen = 3'
	));

	array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`, `b`) VALUES
				(1, 1),
				(2, 1),
				(3, 1);
			
			INSERT INTO `test`.`tab2` (`a`, `b`) VALUES
				(1, 1),
				(2, 1),
				(3, 1);
SQL
		,
		'fields' => array('a', 'b'),
		'expect' => TRUE,
		'comment' => 'Tables are identical; using fields to check explicitly both fields'
	));

	array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`, `b`) VALUES
				(1, 0),
				(2, 0),
				(3, 0);
			
			INSERT INTO `test`.`tab2` (`a`, `b`) VALUES
				(1, 1),
				(2, 1),
				(3, 1);
SQL
		,
		'fields' => array('b'),
		'expect' => FALSE,
		'comment' => 'Only second field is different, and its the only one we are checking'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`, `b`) VALUES
				(1, 0),
				(2, 0),
				(3, 0);
			
			INSERT INTO `test`.`tab2` (`a`, `b`) VALUES
				(1, 1),
				(2, 1),
				(3, 1);
SQL
		,
		'fields' => array('a'),
		'expect' => TRUE,
		'comment' => 'Only second field is different, and we arent checking it'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` CHAR(3),
				`b` CHAR(3)
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`, `b`) VALUES
				('aa0', 'x'),
				('aa0', 'x'),
				('aa0', 'x');
			
			INSERT INTO `test`.`tab2` (`a`, `b`) VALUES
				('aa1', 'z'),
				('aa1', 'z'),
				('aa1', 'z');
SQL
		,
		'fields' => array('a'),
		'maxlen' => 2,
		'expect' => TRUE,
		'comment' => 'This particular combination of maxlen and fields should not find differences'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` CHAR(3),
				`b` CHAR(3)
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`, `b`) VALUES
				('aa0', 'x'),
				('aa0', 'x'),
				('aa0', 'x');
			
			INSERT INTO `test`.`tab2` (`a`, `b`) VALUES
				('aa1', 'z'),
				('aa1', 'z'),
				('aa1', 'z');
SQL
		,
		'fields' => array('b'),
		'maxlen' => 1,
		'expect' => FALSE,
		'comment' => 'This combination of maxlen and fields doesnt exclude differences'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` CHAR(3),
				`b` CHAR(3)
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`, `b`) VALUES
				('aa0', 'x'),
				('aa0', 'x'),
				('aa0', 'x');
			
			INSERT INTO `test`.`tab2` (`a`, `b`) VALUES
				('aa1', 'z'),
				('aa1', 'z'),
				('aa1', 'z');
SQL
		,
		'fields' => array('a'),
		'maxlen' => 3,
		'expect' => FALSE,
		'comment' => 'This combination of maxlen and fields doesnt exclude differences'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(NULL,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(NULL,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
SQL
		,
		'expect' => TRUE,
		'rowOffset' => 0,
		'comment' => 'Tables are identical; using rowOffset=0'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(NULL,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(NULL,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
SQL
		,
		'expect' => TRUE,
		'rowOffset' => 2,
		'comment' => 'Tables are identical; using rowOffset=2'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(1,1,1),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(0,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
SQL
		,
		'expect' => FALSE,
		'rowOffset' => 0,
		'comment' => 'First row is different; using rowOffset=0'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(1,1,1),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(0,0,0),
				(1,2,3),
				(4,NULL,6),
				(NULL,NULL,NULL);
SQL
		,
		'expect' => TRUE,
		'rowOffset' => 1,
		'comment' => 'First row is different but rowOffset=1'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(1,1,1),
				(1,2,3),
				(4,NULL,6),
				(5,6,7);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(1,1,1),
				(1,2,3),
				(4,NULL,6),
				(5,6,7);
SQL
		,
		'expect' => TRUE,
		'rowLimit' => 3,
		'comment' => 'Tables are identical; using rowLimit=3'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(1,1,1),
				(1,2,3),
				(4,NULL,6),
				(1,1,1);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(1,1,1),
				(1,2,3),
				(4,NULL,6),
				(0,0,0);
SQL
		,
		'expect' => TRUE,
		'rowLimit' => 3,
		'comment' => 'Last row is different, but rowLimit=3'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(1,1,1),
				(1,2,3),
				(4,NULL,6),
				(1,1,1);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(0,0,0),
				(1,2,3),
				(4,NULL,6),
				(0,0,0);
SQL
		,
		'expect' => FALSE,
		'rowLimit' => 2,
		'comment' => 'Tables are different, even with rowLimit=2'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(1,1,1),
				(1,2,3),
				(4,NULL,6),
				(1,1,1);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(0,0,0),
				(1,2,3),
				(4,NULL,6),
				(0,0,0);
SQL
		,
		'expect' => TRUE,
		'rowOffset' => 1,
		'rowLimit' => 2,
		'comment' => 'Tables are different, but rowOffset=1 & rowLimit=2'
	));

array_push($tests, array(
		
		'sql' => <<<SQL
			
			CREATE TABLE `test`.`tab1`
			(
				`a` INT,
				`b` INT,
				`c` INT
			);
			
			CREATE TABLE `test`.`tab2` LIKE `test`.`tab1`;
			
			
			INSERT INTO `test`.`tab1` (`a`,`b`,`c`) VALUES
				(1,1,1),
				(1,2,3),
				(4,NULL,6),
				(1,1,1);
			
			INSERT INTO `test`.`tab2` (`a`,`b`,`c`) VALUES
				(0,0,0),
				(1,2,3),
				(4,NULL,6),
				(0,0,0);
SQL
		,
		'expect' => FALSE,
		'rowOffset' => 2,
		'rowLimit' => 2,
		'comment' => 'Tables are different, even with rowOffset=2 & rowLimit=2'
	));

?>