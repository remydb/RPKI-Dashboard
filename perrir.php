<!DOCTYPE html>
<?php
require ('include/functions.php');
if (isset($_GET['rir'])){
  $rir = $_GET['rir'];
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
if (isset($rir)){
  newpichartperrir($rir, 'V','I%','U', 'Valid', 'Invalid', 'Unknown', 'Percentages of invalid traffic', 'Chart1');
  newpichartperrir($rir, 'IA', 'IP', 'IQ', 'Invalid AS', 'Invalid Prefix (Fixed length mismatch)', 'Invalid Prefix (Range length exceeded)', 'Cause of invalids', 'Chart2', 'IB', 'AS & Prefix mismatch', 'V', 'Valid'); 
  newlinechart('V', 'I%', 'Title', 'Chart3', '%', $rir);
}?>
</script>
<script>
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
        <!-- RIR list
        ============================================= -->
         <div class="well">Select a RIR below to view the corresponding charts:</div>
	<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Loading the new page might take a few seconds because graphs are generated on demand, <b>please wait.</b></div>   
	<select name="rir" [B] onChange="Refresh(this.value)"[/B]>
            <option> </option>
            <option value='afrinic'>AFRINIC</option>
            <option value='apnic'>APNIC</option>
            <option value='arin'>ARIN</option>
            <option value='lacnic'>LACNIC</option>
            <option value='ripe'>RIPE</option>
	</select>
        <section id="total">
        </section>
      </div>
	<div class='span9 main'>
        <!-- Per AS
        ============================================= -->
        <?php
          $upperrir = strtoupper($rir);
          if (isset($rir)){
            echo "<section id='charts'>
                  <div class='page-header'>
                    <h1>Charts for $upperrir</h1>
                  </div>
                  <div id='Chart1'></div>";
                    
                    if (query_totals_per_rir('I%', $rir) != 0){
                    echo "<div id='Chart2'></div>
            <div id='Chart3'></div></section>";
                    };
          echo "</div>";
        }
        else{
          echo "<div class='page-header'>
                    <h1>Breakdown per RIR</h1>
                  </div>";
        rirtable();
        }
        ?>        
       </div> 
	</section>
      </div>
    </div>
<script type="text/javascript" src="bootstrap/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
<script class="jsbin" src="bootstrap/js/jquery.dataTables.nightly.js"></script>
<script type="text/javascript" src="bootstrap/js/DT_bootstrap.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#pagetable').dataTable( {

      "sPaginationType": "bootstrap"
    } );
});
function Refresh(id){
location.href="perrir.php?rir=" + id
}
</script>
</body>
</html>
