<!DOCTYPE html>
<?php
require ('include/gChart.php');
require ('include/functions.php');
require ('include/messages.php');
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
newbarchart('ASN', 'V', 'Number of valid routes announced', 'AS', 'Chart1', 10);
newbarchart('ASN', 'I%', 'Number of invalid routes announced', 'AS', 'Chart2', 10);
newbarchart('RIR', 'V', 'Number of valid routes announced', 'AS', 'Chart3', 6);
newbarchart('RIR', 'I%', 'Number of invalid routes announced', 'AS', 'Chart4', 6);
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
          <li><a href="#AS"><i class="icon-chevron-right"></i> Per AS</a></li>
          <li><a href="#country"><i class="icon-chevron-right"></i> Per country</a></li>
        </ul>
      </div>
      <!-- Main content
      =============================================== -->
      <div class='span9 main'>
        <!-- Per AS
        ============================================= -->
        <section id="AS">
          <div class="page-header">
            <h1>Breakdown per AS</h1>
          </div>
        <?php print "$placeholder" ?>
	<h2>Most valid prefix announcements per AS.</h2>
	<div id="Chart1"></div>
        <h2>Most invalid prefix announcements per AS</h2>
        <div id="Chart2"></div>
        </section>

        <!-- Per country
        ============================================= -->
        <section id="country">
          <div class="page-header">
            <h1>Breakdown per RIR</h1>
       	<?php
	rirtable();
	?>   
	</div>
        <h2>Most valid prefix announcements per RIR</h2>
        <div id="Chart3"></div>
        <h2>Most invalid prefix announcements per RIR</h2>
        <div id="Chart4"></div>
        </section>
      </div>
    </div>
  </div>
<script type="text/javascript" src="bootstrap/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
