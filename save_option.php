<?php
header('Content-Type:application/json');
if(isset($_POST['name'], $_POST['value'])) {
	require_once("include/common_function.php");
	$name = $_POST['name'];
	$value = $_POST['value'];

	try
	{
		if(is_array($name))
		{
			for($c=0; $c<count($name); $c++)
			{
				set_option($name[$c], $value[$c]);
			}
		} else
			set_option($name[$c], $value[$c]);

		echo json_encode(array('success'=>true, 'message'=>'Data already saved'));
	} catch(Exception $e)
	{
		echo json_encode(array('success'=>false, 'message'=>$e->getMessage()));
	}

} else
		echo json_encode(array('success'=>false, 'message'=>'Empty option'));
?>