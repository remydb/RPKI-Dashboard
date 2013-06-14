<!DOCTYPE html>
<?php
require ('include/functions.php');
?>
<head>
<title>RPKI Dashboard</title>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href="bootstrap/css/custom.css" rel="stylesheet">
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1', {packages: ['geochart']});
<?php
newgeochart('V', 'Valid routes per country', 'Chart1');
newgeochart('I%', 'Valid routes per country', 'Chart2');
newgeochart('U', 'Unknown routes per country', 'Chart3');
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
          <li><a href="#valid"><i class="icon-chevron-right"></i> Valid</a></li>
          <li><a href="#invalid"><i class="icon-chevron-right"></i> Invalid</a></li>
          <li><a href="#unknown"><i class="icon-chevron-right"></i> Unknown</a></li>
        </ul>
      </div>
      <!-- Main content
      =============================================== -->
      <div class='span9 main'>
        <!-- Pie chart
        ============================================= -->
        <section id="valid">
          <div class="page-header">
            <h1>Percent valid routes per RIR</h1>
		        <div class=\"well\"> To determine the geographical location of an IPv4-prefix we inspect an IP to see which
            /8 it belongs to. We then match this /8 to the appropriate RIR and match each RIR to a group of countries.</div>
          </div>
          <div id="Chart1"></div>
        </section>
        <section id="invalid">
          <div class="page-header">
            <h1>Percent invalid routes per RIR</h1>
          </div>
          <div id="Chart2"></div>
        </section>
        <section id="Unknown">
          <div class="page-header">
            <h1>Percent unknown routes per RIR</h1>
            <?php print "$location"?>
          </div>
          <div id="Chart3"></div>
        </section>
      </div>
    </div>
  </div>
<script type="text/javascript" src="bootstrap/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
