<?php
	require ('volunteermanagement.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Afya Data Analysis Dashboard</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

    <!-- Bootstrap Core CSS -->
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="../dist/css/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="../bower_components/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart","table"]});

        /*
        4D4D4D (gray)
        5DA5DA (blue)
        FAA43A (orange)
        60BD68 (green)
        F17CB0 (pink)
        B2912F (brown)
        B276B2 (purple)
        DECF3F (yellow)
        F15854 (red)*/

      var colors= ['#5DA5DA', '#FAA43A', '#60BD68', '#F17CB0', '#B2912F', '#B276B2', '#DECF3F', '#F15854', '#4d4d4d'];
      var sampleData= [["Church", 10], ["Individuals", 50], ["University", 3], ["austin", 1]];
      var sampleData2=[];

      for (var i=0; i<30; i++){
        var arr= [new Date(2003, 5, i+1), Math.floor(Math.random()*100+1)];
        sampleData2.push(arr);
      }


    function JSONToCSVConvertor(JSONData, ShowLabel) {     

        //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
        var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
        var CSV = '';    
        //This condition will generate the Label/Header
        if (ShowLabel) {
            var row = "";

            //This loop will extract the label from 1st index of on array
            for (var index in arrData[0]) {
                //Now convert each value to string and comma-seprated
                row += index + ',';
            }
            row = row.slice(0, -1);
            //append Label row with line break
            CSV += row + '\r\n';
        }

        //1st loop is to extract each row
        for (var i = 0; i < arrData.length; i++) {
            var row = "";
            //2nd loop will extract each column and convert it in string comma-seprated
            for (var index in arrData[i]) {
                row += '"' + arrData[i][index] + '",';
            }
            row.slice(0, row.length - 1);
            //add a line break after each row
            CSV += row + '\r\n';
        }

        if (CSV == '') {        
            alert("Invalid data");
            return;
        }   

        //this trick will generate a temp "a" tag
        var link = document.createElement("a");    
        link.id="lnkDwnldLnk";

        //this part will append the anchor tag and remove it after automatic click
        document.body.appendChild(link);

        var csv = CSV;  
        blob = new Blob([csv], { type: 'text/csv' }); 
        var csvUrl = window.webkitURL.createObjectURL(blob);
        var filename = 'UserExport.csv';
        $("#lnkDwnldLnk")
        .attr({
            'download': filename,
            'href': csvUrl
        }); 

        $('#lnkDwnldLnk')[0].click();    
        document.body.removeChild(link);
      }



     function drawLineChartMonth(data, id, groupName) {
        var header = ['Day of the Month', groupName];

        data.unshift(header);
        var data = google.visualization.arrayToDataTable(data);

        var options = {
          title: groupName+" Total Hours",
          //curveType: 'function',
          legend: { position: 'bottom' },
          explorer:{axis: 'horizontal', maxZoomOut: 2},
          vAxis: {title:"Hours"}
        };

        var chart = new google.visualization.LineChart(document.getElementById(id));

        chart.draw(data, options);
      }


    </script>
    <script type="text/javascript" src="../../AJAX/getData.js"></script>
    <script>
                                /*$(document).ready(function(){
                                        $('.agroup').click(function(){
                                        get_group_hours($(this).text()).success(function(msg) {
                                            console.log(msg);
                                        });   
                                });
                                    });*/
        dataAjaxHanderURL = '../../AJAX/getData.php';
        function agroupClick(el){
            var name= $(el).text();
           get_group_timesheet(name).success(function(msg){
            drawLineChartMonth(msg, 'linechart', name);  
            $("#download").click(function(){
                JSONToCSVConvertor(msg, false);    
                })
           }); 
        }
                                </script>
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">AFYA Data Analytics</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="login.html"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        <li>
                            <a href="index.html"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Charts<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="PieGraphs.php">Pie Charts</a>
                                </li>
								<li>
                                    <a href="LineGraphs.php">Line Graphs</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="tables.php"><i class="fa fa-table fa-fw"></i> Tables</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Line Graphs: Individual or Group Contributions Over Time</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
							<?php 
								if(empty($_GET['type'])){
									print 'Line Chart Example';
								}
								else{
									print 'Line Chart: Total Volunteered Hours of ';
									print $_GET['type'];
								}
							?>
							<!--<form method="GET" action="Graphs.php">-->
							<div class="form-group">
								<div class="btn-group" role="group" aria-label="...">
								  	<div class="btn-group" role="group">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									  Choose a Group
									  <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
									<?php
										$db = new volunteermanagementsystem('../../volunteerdatabase.db');
										$groups = $db->{'allgroups'}();
										$arow = $groups->fetchArray();
										foreach($arow as $agroup){
											echo "<li class='agroup' onclick='agroupClick(this);'><a href='#'>$agroup</a></li>";
										}
									?>
									</ul>
								  </div>
								</div>
							</div>
							<!--<input type="submit">-->
							<!--</form>-->
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="linechart" style="width: 900px; height: 500px;"></div>
                        </div>
                        <div class="panel-body">
                            <button id="download" class="btn btn-primary" >Download to CSV</button>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
	</div>
    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>