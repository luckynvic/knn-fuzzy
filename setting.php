<?php
require_once('include/config.php');
require_once('include/common_function.php');
include('include/header.php');

?>
<div class="container-fluid">
<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Setting
            <small>System Preference</small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="index.php">Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-cog"></i> Setting
            </li>
        </ol>
    </div>
</div>

<ul class="nav nav-tabs">
  <li role="presentation" class="active"><a href="#offline" role="tab" data-toggle="tab">Offline Template Format</a></li>
  <li role="presentation"><a href="#online" role="tab" data-toggle="tab">Online Template Format</a></li>
  <li role="presentation"><a href="#fuzzy" role="tab" data-toggle="tab">Fuzzy Weight</a></li>
  <li role="presentation"><a href="#alert" role="tab" data-toggle="tab">Alert</a></li>
</ul>
<div class="alert hide" id="alert-template">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="offline">
    	<div class="margin10"></div>

		<div class="alert alert-info" id="alert-offline">
			<p>Tentukan setting Nomor kolom, dan baris file excel untuk Offline Data. No Kolom dan baris dimulai dari 1</p>
		</div>
		
    	<form class="form-horizontal" method="post">
		 <?php 
		 render_option([
		 	'offline_begin_line' => [
		 		'label' => 'Nomor baris data pertama',
		 	],
		 	'offline_position_col' => [
		 		'label' => 'Kolom nama Posisi'
		 	],
		 	'offline_x_col' => [
		 		'label' => 'Kolom nilai X'
		 	],
		 	'offline_y_col' => [
		 		'label' => 'Kolom nilai Y'
		 	],
		 	'offline_beacon1_col' => [
		 		'label' => 'Kolom nilai Beacon 1',
		 	],
		 	'offline_beacon2_col' => [
		 		'label' => 'Kolom nilai Beacon 2'
		 	],
		 	'offline_beacon3_col' => [
		 		'label' => 'Kolom nilai Beacon 3'
		 	],
		 	'offline_orient_col' => [
		 		'label' => 'Kolom Orientasi'
		 	],
		 	
		 	]);
		 ?>
		  <div class="form-group">
		    <div class="col-sm-offset-2 col-sm-10">
		      <button type="submit" class="btn btn-primary" data-save-text='<i class="fa fa-cog fa-spin"></i> Saving...'><i class="fa fa-save"></i> Save</button>
		    </div>
		  </div>
    	</form>
    </div>
	<div role="tabpanel" class="tab-pane" id="online">
		<div class="margin10"></div>

		<div class="alert alert-info" id="alert-online">
			<p>Tentukan setting Nomor kolom, dan baris file excel untuk Online Data. No Kolom dan baris dimulai dari 1</p>
		</div>
		
    	<form class="form-horizontal" method="post">
		 <?php 
		 render_option([
		 	'online_begin_line' => [
		 		'label' => 'Nomor baris data pertama',
		 	],
		 	'online_no_seq_col' => [
		 		'label' => 'Kolom nomor Seq',
		 	],
		 	'online_beacon1_col' => [
		 		'label' => 'Kolom nilai Beacon 1',
		 	],
		 	'online_beacon2_col' => [
		 		'label' => 'Kolom nilai Beacon 2'
		 	],
		 	'online_beacon3_col' => [
		 		'label' => 'Kolom nilai Beacon 3'
		 	]
		 	
		 	]);
		 ?>
		  <div class="form-group">
		    <div class="col-sm-offset-2 col-sm-10">
		      <button type="submit" class="btn btn-primary" data-save-text='<i class="fa fa-cog fa-spin"></i> Saving...'><i class="fa fa-save"></i> Save</button>
		    </div>
		  </div>
    	</form>

	</div>
	<div role="tabpanel" class="tab-pane" id="fuzzy">
		<div class="margin10"></div>
		<div class="alert alert-info" id="alert-online">
			<p>Tentukan nilai batas a, b, c dan bobot untuk masing-masing fuzzy rule untuk penentuan weight euclidean</p>
			</div>
		<form class="form-horizontal" method="post">
		<div class="row">
			<div class="col-md-6"><div class="panel panel-default">
			<div class="panel-heading"> <h3 class="panel-title">Immediate</h3></div>
			<div class="panel-body">
				 <?php 
				 render_option([
				 		'fuzzy_immediate_a' => ['label'=>'Nilai a'],
				 		'fuzzy_immediate_b' => ['label'=>'Nilai b'],
				 		'fuzzy_immediate_weight' => ['label'=>'Bobot', 'type'=>'text'],
				 	]);
				 ?>	
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading"> <h3 class="panel-title">Near</h3></div>
			<div class="panel-body">
				 <?php 
				 render_option([
				 		'fuzzy_near_a' => ['label'=>'Nilai a'],
				 		'fuzzy_near_b' => ['label'=>'Nilai b'],
				 		'fuzzy_near_c' => ['label'=>'Nilai c'],
				 		'fuzzy_near_weight' => ['label'=>'Bobot', 'type'=>'text'],
				 	]);
				 ?>	
			</div>
		</div></div>
			<div class="col-md-6"><div class="panel panel-default">
			<div class="panel-heading"> <h3 class="panel-title">Far</h3></div>
			<div class="panel-body">
				 <?php 
				 render_option([
				 		'fuzzy_far_a' => ['label'=>'Nilai a'],
				 		'fuzzy_far_b' => ['label'=>'Nilai b'],
				 		'fuzzy_far_c' => ['label'=>'Nilai c'],
				 		'fuzzy_far_weight' => ['label'=>'Bobot', 'type'=>'text'],
				 	]);
				 ?>	
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading"> <h3 class="panel-title">Unknown</h3></div>
			<div class="panel-body">
				 <?php 
				 render_option([
				 		'fuzzy_unknown_a' => ['label'=>'Nilai a'],
				 		'fuzzy_unknown_b' => ['label'=>'Nilai b'],
				 		'fuzzy_unknown_weight' => ['label'=>'Bobot', 'type'=>'text'],
				 	]);
				 ?>	
			</div>
		</div></div>
		</div>		
		  <div class="form-group">
		    <div class="col-sm-offset-2 col-sm-10">
		      <button type="submit" class="btn btn-primary" data-save-text='<i class="fa fa-cog fa-spin"></i> Saving...'><i class="fa fa-save"></i> Save</button>
		    </div>
		  </div>
    	</form>
	</div>
	<div role="tabpanel" class="tab-pane" id="alert">
	<div class="margin10"></div>
		<a href="#" class="btn btn-block btn-danger btn-md" id="btn-alert">Show all hidden Alert</a>

		</div>
	</div>
</div>
</div>
<script type="text/javascript">
	$(function(){
		$('#btn-alert').click(function(){
			$.removeCookie('alert-upload-offline');
			$.removeCookie('alert-upload-online');
			alert('Alert state already reset');
			});
		$('.tab-content form').submit(function(e){
			e.preventDefault();
			that = $(this);
			that.find('button[type=submit]').button('save');
			$.ajax({
				url:'save_option.php',
				data: that.serialize(),
				method : 'post',
				dataType : 'json',
			}).done(function(data)
				{
					alert = $('#alert-template').clone();
					if(data.success) 
						alert.attr('class', 'alert alert-success');
					else
						alert.attr('class', 'alert alert-danger');
					alert.append(data.message);
					that.prepend(alert);
					alert.alert();
			}).always(function(){
					that.find('button[type=submit]').button('reset');
				});
		})
	});
</script>
<?php
include('include/footer.php');
?>