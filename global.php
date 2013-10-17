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
<html lang="en">
<head>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache"> 
<title>RPKI Dashboard</title>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href="bootstrap/css/custom.css" rel="stylesheet">
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1', {packages: ['corechart']});
<?php
newpichart('V','I%','U', 'Valid', 'Invalid', 'Unknown', 'Distrubion of validation states', 'Chart1');
newpichart2('IA', 'IP', 'IQ', 'IB', 'V', 'Invalid AS', 'Invalid Prefix (Fixed length mismatch)', 'Invalid Prefix (Range length exceeded)', 'AS & Prefix mismatch', 'Valid', 'Distribution of invalid prefixes', 'Chart2'); 
?>
</script>
</head>
<body data-target="#navparent" data-offset="40" data-spy="scroll">
<div id='wrap'>
  <!-- Navbar stuff
  =================================================== -->
  <?php
  include 'include/navbar.php';
  ?>

  <!-- Header stuff
  =================================================== -->
  <?php
  include 'include/header.php';
  ?>

  <!-- Body stuff
  =================================================== -->
  <div class='container'>
    <div class='row-fluid' id='content'>
      <!-- Sidebar
      =============================================== -->
      <div class="span3 sidebar" id="navparent">
        <ul class="nav nav-list sidenav" data-spy="affix" data-offset-top="200">
          <li><a href="#total"><i class="icon-chevron-right"></i> BGP overview</a></li>
          <li><a href="#invalids"><i class="icon-chevron-right"></i> RPKI overview</a></li>
        </ul>
      </div>
      <!-- Main content
      =============================================== -->
      <div class='span9 main'>
	<!-- Pie chart
        ============================================= -->
        <section id="total">
          <div class="page-header">
            	<h1>Validation states</h1>
	</div>
        <div class="well">This page provides an overview of the current state of <strong>RPKI adoption</strong>.
Todays routing table holds <span class="badge badge-success"><?php echo $vartotal;?></span> prefixes. The
valdation state has been determined for <span class="badge badge-success"><?php echo $varvalidation;?></span>
<strong>prefixes</strong>. This means that <span class="badge badge-success"><?php echo $varpercent;?>%</span>
of the prefixes in the routing table are <strong>validated</strong>.</div>
	<div id="Chart1"></div>
        </section>

        <!-- Per country
        ============================================= -->
        <section id="invalids">
          <div class="page-header">
            <h1>Invalid prefixes</h1>
          </div>
          <div class="well">From the <span class="badge badge-success"><?php echo $varvalidation;?></span> prefixes that
are <strong>validated</strong>, <span class="badge badge-important"><?php echo $varinvalid;?></span>
are <strong>invalid</strong>. The <strong>reason</strong> for these <strong>invalidated prefixes</strong>
is shown in the <strong>pie chart below.</strong></div>
	<div id="Chart2"></div>
        </section>
      </div>
    </div>
  </div>
    <div id='push'></div>
</div>

    <footer class="footer">
      <div class="container">
        <?php include 'include/footer.php';?>
      </div>
    </footer>
<div id="waitpopup" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="Label" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h1>Please wait</h1>
  </div>
  <div class="modal-body"><div class="alert alert-info">The following page is relatively large, so it may take some time to load.</div></div>
</div>
<script type="text/javascript" src="bootstrap/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
