<!DOCTYPE html>
<?php
require ('include/functions.php');
$vartotal = query_total_prefixes('%');
$varunknown = query_total_prefixes('U');
$varinvalid = query_total_prefixes('I%');
$varvalid = query_total_prefixes('V');
$varvalidation = $vartotal - $varunknown;
$varpercent = round($varvalidation / $vartotal * 100, 2);
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
newpichart('V','I%','U', 'Valid', 'Invalid', 'Unknown', 'Percentages of invalid traffic', 'Chart1');
newpichart2('IA', 'IP', 'IB', 'V', 'Invalid AS', 'Invalid Prefix', 'Both Invalid', 'Valid', 'Percentages of invalid traffic', 'Chart2'); 
?>
</script>
</head>
<body data-target="#navparent" data-offset="40" data-spy="scroll">

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
    <div class='row-fluid' id='content'>
      <!-- Sidebar
      =============================================== -->
      <div class="span3 sidebar" id="navparent">
        <ul class="nav nav-list sidenav" data-spy="affix" data-offset-top="200">
          <li><a href="#total"><i class="icon-chevron-right"></i> Total</a></li>
          <li><a href="#invalids"><i class="icon-chevron-right"></i> Invalids</a></li>
        </ul>
      </div>
      <!-- Main content
      =============================================== -->
      <div class='span9 main'>
	<!-- Pie chart
        ============================================= -->
        <section id="total">
          <div class="page-header">
            	<h1>Distribution of RPKI states</h1>
	</div>
        <?php echo "<div class=\"well\">This page provides an overview of the current state of <strong>RPKI adoption</strong>.
Yesterdays routing table holds <span class=\"badge badge-success\">$vartotal</span> prefixes. The
valdation state has been determined for <span class=\"badge badge-success\">$varvalidation</span>
<strong>prefixes</strong>. This means that <span class=\"badge badge-success\">$varpercent%</span>
of the prefixes in the routing table is <strong>configured for RPKI.</strong></div>"; ?>
	<div id="Chart1"></div>
        </section>

        <!-- Per country
        ============================================= -->
        <section id="invalids">
          <div class="page-header">
            <h1>Distribution of invalids</h1>
          </div>
          <?php echo "<div class=\"well\">From the <span class=\"badge badge-success\">$varvalidation</span> prefixes that
are <strong>configured to use RPKI</strong>, <span class=\"badge badge-important\">$varinvalid</span>
are <strong>invalid</strong>. The <strong>reason</strong> for these <strong>invalidated prefixes</strong>
is shown in the <strong>pie chart below.</strong></div>"; ?>
	<div id="Chart2"></div>
        </section>
      </div>
    </div>
  </div>
<script type="text/javascript" src="bootstrap/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
