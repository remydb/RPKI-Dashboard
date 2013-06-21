<!DOCTYPE html>
<?php
require ('include/functions.php');
$vartotal = query_total_prefixes('%');
$varunknown = query_total_prefixes('U');
$varinvalid = query_total_prefixes('I%');
$varvalid = query_total_prefixes('V');
$varvalidation = $vartotal - $varunknown;
?>
<head>
<title>RPKI Dashboard</title>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href="bootstrap/css/custom.css" rel="stylesheet">
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1', {packages: ['corechart']});
<?php
newlinechart('V', 'I%', 'Title', 'Chart1','%', '%', 'Valid', 'Invalid');
newlinechart('%', 'U', 'Title', 'Chart2','%', '%', 'Total BGP prefixes', 'Unknown');
?>
</script>
</head>
<body>

  <!-- Navbar stuff
  =================================================== -->
  <?php
  include 'include/navbar.php';
  ?>

  <!-- Header stuff
  =================================================== -->
  <header class="jumbotron subhead" id="overview">
  <div class="container">
    <h1>RPKI Dashboard</h1>
    <p class="lead">Stuffs</p>
  </div>
  </header>
 
  <!-- Body stuff
  =================================================== -->
  <div class='container'>
    <div id='content' class='row-fluid'>
    
 <!-- Sidebar
 =============================================== -->
      <div class="span3 sidebar" id="navparent">
        <ul class="nav nav-list sidenav" data-spy="affix" data-offset-top="200">
          <li><a href="#valids"><i class="icon-chevron-right"></i> History of (in)valids</a></li>
        </ul>
      </div>


 <!-- Main content
 =============================================== -->
       <div class='span9 main'>
       <h2>Trends</h2>
       <div class="well" id="valids">From the <span class="badge badge-success"><?php echo $vartotal; ?></span>
        prefixes that are currently in the routing table, 
        <span class="badge badge-success"><?php echo $varvalidation; ?></span> match at least
        one ROA. From these matched prefixes <span class="badge badge-important"><?php echo $varinvalid; ?></span>
        are <strong>invalid</strong> while <span class="badge badge-success"><?php echo $varvalid; ?></span> are valid. The 
        line chart below shows the valid and invalid routes over the course of time.
      </div>
	<div id='Chart1'></div>
  <div id='Chart2'></div>
      </div>
    </div>
  </div>
<script type="text/javascript" src="bootstrap/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
