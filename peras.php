<!DOCTYPE html>
<?php
require ('include/functions.php');
if (isset($_GET['asn'])){
  $asn = $_GET['asn'];
}
?>
<html lang="en">
<head>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache"> 
<title>RPKI Dashboard</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="bootstrap/css/custom.css" rel="stylesheet">
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1', {packages: ['corechart']});
<?php
if (isset($asn)){
newpichartperas($asn, 'V','I%','U', 'Valid', 'Invalid', 'Unknown', 'Percentages of (in)valid traffic', 'Chart1');
newpichartperas($asn, 'IA', 'IP', 'IQ', 'Invalid AS', 'Invalid Prefix (Fixed length mismatch)', 'Invalid Prefix (Range length exceeded)', 'Cause of invalids', 'Chart2', 'IB', 'AS & Prefix mismatch', 'V', 'Valid'); 
newlinechart('V', 'I%', 'Amount of valid and invalid prefixes', 'Chart3', $asn, '%', 'Valid', 'Invalid');
//newinvalidlinechart('Causes of invalids', 'Chart4', $asn);
}
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
          <div class="well">Please <strong>select an AS number </strong>from the dropdown list below.</div>
        <?php if (!isset($asn)){echo "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>Loading the new page might take a few seconds because graphs are generated on demand, <b>please wait.</b></div>";}?>  
	<div>
		<form class="navbar-search pull-left">
		<input type="text" class="search-query" placeholder="AS Number" id="searchfield" autocomplete="off"/>
    		</form>
	</div>
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
		<div id='Chart3'></div>
    <div id='Chart4'></div></section>";
            };
  echo "<div id='dat_table'>";
	newtable($asn);  
  echo "</div>";
	echo "</div>";
        }
            ?>
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
<script class="jsbin" src="bootstrap/js/jquery.dataTables.nightly.js"></script>
<script type="text/javascript" src="bootstrap/js/DT_bootstrap.js"></script>
<script type="text/javascript">
var buttonid;
$(document).ready(function() {

    $("#searchfield").typeahead({
        minLength: 1,
        source: function(query, process) {
            $.post('getaslist.php', { q: query, limit: 8 }, function(data) {
                process(JSON.parse(data));
            });

        },
        updater: function (item) {
            $('#waitpopup').modal('show');
            document.location = "peras.php?asn=" + encodeURIComponent(item);
            return item;
        }
    });
    $('.btn').each(function() {
      //console.log(this.id);
      $(this).click(function(){
        var el = $(this);
        // $.post('getroalist.php', {p: this.id}, function(data) {
        //   el.attr('data-content', data);          
        // });
        //el.popover({
        //     placement: 'top',
        //     html: true
        //});
      var isVisible = $(this).next('div.popover:visible').length;
      if (!isVisible){
      $.ajax({
        type: "POST",
        url: 'getroalist2.php',
        data: {p: this.id},
        success: function(data) {
          el.attr('data-content', data);
          el.popover({
            placement: 'top',
            html: true,
            trigger: 'manual'
          });
          el.popover('toggle');
        }
      });}
      else{
        el.popover('toggle');
      }
      });
      
    });
    $('#pagetable').dataTable( {

      "sPaginationType": "bootstrap"
    } );
});
</script>
<script>
function Refresh(id){
location.href="peras.php?asn=" + id
}
</script>
</body>
</html>
