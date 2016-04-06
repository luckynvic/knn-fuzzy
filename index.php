<?php
include('include/config.php');
include('include/header.php');
include('include/knn.php');

$k = isset($_GET['k'])?$_GET['k']:1;

$distance = get_euclidean_array();
$max_seq = get_max_seq();

?>
<script type="text/javascript" src="js/plugins/pulse/jQuery.jPulse.min.js"></script>
<script type="text/javascript" src="js/knn-grid-plot.js"></script>

<script type="text/javascript">
	var point_ways = [];
	// grid setting
	var step = 0;
	var min_step = 1;
	var max_step = <?php echo $max_seq ?>;

	var speed = 5;
	var min_speed = 1;
	var max_speed = 10;

</script>

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
    <div role="tabpanel" class="tab-pane active" id="grid">
    <div class="map-grid-container knn-map" data-type="map">
		
		<div class="point" id="point"></div>

		<div class="btn-toolbar pull-right" role="toolbar" id="btn-controls">
		<div class="btn-group btn-group-xs" data-toggle="buttons">
		  <label class="btn btn-default active">
		    <input type="radio" autocomplete="off" name="map-grid" checked value="map"> Map
		  </label>
		  <label class="btn btn-default">
		    <input type="radio" autocomplete="off" name="map-grid" value="grid"> Grid
		  </label>
		</div>
    	<div class="btn-group btn-group-xs" role="group" >

    		<a href="#" id="btn-go" class="btn btn-default" rel="tooltip" data-placement="top" title="Go"><i class="fa fa-play"></i></a>
    		<a href="#" id="btn-pause" disabled="disabled" class="btn btn-default" rel="tooltip" data-placement="top" title="Pause"><i class="fa fa-pause"></i></a>
    		<a href="#" id="btn-reset" class="btn btn-default" rel="tooltip" data-placement="top" title="Reset"><i class="fa fa-refresh"></i></a>
    	</div>
    	<div class="btn-group btn-group-xs" role="group" >
    		
    		<a href="#" id="btn-backward" class="btn btn-default" rel="tooltip" data-placement="top" title="Backward"><i class="fa fa-backward"></i></a>
    		<a href="#" id="btn-step" class="btn btn-default">0</a>
    		<a href="#" id="btn-forward" class="btn btn-default" rel="tooltip" data-placement="top" title="Forward"><i class="fa fa-forward"></i></a>
		</div>
    	<div class="btn-group btn-group-xs" role="group" >
    		<a href="#" id="btn-speed-up" class="btn btn-default" rel="tooltip" data-placement="top" title="Faster"><i class="fa fa-caret-up"></i></a>
    		<a href="#" id="btn-speed" class="btn btn-default">5</a>
    		<a href="#" id="btn-speed-down" class="btn btn-default" rel="tooltip" data-placement="top" title="Slower"><i class="fa fa-caret-down"></i></a>
		</div>

		<div class="btn-group btn-group-xs" role="group" >
			<a href="#" id="btn-log" class="btn btn-default" rel="tooltip" data-placement="top" title="Info Window"><i class="fa fa-list"></i> Info</a>		
		</div>
		</div>

		<div id="log-window" style="display:none">
		</div>

    	</div>
    </div>
    <div role="tabpanel" class="tab-pane" id="euclidean">
    <div class="margin10"></div>
    	<?php
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
    	<div class="table-responsive">
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
    			<th rowspan="2">X.w</th>
    			<th rowspan="2">Y.w</th>
    			<th rowspan="2">Xe</th>
    			<th rowspan="2">Ye</th>
    			<th rowspan="2">CDF</th>
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
				<td id="weight_<?php echo $value['seq'] ?>"></td>
				<td id="xw_<?php echo $value['seq'] ?>"></td>
				<td id="yw_<?php echo $value['seq'] ?>"></td>
				<td id="xe_<?php echo $value['seq'] ?>"></td>
				<td id="ye_<?php echo $value['seq'] ?>"></td>
				<td id="cdf_<?php echo $value['seq'] ?>"></td>
			</tr>
    		<?php  } ?>
    		<tr>
    			<td colspan="7">Standard Deviation</td>
    			<td id="k_std"></td>
    			<td id="w_std"></td>
    			<td colspan="4"></td>
    		</tr>
    		<tr>
    			<td colspan="7">Error</td>
    			<td id="k_error"></td>
    			<td id="w_error"></td>
    			<td colspan="4"></td>
    		</tr>
    		<tr>
    			<td colspan="7">Minimum</td>
    			<td id="k_min"></td>
    			<td id="w_min"></td>
    			<td colspan="4"></td>
    		</tr>
    		<tr>
    			<td colspan="7">Maximum</td>
    			<td id="k_max"></td>
    			<td id="w_max"></td>
    			<td colspan="4"></td>
    		</tr>
    		<tr>
    			<td colspan="7">Range</td>
    			<td id="k_range"></td>
    			<td id="w_range"></td>
    			<td colspan="4"></td>
    		</tr>
    		</tbody>
    	</table>
    	</div>
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
		}
	});
}

function fill_nearest()
{
	k = $('#k').val();
	$.ajax({
		url : 'get_nearest_neighbours.php',
		data : {k : k},
		dataType : 'json',
		method : 'GET'
	}).done(function(data){
			//fill online data
		$.each(data.nearest, function(i, v){
			$('#x_'+v.seq).text(v.x);
			$('#y_'+v.seq).text(v.y);
			$('#pos_'+v.seq).text(v.position);
			$('#k_'+v.seq).text(v.value);
			$('#weight_'+v.seq).text(v.weight);			
			$('#xw_'+v.seq).text(v.xw);
			$('#yw_'+v.seq).text(v.yw);
			$('#xe_'+v.seq).text(v.xe);
			$('#ye_'+v.seq).text(v.ye);
			$('#cdf_'+v.seq).text(v.cdf);
			point_ways[v.seq] = v;
		});
		$('#k_std').text(data.distance_standard_deviation);
		$('#w_std').text(data.weight_standard_deviation);
		$('#k_error').text(data.distance_mean);
		$('#w_error').text(data.weight_mean);
		$('#k_min').text(data.distance_min);
		$('#w_min').text(data.weight_min);
		$('#k_max').text(data.distance_max);
		$('#w_max').text(data.weight_max);
		$('#k_range').text(data.distance_range);
		$('#w_range').text(data.weight_range);
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
	
	//clear point
	point_ways = [];

	for(i=1; i<=<?php echo $max_seq ?>; i++)
			mark_neighbours(i, k);

	fill_nearest();
	
}

function log_point(point)
{
	text = 'No Sequence : '+point.seq +'<br>'
	text = text + 'Position : '+ point.position + '<br>';
	text = text + 'X : '+ point.x + '<br>';
	text = text + 'Y : '+ point.y + '<br>';
	text = text + 'Euclidean Distance : '+ point.value ;

	$('#log-window').html(text);
}

// map function
function set_point_map(point)
{
	// console.log(point);
	log_point(point);
	$('#point').jPulse( "disable");
	layer = $(".map-grid-container").data('type');
	cartesian = get_cartesian_plot(layer, point.x, point.y);
	css_left = cartesian.x - 12;
	css_top = cartesian.y - 24;

	$("#point").animate({
		left : css_left,
		top : css_top
	}, {
		easing : 'swing',
		duration : 1000 - (speed * 100),
		complete : function(){
			$('#point').show();
			$( "#point" ).jPulse({
				color: "#00ACED",
				size: 100,
				speed: 1000,
				interval: 500,
				left: 0,
				top: 12,
				zIndex: 98
			});
		}
	});


	// linear moving


	// $('#point').css('top', css_top)
	// 		   .css('left', css_left)
	// 		   .show();

	// $( "#point" ).jPulse({
	// 	color: "#00ACED",
	// 	size: 100,
	// 	speed: 1000,
	// 	interval: 500,
	// 	left: 0,
	// 	top: 12,
	// 	zIndex: 98
	// });
}
var timeoutId;
var pause = false;

function play()
{
	if(step >= max_step)
		pause = true;

	move_next();
	if(pause == false)
		timeoutId = setTimeout(play, 1100 - (speed * 100));
}

function move_next()
{
	prev = step;
	if(step < max_step )
		step = step + 1;
	if(prev!=step)
		set_point_map(point_ways[step]);
	$('#btn-step').text(step);
}

function move_prev()
{
	prev = step;
	if(step > min_step )
		step = step - 1;
	if(prev!=step)
		set_point_map(point_ways[step]);
	$('#btn-step').text(step);
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

	// map/grid control
	$('input[name="map-grid"]').change(function(e){
		e.preventDefault();
		$(".map-grid-container").attr('class','map-grid-container knn-'+$(this).val());
		$(".map-grid-container").data('type', $(this).val());
	});

	$('a[rel="tooltip"]').tooltip({
		container : "body"
	});

	$('#btn-backward').click(function(e){
		e.preventDefault();
		move_prev();
		$('#btn-pause').trigger('click');
	});

	$('#btn-forward').click(function(e){
		e.preventDefault();
		move_next();
		$('#btn-pause').trigger('click');
	});

	$('#btn-reset').click(function(e){
		e.preventDefault();
		step = 0;
		$('#point').hide();
		$('#point').jPulse( "disable" );
		$('#btn-step').text(step);
		$('#btn-pause').trigger('click');
		$('#log-window').text('');
	});

	$('#btn-reset').trigger('click');

	$('#btn-speed-up').click(function(e){
		e.preventDefault();
		if(speed < max_speed)
			speed = speed + 1;
		$('#btn-speed').text(speed);
	});

	$('#btn-speed-down').click(function(e){
		e.preventDefault();
		if(speed > min_speed)
			speed = speed - 1;
		$('#btn-speed').text(speed);
	});

	$('#btn-go').click(function(e){
		e.preventDefault();
		pause = false;
		play();
		$(this).attr('disabled', true);
		$('#btn-pause').attr('disabled', false);
	});

	$('#btn-pause').click(function(e){
		e.preventDefault();
		pause = true;
		clearTimeout(timeoutId);
		$(this).attr('disabled', true);
		$('#btn-go').attr('disabled', false);
	});

	$('#btn-log').click(function(e){
		e.preventDefault();
		$('#log-window').toggle(100);
	});
})
 </script>
<?php
include('include/footer.php');
?>