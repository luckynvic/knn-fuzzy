<?php
require_once("db.php");

function get_option($name, $default = '')
{
	$db = getDb();
	$stmt = $db->prepare("select value from mst_option where name = :name");
	$stmt->execute([
		':name' => $name
		]);
	
	if(($result = $stmt->fetchColumn())!==FALSE)
		return $result;
	else
		return $default;
}

function set_option($name, $value)
{
	//delete previous value
	try
	{
		$db = getDb();
		$stmt1 = $db->prepare("select name from mst_option where name = :name");
		$stmt1->execute([
		':name' => $name
		]);

		if($stmt1->fetchColumn()!==FALSE)
			//update
			$stmt = $db->prepare("update mst_option set value=:value where name = :name");
		else	
			// insert new value
			$stmt = $db->prepare("insert into mst_option(name, value) values(:name, :value)");

		$stmt->execute([
			':name' => $name,
			':value' => $value
			]);

	} catch(Exception $e)
	{
		throw $e;
	}
}

/**
 * $params = array(
 * $name = array(
 * 			label = "text",
 * 			help = "text"
 * )
 * )
 */

function render_option(array $params)
{
	foreach ($params as $key => $value) {
	$val = get_option($key);
	$label = $value['label'];
	$desc = isset($value['desc']) ? $value['desc'] : '';
	$type = isset($value['type']) ? $value['type'] : 'number';
	$input = isset($value['input']) ? 
		$value['input'] : 
		"<input type='{$type}' class='form-control' id='{$key}' name='value[]' value='{$val}'>";

	echo "
	  <div class='form-group'>
	    <label for='{$key}' class='col-sm-2 control-label'>{$label}</label>
	    <div class='col-sm-10'>
	      <input type='hidden' value='{$key}' name='name[]' />
	      {$input}
	      ";
	if($desc!=='')
		echo "<span class='help-block'>{$desc}</span>";
	echo "    </div>
	  </div>";
	}
}

?>