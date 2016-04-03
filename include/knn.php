<?php
require_once('config.php');
require_once('db.php');
require_once('common_function.php');

function get_fuzzy_rules()
{
		$result = [
			'immediate' => [
				'a' => (int)get_option('fuzzy_immediate_a', 2),
				'b' => (int)get_option('fuzzy_immediate_b', 6),
				'weight' => (float)get_option('fuzzy_immediate_weight', 0),
			],
			'near' => [
				'a' => (int)get_option('fuzzy_near_a', 2),
				'b' => (int)get_option('fuzzy_near_b', 6),
				'c' => (int)get_option('fuzzy_near_c', 10),
				'weight' => (float)get_option('fuzzy_near_weight', 0.3),
			],
			'far' => [
				'a' => (int)get_option('fuzzy_far_a', 6),
				'b' => (int)get_option('fuzzy_far_b', 10),
				'c' => (int)get_option('fuzzy_far_c', 14),
				'weight' => (float)get_option('fuzzy_far_weight', 0.6),
			],
			'unknown' => [
				'a' => (int)get_option('fuzzy_unknown_a', 10),
				'b' => (int)get_option('fuzzy_unknown_b', 14),
				'weight' => (float)get_option('fuzzy_unknown_weight', 1),
			]
		];
	return $result;
}

function get_fuzzy_weight($distance)
{
	$rules = get_fuzzy_rules();
	$result = [];

	// immediate
	if($distance <= $rules['immediate']['a'])
		$result['immediate'] = 1;
	if($distance > $rules['immediate']['a'] && $distance < $rules['immediate']['b'])
		$result['immediate'] = ($rules['immediate']['b'] - $distance) / ($rules['immediate']['b'] - $rules['immediate']['a']);
	if($distance >= $rules['immediate']['b'])
		$result['immediate'] = 0;

	// near
	if($distance <= $rules['near']['a'] || $distance >= $rules['near']['c'])
		$result['near'] = 0;
	if($distance > $rules['near']['a'] && $distance <= $rules['near']['b'])
		$result['near'] = ($distance - $rules['near']['a']) / ($rules['near']['b'] - $rules['near']['a']);
	if($distance > $rules['near']['b'] && $distance < $rules['near']['c']) {
		$result['near']  = -1 * ($distance - $rules['near']['c']) / ($rules['near']['c'] - $rules['near']['b']);
	}
	
	//far
	if($distance <= $rules['far']['a'] || $distance >= $rules['far']['c'])
		$result['far'] = 0;
	if($distance > $rules['far']['a'] && $distance <= $rules['far']['b'])
		$result['far'] = ($distance - $rules['far']['a']) / ($rules['far']['b'] - $rules['far']['a']);
	if($distance > $rules['far']['b'] && $distance < $rules['far']['c'])
		$result['far']  = -1 * ($distance - $rules['far']['c']) / ($rules['far']['c'] - $rules['far']['b']);
	
	//unknown
	if($distance <= $rules['unknown']['a'])
		$result['unknown'] = 0;
	if($distance > $rules['unknown']['a'] && $distance < $rules['unknown']['b'])
		$result['unknown'] = ($distance - $rules['unknown']['a'] ) / ($rules['unknown']['b'] - $rules['unknown']['a']);
	if($distance >= $rules['unknown']['b'])
		$result['unknown'] = 1;

	return $result;
}

function get_defuzzy_centroid($fuzzy_set)
{
	$num = 0;
	$denum = 0;

	// only get highest value;
	$highest = [];
	foreach ($fuzzy_set as $domain => $set) {
		foreach ($set as $key => $value) {
			if(!isset($highest[$key]))
				$highest[$key] = ['x' => $value['x'], 'y' => 0];
			if($value['y'] > $highest[$key]['y'])
				$highest[$key]['y'] = $value['y'];
		}
	}

	foreach ($highest as $value) {
		$num += $value['x'] * $value['y'];
		$denum += $value['y'];
	}		


	// foreach ($fuzzy_set as $domain => $set) {
	// 	foreach ($set as $key => $value) {
	// 		$num += $value['x'] * $value['y'];
	// 		$denum += $value['y'];
	// 	}		
	// }
	if($denum==0)
		return 0;
	else
		return $num/$denum;
}

function get_defuzzy_weight_average($fuzzy_set)
{
	$rules = get_fuzzy_rules();
	$num = 0;
	$denum = 0;
	foreach ($fuzzy_set as $domain => $set) {
		foreach ($set as $key => $value) {
			$num += $rules[$domain]['weight'] * $value['y'];
			$denum += $value['y'];
		}		
	}
	return $num / $denum;
}

function gen_euclidean()
{

$db = getDb();	
$online = $db->query('select * from mst_online order by seq')->fetchAll();
$offline = $db->query('select * from mst_offline order by y desc, x desc')->fetchAll();
$db->exec('delete from trn_euclidean');

$row = 0;
$presql = 'insert into trn_euclidean (off_id, seq, value, beacon1, beacon2, beacon3, fuzzy_data) values ';
$sql = $presql;
$params = [];
foreach ($offline as $key_off => $val_off) {
	foreach ($online as $key_on => $val_on) {
		$beacon1 = pow(($val_off['beacon1'] - $val_on['beacon1']), 2);
		$beacon2 = pow(($val_off['beacon2'] - $val_on['beacon2']), 2);
		$beacon3 = pow(($val_off['beacon3'] - $val_on['beacon3']), 2);
		$euclidian = sqrt( $beacon1 + $beacon2 + $beacon3);
		
		$weight = get_fuzzy_weight($euclidian);

		$sql .= "(
			:pos_{$key_off}, 
			:seq_{$key_off}_{$key_on}, 
			:value_{$key_off}_{$key_on}, 
			:beacon1_{$key_off}_{$key_on}, 
			:beacon2_{$key_off}_{$key_on}, 
			:beacon3_{$key_off}_{$key_on},
			:data_{$key_off}_{$key_on}
			),";

		$params[":pos_{$key_off}"] = $val_off['id'];
		$params[":seq_{$key_off}_{$key_on}"] = (int)$val_on['seq'];
		$params[":value_{$key_off}_{$key_on}"] = $euclidian;
		$params[":beacon1_{$key_off}_{$key_on}"] = $beacon1;
		$params[":beacon2_{$key_off}_{$key_on}"] = $beacon2;
		$params[":beacon3_{$key_off}_{$key_on}"] = $beacon3;
		$params[":data_{$key_off}_{$key_on}"] = serialize($weight);
		
		// execute each 100 data
		// sqllite has limit to 999 parameter binding
		if( $row++ > 100){
			$sql = rtrim($sql, ',');
			$stmt = $db->prepare($sql);
			$stmt->execute($params);
			// reset all
			$row=0;
			$sql = $presql;
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
	$stmt = $db->prepare('select 
		e.off_id as id, 
		o.position, 
		o.orient, 
		o.x, o.y, 
		e.seq, 
		e.value,
		e.fuzzy_data
		from trn_euclidean e left join mst_offline o on (o.id = e.off_id)
		where e.seq = :seq order by e.value asc limit :k');
	$stmt->execute([
		':seq' => $seq,
		':k' => $k
		]);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$sets = [];
	array_walk($result, function(&$item, $idx) use (&$sets){
		$fuzzy = unserialize($item['fuzzy_data']);

		foreach ($fuzzy as $domain => $y) {
			if(!isset($sets[$domain]))
				$sets[$domain] = [];
			array_push($sets[$domain], [
				'x'=>(float)$item['value'], 'y'=>$y
				]);
		}
		unset($item['fuzzy_data']);
	});
	$result['fuzzy_set'] = $sets;
	$result['defuzzy'] = [
		'centroid' => get_defuzzy_centroid($sets),
		'weight_average' => get_defuzzy_weight_average($sets),
		];
	return $result;
}

function get_nearest_neighbour($neighbours)
{
	if(count($neighbours)==1)
		$result = [
			'id' => $neighbours[0]['id'],
			'position' => $neighbours[0]['position'],
			'orient' => $neighbours[0]['orient'],
			'x' => $neighbours[0]['x'],
			'y' => $neighbours[0]['y'],
			'value' => $neighbours[0]['value'],
			'seq' => $neighbours[0]['seq']
		];
	else {
		//vote
		$hits = [];
		foreach ($neighbours as $val) {

			// only process data with id
			if(!isset($val['id']))
				continue;

			if(!isset($hits[$val['position']]['hits'])) {
				$hits[$val['position']]['hits'] = 0;
				//first found item
				$hits[$val['position']]['id'] = $val['id'];
				$hits[$val['position']]['x'] = $val['x'];
				$hits[$val['position']]['y'] = $val['y'];
				$hits[$val['position']]['value'] = $val['value'];
				$hits[$val['position']]['orient'] = $val['orient'];
				$hits[$val['position']]['seq'] = $val['seq'];
			}
			$hits[$val['position']]['hits'] += 1;
		}
		// get highest hit
		$result = ['hits'=>0, 'position'=>''];
		foreach ($hits as $pos => $val) {
			if($val['hits'] > $result['hits'])
			{
				$result['position'] = $pos;
				$result['x'] = $val['x'];
				$result['y'] = $val['y'];
				$result['id'] = $val['id'];
				$result['value'] = $val['value'];
				$result['orient'] = $val['orient'];
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
		"select 
		e.off_id as id, 
		o.position, 
		o.x, 
		o.y, 
		e.seq, 
		e.value, 
		o.orient
		from trn_euclidean e left join mst_offline o on (o.id = e.off_id)
		order by o.y desc, o.x desc, e.seq
		")->fetchAll();
	$result = [];
	foreach ($query as $val) {
		if(!isset($result[$val['id']]))
			$result[$val['id']] = [];

		$result[$val['id']]['position'] = $val['position'];
		$result[$val['id']]['id'] = $val['id'];
		$result[$val['id']]['x'] = (int)$val['x'];
		$result[$val['id']]['y'] = (int)$val['y'];
		$result[$val['id']]['orient'] = $val['orient'];
		$result[$val['id']][$val['seq']] = (float)$val['value'];
	}
	return $result;
}