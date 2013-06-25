<!DOCTYPE html>
<?php
require ('include/functions.php');
if (isset($_GET['v'])){
  $validity = $_GET['v'];
}
?>
<html lang="en">
<head>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache"> 
<title>RPKI Dashboard</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="bootstrap/css/custom.css" rel="stylesheet">
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
      <div class='span3'>
        <!-- AS list
        ============================================= -->
        <section id="total">
          <div class="well">Please <strong>select a validity state</strong> from the dropdown list below.</div>
          <div>
            <form class="navbar-search pull-left">
              <select name="rir" [B] onChange="Refresh(this.value)"[/B]>
                <option> </option>
                <option value='V'>Valid</option>
                <option value='I'>Invalid (all)</option>
                <option value='IA'>Invalid - AS mismatch</option>
                <option value='IQ'>Invalid - Prefix range length exceeded</option>
                <option value='IP'>Invalid - Prefix fixed length mismatch</option>
                <option value='IB'>Invalid - Prefix & AS mismatch</option>
              </select>
            </form>
          </div>
        </section>
      </div>
      <div class="span9 main">
        <?php
          if (isset($validity)){
            //echo "<div id='dat_table'>";
            validitytable($validity);  
            //echo "</div>";
          }
        ?>
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
<script class="jsbin" src="bootstrap/js/jquery.dataTables.nightly.js"></script>
<script type="text/javascript" src="bootstrap/js/DT_bootstrap.js"></script>
<script type="text/javascript">
var buttonid;
$(document).ready(function() {
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
location.href="validitytables_" + id + ".html"
}
</script>
</body>
</html>
