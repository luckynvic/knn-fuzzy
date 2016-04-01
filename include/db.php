<?php
function getDb()
{
	if(!isset($GLOBALS['db'])) {
		$db =  new PDO('sqlite:'.dirname(__FILE__).'/../db/db.sqlite');
		if(DEBUG)
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$GLOBALS['db'] = $db;
	}

	return $GLOBALS['db'];
}

$db = getDb();
// auto create table
// $db->exec('drop table trn_euclidean');
$query_offline = $db->query("SELECT count(0) FROM sqlite_master WHERE type='table' AND name='mst_offline'");
$query_option = $db->query("SELECT  count(0) FROM sqlite_master WHERE type='table' AND name='mst_option'");
$query_online = $db->query("SELECT  count(0) FROM sqlite_master WHERE type='table' AND name='mst_online'");
$query_euclidean = $db->query("SELECT  count(0) FROM sqlite_master WHERE type='table' AND name='trn_euclidean'");

$result = $query_offline->fetch(PDO::FETCH_NUM);
if($result[0]==0)
{
	//create offline table
	$db->exec(
		"create table mst_offline (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			position varchar(50),
			x INTEGER, 
			y INTEGER, 			
			beacon1 REAL, 
			beacon2 REAL, 
			beacon3 REAL,
			orient varchar(30)
			)"
		);
}

$result = $query_online->fetch(PDO::FETCH_NUM);
if($result[0]==0)
{
	//create offline table
	$db->exec(
		"create table mst_online (
			seq INTEGER  PRIMARY KEY, 
			beacon1 REAL, 
			beacon2 REAL, 
			beacon3 REAL
			)"
		);
}

$result = $query_option->fetch(PDO::FETCH_NUM);
if($result[0]==0)
{
	//create offline table
	$db->exec(
		"create table mst_option (
			name VARCHAR(50) PRIMARY KEY, 
			value VARCHAR(255)
			)"
		);
}

$result = $query_euclidean->fetch(PDO::FETCH_NUM);
if($result[0]==0)
{
	//create offline table
	$db->exec(
		"create table trn_euclidean (
			off_id INTEGER, 
			seq INTEGER,
			beacon1 REAL,
			beacon2 REAL,
			beacon3 REAL,
			value REAL,
			weight REAL
			)"
		);
}