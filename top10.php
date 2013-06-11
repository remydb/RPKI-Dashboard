<!DOCTYPE html>
<?php
require ('include/gChart.php');
require ('include/functions.php');
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
newbarchart('ASN', 'V', 'Top 10 ASes for valid routes', 'AS', 'Chart1');
newbarchart('ASN', 'I%', 'Top 10 ASes for invalid routes', 'AS', 'Chart2');
newbarchart('Country', 'V', 'Top 10 countries for valid routes', 'AS', 'Chart3');
newbarchart('Country', 'I%', 'Top 10 countries for invalid routes', 'AS', 'Chart4');
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
        <h2>Best</h2>
        <div id="Chart1"></div>
        <h2>Worst</h2>
        <div id="Chart2"></div>
        </section>

        <!-- Per country
        ============================================= -->
        <section id="country">
          <div class="page-header">
            <h1>Breakdown per country</h1>
          </div>
        <h2>Best</h2>
        <div id="Chart3"></div>
        <h2>Worst</h2>
        <div id="Chart4"></div>
        </section>
      </div>
    </div>
  </div>
<script type="text/javascript" src="bootstrap/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
