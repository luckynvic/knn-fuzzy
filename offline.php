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
            Offline Data
            <small>Upload &amp; manage</small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="index.php">Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-table"></i> Offline Data
            </li>
        </ol>
    </div>
</div>
<div class="alert alert-warning" id="alert-upload">
<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Data lama akan secara otomatis ditimpa dengan data baru yang diupload
</div>
<form method="post" enctype="multipart/form-data"   >
<div class="row">
    <div class="col-lg-10">
        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
          <div class="form-control" data-trigger="fileinput"><i class="fa fa-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
          <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Select file</span><span class="fileinput-exists">Change</span><input type="file" name="file"></span>
          <a href="#" class="input-group-addon btn btn-danger fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i></a>
        </div>
    </div>
    <div class="col-lg-2">
        <button type="submit" name="upload" class="btn btn-primary btn-block"><i class="fa fa-upload"></i> Upload</button>
    </div>
</div>
</form>
<?php
//process upload data
if(isset($_POST['upload'])) {
    include('lib/simple-xlsx/simplexlsx.class.php');
    $db = getDb();
    $xlsx = new SimpleXLSX($_FILES['file']['tmp_name']);
    list($num_cols, $num_rows) = $xlsx->dimension();
    $rows = $xlsx->rows();
    $db->exec('delete from mst_offline');
    //option
    $begin_row = (int)get_option('offline_begin_line',0) - 1;
    if($begin_row == -1)
        throw new Exception("No Offline Template Setting Availabel");
        
    $position_col = (int)get_option('offline_position_col',1) - 1;
    $x_col = (int)get_option('offline_x_col',2) - 1;
    $y_col = (int)get_option('offline_y_col',3) - 1;
    $beacon1_col = (int)get_option('offline_beacon1_col',3) - 1;
    $beacon2_col = (int)get_option('offline_beacon2_col',4) - 1;
    $beacon3_col = (int)get_option('offline_beacon3_col',5) - 1;
    $db->beginTransaction();
    try {
        for($row = $begin_row; $row<count($rows); $row++)
        {
            if(!isset($rows[$row][$position_col]) || $rows[$row][$position_col]=='')
                continue;            

            $stmt = $db->prepare('insert into mst_offline (position, x, y, beacon1, beacon2, beacon3)
                    values (:position, :x, :y, :beacon1, :beacon2, :beacon3)
                ');
            $stmt->execute([
                ':position' => $rows[$row][$position_col],
                ':x' => $rows[$row][$x_col],
                ':y' => $rows[$row][$y_col],
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
$result = $db->query('select * from mst_offline');
 ?>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>X</th>
                    <th>Y</th>
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
                    <td><?php echo $row['position'] ?></td>
                    <td><?php echo $row['x'] ?></td>
                    <td><?php echo $row['y'] ?></td>
                    <td><?php echo $row['beacon1'] ?></td>
                    <td><?php echo $row['beacon2'] ?></td>
                    <td><?php echo $row['beacon3'] ?></td>

                </tr>
                <?php
            } //close foreach
                 } else {?>
                <tr>
                    <td colspan="6">Data are empty</td>
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
        if($.cookie('alert-upload-offline')==0)
            $('#alert-upload').alert('close');
        $('#alert-upload').on('closed.bs.alert', function () {
            $.cookie('alert-upload-offline',0);
        });
    });
</script>
<?php
include('include/footer.php');
?>