<?php
$colours = array("3366cc","dc3912","ff9900","109618","990099","0099c6","dd4477","66aa00","b82e2e","6e97e9");
$date = date("d-m-y", time()-86400);

function getASlist()
{
	$mysqli = new mysqli("localhost", "root", "noedelsoep", "bgp");
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$query = "SELECT DISTINCT(ASN) as ASN FROM `".$GLOBALS['date']."` ORDER BY ASN";
	if (!$result = $mysqli->query($query))
	{
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	while ($row = $result->fetch_row())
	{
		//echo "<a href='anpie.php?asn=" . $row[0] . "'>AS" . $row[0] . "</a><br>";
		echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
	}
	$mysqli->close();
}

function query_totals($status)
{
	$mysqli = new mysqli("localhost", "root", "noedelsoep", "bgp");
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	if (!($stmt = $mysqli->prepare("SELECT count(*) FROM `".$GLOBALS['date']."` where Validity like ?"))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stmt->bind_param("s", $status);
	if (!$stmt->execute()) {
	     echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->bind_result($total);
	if (!($stmt->fetch())) {
	    echo "Getting result failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$mysqli->close();
	return $total;
}

function query_totals_peras($status, $asn)
{
	$mysqli = new mysqli("localhost", "root", "noedelsoep", "bgp");
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	if (!($stmt = $mysqli->prepare("SELECT count(*) FROM `".$GLOBALS['date']."` where Validity like ? and ASN = ?"))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stmt->bind_param("ss", $status, $asn);
	if (!$stmt->execute()) {
	     echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->bind_result($total);
	if (!($stmt->fetch())) {
	    echo "Getting result failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$mysqli->close();
	return $total;
}

function top10_graph($type, $validity, $title)
{
	
	$mysqli = new mysqli("localhost", "root", "noedelsoep", "bgp");
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$query = "SELECT `$type`, 
			COUNT(`$type`) AS `occurrence` 
			FROM `".$GLOBALS['date']."` 
			WHERE Validity LIKE '$validity' 
			GROUP BY `$type` ORDER BY `occurrence` 
			DESC LIMIT 10;";
	if (!$result = $mysqli->query($query))
	{
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stack = array();
	$stack2 = array();
	while ($row = $result->fetch_row())
	{
		if ($type == 'ASN'){
			array_push($stack, "AS" . $row[0] . " (" . $row[1] . ")");// = $row[1];
		}
		else{
			array_push($stack, strtoupper($row[0]) . " (" . $row[1] . ")");// = $row[1];
		}
		array_push($stack2, $row[1]);
	}
	$mysqli->close();
	$barChart = new gBarChart(400,330,'g','h');
	$barChart->addDataSet(array($stack2[0]));
	$barChart->addDataSet(array($stack2[1]));
	$barChart->addDataSet(array($stack2[2]));
	$barChart->addDataSet(array($stack2[3]));
	$barChart->addDataSet(array($stack2[4]));
	$barChart->addDataSet(array($stack2[5]));
	$barChart->addDataSet(array($stack2[6]));
	$barChart->addDataSet(array($stack2[7]));
	$barChart->addDataSet(array($stack2[8]));
	$barChart->addDataSet(array($stack2[9]));
	$barChart->setTitle($title);
	$barChart->setVisibleAxes(array('x'));
	$barChart->addAxisRange(0, 0, $stack2[0]);
	$barChart->addAxisRange(1, 0, $stack2[0]);
	//$barChart->setGridLines(6.25,6.25);
	$barChart->setColors($GLOBALS['colours']);
	//$barChart->setLabels($stack);
	//$barChart->setGradientFill('c',0,array('FFE7C6',0,'76A4FB',1));
	$barChart->setLegend($stack);
	return $barChart;
}

function pichart($a, $b, $c, $lega, $legb, $legc, $title){
	$vara = query_totals($a);
	$varb = query_totals($b);
	$varc = query_totals($c);
	$total = ($vara + $varb + $varc)/100;
	$piChart = new gPie3DChart();
	$piChart->addDataSet(array($vara,$varb,$varc));
	$piChart->setTitle($title);
	$piChart->setLegend(array($lega . " (" . $vara . ")", $legb . " (" . $varb . ")", $legc . " (" . $varc . ")"));
	$piChart->setLabels(array(round($vara/$total, 2) . '%', round($varb/$total,2) . '%', round($varc/$total,2) . '%'));
	$piChart->setColors($GLOBALS['colours']);
	return $piChart;
}

function pichartperas($asn, $a, $b, $c, $lega, $legb, $legc, $title){
	$vara = query_totals_peras($a, $asn);
	$varb = query_totals_peras($b, $asn);
	$varc = query_totals_peras($c, $asn);
	$total = ($vara + $varb + $varc)/100;
	$piChart = new gPie3DChart();
	$piChart->setTitle($title);
	$piChart->addDataSet(array($vara,$varb,$varc));
	$piChart->setLegend(array($lega . " (" . $vara . ")", $legb . " (" . $varb . ")", $legc . " (" . $varc . ")"));
	$piChart->setLabels(array(round($vara/$total, 2) . '%', round($varb/$total,2) . '%', round($varc/$total,2) . '%'));
	$piChart->setColors($GLOBALS['colours']);
	return $piChart;
}

function newpichart($a, $b, $c, $lega, $legb, $legc, $title, $chart){
        $vara = query_totals($a);
        $varb = query_totals($b);
        $varc = query_totals($c);
        $total = $vara + $varb + $varc;
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        $perc = round($varc / $total * 100, 2);
        
	print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
        	['Topping', 'Slices'],\n['$lega - $pera%', $vara],\n['$legb - $perb%', $varb],\n['$legc - $perc%', $varc]
        ]);

        // Create and draw the visualization.
        var options = {'title':'$title','width':800,'height':600,'is3D':1};
        new google.visualization.PieChart(document.getElementById('$chart')).
            draw(data, options);
      }";
}

function newpichartperas($asn, $a, $b, $c, $lega, $legb, $legc, $title, $chart){
        $vara = query_totals_peras($a, $asn);
        $varb = query_totals_peras($b, $asn);
        $varc = query_totals_peras($c, $asn);
        $total = $vara + $varb + $varc;
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        $perc = round($varc / $total * 100, 2);
        
	print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
        	['Topping', 'Slices'],\n['$lega - $pera%', $vara],\n['$legb - $perb%', $varb],\n['$legc - $perc%', $varc]
        ]);

        // Create and draw the visualization.
        var options = {'title':'$title','width':800,'height':600,'is3D':1};
        new google.visualization.PieChart(document.getElementById('$chart')).
            draw(data, options);
      }";
}

function newbarchart($type, $validity, $title, $group, $chart){
        $mysqli = new mysqli("localhost", "root", "noedelsoep", "bgp");
        if ($mysqli->connect_errno) {
          echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }

        $query = "SELECT `$type`, 
            COUNT(`$type`) AS `occurrence` 
            FROM `".$GLOBALS['date']."` 
            WHERE Validity LIKE '$validity' 
            GROUP BY `$type` ORDER BY `occurrence` 
            DESC LIMIT 10;";
        if (!$result = $mysqli->query($query))
        {
            echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $stack = array();
        $stack2 = array();
        while ($row = $result->fetch_row())
        {
          if ($type == 'ASN'){
            array_push($stack, "AS" . $row[0] . " (" . $row[1] . ")");// = $row[1];
          }
          else{
            array_push($stack, strtoupper($row[0]) . " (" . $row[1] . ")");// = $row[1];
          }
          array_push($stack2, $row[1]);
        }
        $mysqli->close();
        

	print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', '$stack[0]', '$stack[1]', '$stack[2]', '$stack[3]', '$stack[4]', '$stack[5]', '$stack[6]', '$stack[7]', '$stack[8]', '$stack[9]'],
          ['', $stack2[0], $stack2[1], $stack2[2], $stack2[3], $stack2[4], $stack2[5], $stack2[6], $stack2[7], $stack2[8], $stack2[9]]
        ]);

        // Create and draw the visualization.
        var options = {
        title: '$title',
        width: 800,
        height: 500,
        };
        
        var chart = new google.visualization.BarChart(document.getElementById('$chart'));
        chart.draw(data, options);
      }";
}

function newgeochart($validity, $title, $chart){
        $mysqli = new mysqli("localhost", "root", "noedelsoep", "bgp");
        if ($mysqli->connect_errno) {
          echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }

	if ($validity=='V')
  	{
  	$colors = "['#fd2323', '#ed9200', '#e4d417', '#95c12d']";
  	}
       	else if ($validity=='U')
  	{
  	$colors = "['#95c12d', '#e4d417', '#ed9200', '#fd2323']";
 	}
	else
  	{
  	$colors = "['#95c12d', '#e4d417', '#ed9200', '#fd2323']";
 	}
 
	$query = "SELECT `Country`, 
            COUNT(`Country`) AS `occurrence` 
            FROM `".$GLOBALS['date']."` 
            WHERE Validity LIKE '$validity' 
            GROUP BY `Country`";
        $query2 = "SELECT `Country`,
        			COUNT(`Country`) AS `occurence`
        			FROM `".$GLOBALS['date']."`
        			GROUP BY `Country`";

        if (!$result = $mysqli->query($query))
        {
            echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }

        if ($result2 = $mysqli->query($query2))
        {
        	$data_array = array();
			while ($row = $result2->fetch_row()) {
			    $data_array[$row[0]] = $row[1];
			}
        }

	print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
          ['Country', 'Amount (%)'],";
        while ($row = $result->fetch_row())
        {
        	if ($row[0] == 'uk'){
        		echo "['gb', ".round(($row[1]/$data_array['uk']*100), 1)."],";
        	}
        	else{
        	echo "['".$row[0]."', ".round(($row[1]/$data_array[$row[0]]*100), 1)."],";
        	}
        }
        $mysqli->close();

    echo "]);

        // Create and draw the visualization.
        var options = {
        title: '$title',
	colorAxis: {minValue: 0, maxValue: 100, colors:$colors}
        };
        
        var chart = new google.visualization.GeoChart(document.getElementById('$chart'));
        chart.draw(data, options);
      }";
}
?>

