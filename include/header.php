<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $page_title; ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <script src="js/plugins/cookie/jquery.cookie.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
    <div id="wrapper">
 <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><?php echo $app_name; ?></a>
            </div>

            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li <?php if($route=='index') echo 'class="active"'; ?>>
                        <a href="index.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li <?php if($route=='online') echo 'class="active"'; ?>>
                        <a href="online.php"><i class="fa fa-fw fa-location-arrow"></i> Online Data</a>
                    </li>
                    <li <?php if($route=='offline') echo 'class="active"'; ?>>
                        <a href="offline.php"><i class="fa fa-fw fa-table"></i> Offline Data</a>
                    </li>
                    <li <?php if($route=='setting') echo 'class="active"'; ?>>
                        <a href="setting.php"><i class="fa fa-fw fa-cog"></i> Setting</a>
                    </li>
                    <li <?php if($route=='about') echo 'class="active"'; ?>>
                        <a href="about.php"><i class="fa fa-fw fa-info"></i> About</a>
                    </li>
                    
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>
<!--             <li role="presentation" <?php if($route=='index') echo 'class="active"'; ?>><a href="index.php">Home</a></li>
            <li role="presentation" <?php if($route=='gen') echo 'class="active"'; ?>><a href="gen.php">Text Generator</a></li>
            <li role="presentation" <?php if($route=='about') echo 'class="active"'; ?>><a href="about.php">About</a></li>
          </ul>
        </nav>
        <h3 class="text-muted"><?php echo $app_name; ?></h3>
      </div>
 -->
       <div id="page-wrapper">