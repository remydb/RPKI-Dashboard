<!DOCTYPE html>
<?php
require ('include/gChart.php');
require ('include/functions.php');
require ('include/messages.php');
if (isset($_GET['asn'])){
  $asn = $_GET['asn'];
}
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
if (isset($asn)){
newpichartperas($asn, 'V','I%','U', 'Valid', 'Invalid', 'Unknown', 'Percentages of invalid traffic', 'Chart1');
newpichartperas($asn, 'IA', 'IP', 'IB', 'Invalid AS', 'Invalid Prefix', 'Both Invalid', 'Percentages of invalid traffic', 'Chart2'); 
newlinechart('V', 'I%', 'Title', 'Chart3', $asn);
}
?>
</script>
<script>
function Refresh(id){
location.href="peras.php?asn=" + id
}
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
      <!--<div class="span3 sidebar" id="navparent">
        <ul class="nav nav-list sidenav" data-spy="affix" data-offset-top="200">
          <li><a href="#total"><i class="icon-chevron-right"></i> Total</a></li>
          <li><a href="#invalids"><i class="icon-chevron-right"></i> Invalids</a></li>
        </ul>
      </div>-->
      <!-- Main content
      =============================================== -->
      <div class='span3 main'>
        <!-- AS list
        ============================================= -->
        <section id="total">
          <?php print "$select" ?>
              <form class="navbar-search pull-left">
    <input type="text" class="search-query" placeholder="AS Number" id="searchfield"/>
    </form>
        </section>
      </div>
        <?php
	if (isset($asn)){
	  echo "<div class='span9 main'>
          <section id='charts'>
          <div class='page-header'>
            <h1>Charts for AS$asn</h1>
          </div>
          <div id='Chart1'></div>";
            
            if (query_totals_peras('I%', $asn) != 0){
            echo "<div id='Chart2'></div>
		<div id='Chart3'></div>";
            }
	newtable($asn);  
	echo "</section></div>";
        }
            ?>
    </div>
  </div>
<script type="text/javascript" src="bootstrap/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#searchfield").typeahead({
        minLength: 1,
        source: function(query, process) {
            $.post('getaslist.php', { q: query, limit: 8 }, function(data) {
                process(JSON.parse(data));
            });

        },
        updater: function (item) {
            document.location = "peras.php?asn=" + encodeURIComponent(item);
            return item;
        }
    });
});
</script>
</body>
</html>
