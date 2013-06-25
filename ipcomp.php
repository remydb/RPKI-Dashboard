<!DOCTYPE html>
<?php
require ('include/functions.php');
?>
<html lang="en">
<head>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache"> 
<title>RPKI Dashboard - IPv4/6</title>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href="bootstrap/css/custom.css" rel="stylesheet">
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1', {packages: ['corechart']});
<?php
newpichart('V','I%','U', 'Valid', 'Invalid', 'Unknown', 'Current state of RPKI adoption for IPv4', 'Chart1', '4');
newpichart2('IA', 'IP', 'IQ', 'IB', 'V', 'Invalid AS', 'Invalid Prefix (Fixed length mismatch)', 'Invalid Prefix (Range length exceeded)', 'AS & Prefix mismatch', 'Valid', 'Distribution of RPKI prefixes for IPv4', 'Chart2', '4');
newpichart('V','I%','U', 'Valid', 'Invalid', 'Unknown', 'Current state of RPKI adoption for IPv6', 'Chart3', '6');
newpichart2('IA', 'IP', 'IQ', 'IB', 'V', 'Invalid AS', 'Invalid Prefix (Fixed length mismatch)', 'Invalid Prefix (Range length exceeded)', 'AS & Prefix mismatch', 'Valid', 'Distribution of RPKI prefixes for IPv6', 'Chart4', '6');
newpichartperip('%', 'Total amount of BGP prefixes', 'Chart5');
newpichartperip('V', 'Total amount of valid prefixes', 'Chart6');
newpichartperip('I%', 'Total amount of invalid prefixes', 'Chart7');
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
      =============================================== 
      <div class="span3 sidebar" id="navparent">
        <ul class="nav nav-list sidenav" data-spy="affix" data-offset-top="200">
          <li><a href="#ipv4"><i class="icon-chevron-right"></i> IPv4</a></li>
          <li><a href="#ipv6"><i class="icon-chevron-right"></i> IPv6</a></li>
          <li><a href="#comparison"><i class="icon-chevron-right"></i> Comparison</a></li>          
        </ul>
      </div>-->
      <!-- Main content
      =============================================== -->
      <div class='span12 main'>
          <div class="page-header">
            <h1>Comparison of IPv4 vs IPv6</h1>
          </div>
          <div class="well">This page shows a comparison between <strong>IPv4</strong> and <strong>IPv6</strong> regarding RPKI adoption.</div>
          <div class='row-fluid'>
            <div class='span6'>
        <section id="ipv4">
          <h2>IPv4</h2>
          <div id="Chart1"></div>
          <div id="Chart2"></div>
        </section>
      </div>
      <div class='span6'>
        <section id="ipv6">
          <h2>IPv6</h2>
          <div id="Chart3"></div>
          <div id="Chart4"></div>
        </section>
      </div>
      </div>
        <section id="comparison">
          <h2>Comparison</h2>
          <div id="Chart5"></div>
          <div id="Chart6"></div>
          <div id="Chart7"></div>
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
<script type="text/javascript" src="bootstrap/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
