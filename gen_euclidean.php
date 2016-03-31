<?php
require_once('include/knn.php');
ini_set('max_execution_time',0);
set_time_limit(0);

header('Content-Type:application/json');

try {
	gen_euclidean();
	echo json_encode(['success'=>true]);
} catch(Exception $e) {
	echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}
?>