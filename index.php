<!DOCTYPE html>
<head>
<title>RPKI Dashboard</title>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href="bootstrap/css/custom.css" rel="stylesheet">
</head>
<body>

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
    <div id='content' class='row-fluid'>
      <div class='span12 main'>
        <h2>RPKI Monitor</h2>
          <p>This web page has been made to monitor the adoption rate of Route Origin Validation for BGP.
            All BGP messages received through BGPmon during each day are stored in a database, this database can then be used to create graphs.<p> 
          <p>What ever.. I'll put other stuff here some time. I can't be bothered to do it now..</p>
      </div>
    </div>
  </div>
<script src="bootstrap/js/bootstrap.js"></script>
</body>
</html>
