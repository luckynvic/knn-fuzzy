<?php
require_once('config.php');
require_once('db.php');

function gen_euclidean()
{

$db = getDb();	
$online = $db->query('select * from mst_online order by seq')->fetchAll();
$offline = $db->query('select * from mst_offline order by y desc, x desc')->fetchAll();
$db->exec('delete from trn_euclidean');

$row = 0;

$sql = 'insert into trn_euclidean (position, seq, value, beacon1, beacon2, beacon3) values ';
$params = [];
foreach ($offline as $key_off => $val_off) {
	foreach ($online as $key_on => $val_on) {
		$beacon1 = pow(($val_off['beacon1'] - $val_on['beacon1']), 2);
		$beacon2 = pow(($val_off['beacon2'] - $val_on['beacon2']), 2);
		$beacon3 = pow(($val_off['beacon3'] - $val_on['beacon3']), 2);
		$euclidian = sqrt( $beacon1 + $beacon2 + $beacon3);
		$sql .= "(:pos_{$key_off}, :seq_{$key_off}_{$key_on}, :value_{$key_off}_{$key_on}, :beacon1_{$key_off}_{$key_on}, :beacon2_{$key_off}_{$key_on}, :beacon3_{$key_off}_{$key_on}),";

		$params[":pos_{$key_off}"] = $val_off['position'];
		$params[":seq_{$key_off}_{$key_on}"] = (int)$val_on['seq'];
		$params[":value_{$key_off}_{$key_on}"] = $euclidian;
		$params[":beacon1_{$key_off}_{$key_on}"] = $beacon1;
		$params[":beacon2_{$key_off}_{$key_on}"] = $beacon2;
		$params[":beacon3_{$key_off}_{$key_on}"] = $beacon3;

		// execute each 100 data
		if( $row++ > 100){
			$sql = rtrim($sql, ',');
			$stmt = $db->prepare($sql);
			$stmt->execute($params);
			// reset all
			$row=0;
			$sql = 'insert into trn_euclidean (position, seq, value, beacon1, beacon2, beacon3) values ';
			$params = [];
		}

	}
}
if(count($params)>0) {
	$sql = rtrim($sql, ',');
	$stmt = $db->prepare($sql);
	$stmt->execute($params);
}
}

function get_max_seq()
{
	$db = getDb();
	return $db->query('select max(seq) from mst_online')->fetchColumn();
}


function get_near_neighbours($seq, $k = 1)
{
	$db = getDb();	
	$stmt = $db->prepare('select e.position, o.x, o.y, e.seq, e.value from trn_euclidean e left join mst_offline o on (o.position = e.position)
		where e.seq = :seq order by e.value asc limit :k');
	$stmt->execute([
		':seq' => $seq,
		':k' => $k
		]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_nearest_neighbour($neighbours)
{
	if(count($neighbours)==1)
		$result = [
			'position' => $neighbours[0]['position'],
			'x' => $neighbours[0]['x'],
			'y' => $neighbours[0]['y'],
			'value' => $neighbours[0]['value'],
			'seq' => $neighbours[0]['seq'],
		];
	else {
		//vote
		$hits = [];
		foreach ($neighbours as $val) {
			if(!isset($hits[$val['position']]['hits']))
				$hits[$val['position']]['hits'] = 0;
			$hits[$val['position']]['hits'] += 1;
			$hits[$val['position']]['x'] = $val['x'];
			$hits[$val['position']]['y'] = $val['y'];
			$hits[$val['position']]['value'] = $val['value'];
			$hits[$val['position']]['seq'] = $val['seq'];

		}
		// get highest hit
		$result = ['hits'=>0, 'position'=>''];
		foreach ($hits as $pos => $val) {
			if($val['hits'] > $result['hits'])
			{
				$result['position'] = $pos;
				$result['x'] = $val['x'];
				$result['y'] = $val['y'];
				$result['value'] = $val['value'];
				$result['hits'] = $val['hits'];
				$result['seq'] = $val['seq'];
			}
		}
	}
	return $result;
}

function gen_nearest_neigbours($k = 1)
{
	$max = get_max_seq();
	$result = [];
	foreach (range(1, $max) as $val) {
		$neighbours = get_near_neigbours($val, $k);
		$nearest = get_nearest_neighbour($neighbours);
		$result[$val] = [
			'neighbours' => $neighbours,
			'nearest' => $nearest
		];
	}
}

function get_euclidean_array()
{
	$db = getDb();	
	$query = $db->query(
		"select e.position, o.x, o.y, e.seq, e.value from trn_euclidean e left join mst_offline o on (o.position = e.position)
		order by o.y desc, o.x desc, e.seq
		")->fetchAll();
	$result = [];
	foreach ($query as $val) {
		if(!isset($result[$val['position']]))
			$result[$val['position']] = [];
		$result[$val['position']]['position'] = $val['position'];
		$result[$val['position']]['x'] = (int)$val['x'];
		$result[$val['position']]['y'] = (int)$val['y'];
		$result[$val['position']][$val['seq']] = (float)$val['value'];
	}
	return $result;
}