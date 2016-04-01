<?php
include('include/config.php');
include('include/common_function.php');
include('include/header.php');
?>
<link rel="stylesheet" type="text/css" href="css/plugins/fileinput.css">
<div class="container-fluid">
<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Online Data
            <small>Upload &amp; manage</small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="index.php">Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-location-arrow"></i> Online Data
            </li>
        </ol>
    </div>
</div>
<div class="alert alert-success hide" id="alert-euclidean">
<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Data Euclidean distance telah di hitung.<br>
<a href="index.php#euclidean" class="btn btn-primary pull-right"><i class="fa fa-open-eye"></i> Lihat Hasil</a>
<div class="clearfix"></div>
</div>

<div class="alert alert-warning" id="alert-upload">
<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Data lama akan secara otomatis ditimpa dengan data baru yang diupload
</div>
<form method="post" enctype="multipart/form-data" id="upload-form"  >
<div class="row">
    <div class="col-lg-5">
        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
          <div class="form-control" data-trigger="fileinput"><i class="fa fa-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
          <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Select file</span><span class="fileinput-exists">Change</span><input type="file" name="file"></span>
          <a href="#" class="input-group-addon btn btn-danger fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i></a>
        </div>
    </div>
    <div class="col-lg-2">
    <div class="input-group">
      <span class="input-group-addon">Sheet</span>
      <input type="number" class="form-control" placeholder="Sheet" value="1" name="sheet">
    </div>
    </div>
    <div class="col-lg-2 checkbox">
    <label>
    <input type="checkbox" name="clear">Clear Previous Data
    </label>
    </div>
    <div class="col-lg-3">
        <div class="btn-group pull-right">
            <button type="submit" name="upload" class="btn btn-primary"><i class="fa fa-upload"></i> Upload</button>
            <a href="#" class="btn btn-success" id ="btn-euclidean" data-gen-text="<i class='fa fa-refresh fa-spin'></i> Generating..."><i class="fa fa-refresh"></i> Gen Euclidean</a>
        </div>
    </div>
</div>
</form>
<?php
//process upload data
if(isset($_POST['upload'])) {
    include('lib/simple-xlsx/simplexlsx.class.php');
    $db = getDb();
    $xlsx = new SimpleXLSX($_FILES['file']['tmp_name']);
    $sheet = isset($_POST['sheet'])?(int)$_POST['sheet']:1;
    $clear = isset($_POST['clear']);

    list($num_cols, $num_rows) = $xlsx->dimension($sheet);
    $rows = $xlsx->rows($sheet);

    if($clear)
    $db->exec('delete from mst_online');
    //option
    $begin_row = (int)get_option('online_begin_line',0) - 1;
    if($begin_row == -1)
        throw new Exception("No Online Template Setting Availabel");
        
    $seq_col = (int)get_option('online_no_seq_col',7) - 1;
    $beacon1_col = (int)get_option('online_beacon1_col',8) - 1;
    $beacon2_col = (int)get_option('online_beacon2_col',9) - 1;
    $beacon3_col = (int)get_option('online_beacon3_col',10) - 1;

    $db->beginTransaction();
    try {
        for($row = $begin_row; $row<count($rows); $row++)
        {
            if(!isset($rows[$row][$seq_col]) || $rows[$row][$seq_col]=='')
                continue;            

            $stmt = $db->prepare('insert into mst_online (seq, beacon1, beacon2, beacon3)
                    values (:seq, :beacon1, :beacon2, :beacon3)
                ');
            $stmt->execute([
                ':seq' => $rows[$row][$seq_col],
                ':beacon1' => $rows[$row][$beacon1_col],
                ':beacon2' => $rows[$row][$beacon2_col],
                ':beacon3' => $rows[$row][$beacon3_col]
                ]);
        }
        $db->commit();
        echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
        . 'Data uploaded successfully</div>';
    } catch(Exception $e){
        echo 
        '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
        . $e->getMessage()
        . '</div>';
        
        $db->rollback();
    }

}
?>

<!-- DISPLAY DATA -->
<div class="margin10"></div>
<?php 
$result = $db->query('select * from mst_online order by seq');
 ?>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>No Seq</th>
                    <th>Beacon1</th>
                    <th>Beacon2</th>
                    <th>Beacon3</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if($result!==false) {
                foreach ($result as $row) {
            ?>
                <tr>
                    <td><?php echo $row['seq'] ?></td>
                    <td><?php echo $row['beacon1'] ?></td>
                    <td><?php echo $row['beacon2'] ?></td>
                    <td><?php echo $row['beacon3'] ?></td>

                </tr>
                <?php
            } //close foreach
                 } else {?>
                <tr>
                    <td colspan="3">Data are empty</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<script type="text/javascript" src="js/plugins/fileinput/fileinput.js"></script>
<script type="text/javascript">
    $(function() {
        if($.cookie('alert-upload-online')==0)
            $('#alert-upload').alert('close');
        $('#alert-upload').on('closed.bs.alert', function () {
            $.cookie('alert-upload-online',0);
        });
        $('#btn-euclidean').click(function(e){
            e.preventDefault();
            $('#btn-euclidean').button('gen');
            $.ajax({
                url : 'gen_euclidean.php',
                dataType : 'json',
            }).done(function(data){
                alert = $('#alert-euclidean').clone();
                if(data.success) {
                    alert.attr('class','alert alert-success');
                } else
                {
                    alert.attr('class','alert alert-danger');
                    alert.html(data.message);
                }
                alert.appendTo('#upload-form');
                alert.alert();
            }).complete(function(){
                $('#btn-euclidean').button('reset')
            })

        });
    });
</script>
<?php
include('include/footer.php');
?>