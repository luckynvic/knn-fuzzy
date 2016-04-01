<?php
include('include/config.php');
include('include/header.php');
include('include/knn.php');

$k = isset($_GET['k'])?$_GET['k']:1;
?>
    <div class="well well-sm">
		<div class="row">
			<div class="col-md-10"></div>
			<div class="col-md-2">
			<select name="k" id="k" class="form-control">
    		<?php
    		foreach (range(1,10) as $val) {
    			echo "<option value='{$val}'".($k==$val?" selected":"").">k = {$val}</option>";
    		}
    		?>
	    	</select>
	    	</div>
		</div>
    </div>

<ul class="nav nav-tabs">
  <li role="presentation" class="active"><a href="#grid" aria-controls="profile" role="tab" data-toggle="tab">Map &amp; Grid</a></li>
  <li role="presentation"><a href="#euclidean" aria-controls="profile" role="tab" data-toggle="tab">Euclidean Distance</a></li>
  <li role="presentation"><a href="#online" aria-controls="profile" role="tab" data-toggle="tab" id="online-tab">Online k = 1</a></li>
</ul>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="grid"></div>
    <div role="tabpanel" class="tab-pane" id="euclidean">
    <div class="margin10"></div>
    	<?php
    	$distance = get_euclidean_array();
    	$max_seq = get_max_seq();
    	if(!empty($distance)) {
    	?>
    	<div class="table-responsive">
    	<table class="table table-condensed table-bordered" id="table-euclidean">
	    	<?php 
	    	$row = 1;
	    	foreach ($distance as $key => $value) {
	    		if($row==1) {
	    			echo '<thead><tr>';
	    			echo "<th>Position</th>";
	    			echo "<th>X</th>";
	    			echo "<th>Y</th>";
	    			foreach (range(1, $max_seq) as $val1)
					echo "<th>{$val1}</th>";
					echo '</tr></thead><tbody>';
				}
				echo '<tr>';
				echo "<td>{$value['position']}</td>";
				echo "<td>{$value['x']}</td>";
				echo "<td>{$value['y']}</td>";
    			foreach (range(1, $max_seq) as $val1)
					echo "<td align='right' id='euclidean_{$value['id']}_{$val1}'>".number_format($value[$val1], 6)."</td>";
				echo '</tr>';
				$row++;
	    	}
	    	echo '</tbody>';
	    	?>
    	</table>
    	</div>
    	<?php } else { ?>
		<div class="alert alert-warning">
			Data OFFLINE atau ONLINE belum ada.
		</div>
    	<?php } ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="online">
    	<?php
    	$online = $db->query('select o.seq, o.beacon1, o.beacon2, o.beacon3 from mst_online o order by o.seq')->fetchAll();
    	?>
    	<table class="table table-condensed table-border" id="online-table">
    		<thead>
    		<tr>
    			<th rowspan="2">Seq</th>
    			<th rowspan="2">Beacon 1</th>
    			<th rowspan="2">Beacon 2</th>
    			<th rowspan="2">Beacon 3</th>
    			<th colspan="3">C Pred</th>
    			<th rowspan="2" id='online-head'>k = 1</th>
    			<th rowspan="2">Weight</th>
    		</tr>
    		<tr>
    			<th>X</th>
    			<th>Y</th>
    			<th>Position</th>
    		</tr>
    		</thead>
    		<tbody>
    		<?php foreach ($online as $value) {
    		?>
			<tr>
				<td><?php echo $value['seq'] ?></td>
				<td><?php echo $value['beacon1'] ?></td>
				<td><?php echo $value['beacon2'] ?></td>
				<td><?php echo $value['beacon3'] ?></td>
				<td id="x_<?php echo $value['seq'] ?>"></td>
				<td id="y_<?php echo $value['seq'] ?>"></td>
				<td id="pos_<?php echo $value['seq'] ?>"></td>
				<td id="k_<?php echo $value['seq'] ?>"></td>
				<td></td>
			</tr>
    		<?php  } ?>
    		</tbody>
    	</table>
    </div>
 </div>
 <script type="text/javascript">

function mark_neighbours(seq, k)
{
	$.ajax({
		url : 'get_neighbours.php',
		data : {seq : seq, k : k},
		dataType : 'json',
		method : 'GET'
	}).done(function(data){
		if(data.success) {
			// mark neighbours
			$.each(data.neighbours, function(i, v){
				$('#euclidean_'+v.id+'_'+v.seq).attr('class','danger');

			});
			//mark nearest
			$('#euclidean_'+data.nearest.id+'_'+data.nearest.seq).attr('class','success');
			//fill online data
			$('#x_'+data.nearest.seq).text(data.nearest.x);
			$('#y_'+data.nearest.seq).text(data.nearest.y);
			$('#pos_'+data.nearest.seq).text(data.nearest.position);
			$('#k_'+data.nearest.seq).text(data.nearest.value);
		}
	});
}

function mark_knn()
{
	k = $('#k').val();
	//clear mark
	$('#table-euclidean td').attr('class', '');

	//online data
	$('#online-tab').text('Online k = '+k);
	$('#online-head').text('k = '+k);

	for(i=1; i<=<?php echo $max_seq ?>; i++)
			mark_neighbours(i, k);
}

$(function(){
	var url = document.location.toString();
	if (url.match('#')) {
	    $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
	} 

	// Change hash for page-reload
	$('.nav-tabs a').on('shown.bs.tab', function (e) {
	    window.location.hash = e.target.hash;
	});

	$('#k').change(function(e){
		// e.preventDefault();
		mark_knn();
	});

	mark_knn();
})
 </script>
<?php
include('include/footer.php');
?>