<?php
require_once('include/knn.php');
// header('Content-Type:application/json');
if(isset($_GET['k'])) {
	$max = get_max_seq();
	$nearest = [];
	$nearest_value = [];
	$weight_value = [];
	foreach (range(1, $max) as $seq) {
		$neighbours = get_near_neighbours($seq, $_GET['k']);
		$nearest[$seq] = get_nearest_neighbour($neighbours);
		$nearest[$seq]['weight'] = $neighbours['defuzzy']['weight_average'];
		$nearest[$seq]['xw'] = $nearest[$seq]['weight'] * $nearest[$seq]['x'];
		$nearest[$seq]['yw'] = $nearest[$seq]['weight'] * $nearest[$seq]['y'];
		$nearest[$seq]['xe'] = ($nearest[$seq]['xw']==0?'unknown': $nearest[$seq]['xw'] / $nearest[$seq]['weight'] );
		$nearest[$seq]['ye'] = ($nearest[$seq]['yw']==0?'unknown': $nearest[$seq]['yw'] / $nearest[$seq]['weight'] );
		$nearest_value[$seq] = $nearest[$seq]['value'];
		$weight_value[$seq] = $nearest[$seq]['weight'];
	}

	$result = [
		'success'=>true, 
		// 'nearest' => $nearest,
		'distance_standard_deviation' => get_standard_deviation($nearest_value),
		'weight_standard_deviation' => get_standard_deviation($weight_value),
		'distance_mean' => get_mean($nearest_value),
		'weight_mean' => get_mean($weight_value),
		'distance_min' => min($nearest_value),
		'weight_min' => min($weight_value),
		'distance_max' => max($nearest_value),
		'weight_max' => max($weight_value),
		'distance_range' =>  max($nearest_value) - min($nearest_value),
		'weight_range' => max($weight_value) - min($weight_value),
		'message'=>''];


	// recalculate CFD and RSME
	foreach (range(1, $max) as $seq) {
		$nearest[$seq]['zscore'] = ($nearest[$seq]['weight'] - $result['weight_mean']) / $result['weight_standard_deviation'];
		$nearest[$seq]['cdf'] = get_cdf($nearest[$seq]['zscore']);
	}
	$result['nearest'] = $nearest;



	echo json_encode($result);
} else 
{
	echo json_encode(['success'=>false, 'message'=>'Invalid parameter']);
}
?>