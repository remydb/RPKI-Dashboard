<!DOCTYPE html>
<?php
require ('include/functions.php');
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
newbarchart('ASN', 'V', 'Number of valid routes announced', 'AS', 'Chart1', 10);
newbarchart('ASN', 'I%', 'Number of invalid routes announced', 'AS', 'Chart2', 10);
//newrirbarchart('RIR', 'V', 'Number of valid routes announced', 'AS', 'Chart3', 6);
//newrirbarchart('RIR', 'I%', 'Number of invalid routes announced', 'AS', 'Chart4', 6);

//newrirbarchart('AFRINIC', 'Chart3');
//newrirbarchart('APNIC', 'Chart4');
//newrirbarchart('ARIN', 'Chart5');
//newrirbarchart('LACNIC', 'Chart6');
//newrirbarchart('RIPE', 'Chart7');
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
      <!--
      <div class="span3 sidebar" id="navparent">
        <ul class="nav nav-list sidenav" data-spy="affix" data-offset-top="200">
          <li><a href="#AS"><i class="icon-chevron-right"></i> Per AS</a></li>
          <li><a href="#RIR"><i class="icon-chevron-right"></i> Per RIR</a></li>
        </ul>
      </div>
      -->
      <!-- Main content
      =============================================== -->
      <div class='span9 main'>
        <!-- Per AS
        ============================================= -->
        <section id="AS">
          <div class="page-header">
            <h1>Prefix announcement Top 10</h1>
          </div>
	<div class="well">The following charts show an overview of the autonomous systems that 
          announce the <strong>most validated prefixes</strong>. These charts are generated daily using the most up to date
          routing tables for IPv4 and IPv6 provided by <a href="http://www.ris.ripe.net/dumps/">RIPE NCC</a>.<br />
          Click on a <strong>bar</strong> or <strong>legend</strong> item to view additional information
          for the corresponding AS number.</div>
	<div id="Chart1"></div>

        <div id="Chart2"></div>
        </section>

        <!-- Per RIR
        ============================================= -->
        <!--
        <section id="RIR">
          <div class="page-header">
            <h1>Breakdown per RIR</h1>
	        </div>
          <div class="well">The following charts show an overview of <strong>valid and invalid prefixes for each RIR</strong>.</div>
        <div id="Chart3"></div>
        <div id="Chart4"></div>
        <div id="Chart5"></div>
        <div id="Chart6"></div>
        <div id="Chart7"></div>
        </section>
        -->
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
  <div class="modal-body"><div class="alert alert-info">The following page is generated on-the-fly, so it may take some time to load.</div></div>
</div>
<script type="text/javascript" src="bootstrap/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
