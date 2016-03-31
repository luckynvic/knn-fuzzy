<?php
require_once('include/knn.php');
// header('Content-Type:application/json');

if(isset($_GET['k'], $_GET['seq'])) {
	$neighbours = get_near_neighbours($_GET['seq'], $_GET['k']);
	$nearest = get_nearest_neighbour($neighbours);
	echo json_encode([
		'success'=>true, 
		'neighbours' => $neighbours,
		'nearest' => $nearest,
		'message'=>'']);
} else 
{
	echo json_encode(['success'=>false, 'message'=>'Invalid parameter']);
}
?>