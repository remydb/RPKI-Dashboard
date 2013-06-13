<?php
$colours = array("3366cc","dc3912","ff9900","109618","990099","0099c6","dd4477","66aa00","b82e2e","6e97e9");
$day = 86400;
$date = "today_routes"; //date("d-m-y", time()-$day)."_routes";

function mySQLi()
{
	$mysqli = new mysqli("localhost", "root", "noedelsoep", "bgp");
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	return $mysqli;	
}

function getASlist()
{
	$mysqli = mySQLi();

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

function getASlistjson($part, $limit)
{
    $mysqli = mySQLi();

    $query = "SELECT DISTINCT(ASN) as ASN FROM `".$GLOBALS['date']."` WHERE `ASN` LIKE '$part%' ORDER BY ASN LIMIT $limit";
    if (!$result = $mysqli->query($query))
    {
        echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    $a = "";
    while ($row = $result->fetch_row())
    {
        //echo "<a href='anpie.php?asn=" . $row[0] . "'>AS" . $row[0] . "</a><br>";
        $a .= "\"".$row[0]."\", ";
        //$a .= $row[0].",";
    }
    $mysqli->close();
    return rtrim($a, ", ");
}

function query_totals($status)
{
	$mysqli = mySQLi();
	
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
        $mysqli = mySQLi();

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

function query_totals_per_rir($status, $rir)
{
        $mysqli = mySQLi();

        if (!($stmt = $mysqli->prepare("SELECT count(*) FROM `".$GLOBALS['date']."` where Validity like ? AND RIR like ?"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $stmt->bind_param("ss", $status, $rir);
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

function query_total_prefixes($status)
{
        $mysqli = mySQLi();

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
        	['Topping', 'Slices'],
		['$lega - $pera%', $vara],
		['$legb - $perb%', $varb],
		['$legc - $perc%', $varc]
        ]);

        // Create and draw the visualization.
        var options = {'title':'$title','width':800,'height':600,'is3D':1};
        new google.visualization.PieChart(document.getElementById('$chart')).
            draw(data, options);
      }";
}

function newpichart2($a, $b, $c, $d, $lega, $legb, $legc, $legd, $title, $chart){
        $vara = query_totals($a);
        $varb = query_totals($b);
        $varc = query_totals($c);
       	$vard = query_totals($d); 
	$total = $vara + $varb + $varc + $vard;
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        $perc = round($varc / $total * 100, 2);
	$perd = round($vard / $total * 100, 2);
        print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
                ['Topping', 'Slices'],
                ['$lega - $vara ($pera%)', $vara],
                ['$legb - $varb ($perb%)', $varb],
                ['$legc - $varc ($perc%)', $varc],
		['$legd - $vard ($perd%)', $vard]
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

function newbarchart($type, $validity, $title, $group, $chart, $amount){
	$mysqli = mySQLi();

        $query = "SELECT `$type`, 
            COUNT(`$type`) AS `occurrence` 
            FROM `".$GLOBALS['date']."` 
            WHERE Validity LIKE '$validity'
            AND $type != ''
            GROUP BY `$type` ORDER BY `occurrence` 
            DESC LIMIT $amount;";

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
        
        //foreach ($stack as $v){
	//print "'$v', ";
	//} 
	print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
        ['Year',";
	foreach ($stack as $v){
		print "'$v',";
	}  
	print "],";
	
	print "	
	['', ";
	foreach ($stack2 as $v){
                print "$v,";
        }
	print "]
        ]);

        // Create and draw the visualization.
        var options = {
        hAxis: {title: \"$title\"},
        //width: 800,
        height: 400,
        chartArea:{left:0,top:0,width:\"80%\",height:\"80%\"} 
	};
        
        var chart = new google.visualization.BarChart(document.getElementById('$chart'));
        chart.draw(data, options);
      }";
}

function newgeochart($validity, $title, $chart){
	$mysqli = mySQLi();
	
	if ($validity=='V')
  	{
  	$colors = "['#fd2323', '#ed9200', '#e4d417', '#95c12d']";
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

function newlinechart($validity, $validity2, $title, $chart, $as){
        
        //Dirty way to create an array with dates of last 7 days
		//Should consider using dateperiod/dateinterval instead
		$daterange = array();
	 	$daterange[0] = date("d-m-y", time()-604800); //7 days ago	
		$daterange[1] = date("d-m-y", time()-518400); //6 days ago
		$daterange[2] = date("d-m-y", time()-432000); //5 days ago
		$daterange[3] = date("d-m-y", time()-345600); //4 days ago
		$daterange[4] = date("d-m-y", time()-259200); //3 days ago
		$daterange[5] = date("d-m-y", time()-172800); //2 days ago		
		$daterange[6] = date("d-m-y", time()-86400);  //yesterday

        $mysqli = mySQLi();

        $stack = array();
        foreach ($daterange as $xdate){
	        $query = "SELECT COUNT(*) 
	            FROM `$xdate` 
	            WHERE Validity LIKE '$validity' AND ASN LIKE '$as'";
	        if ($result = $mysqli->query($query))
	        {
	            //echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
				$row = $result->fetch_row();
	        	$stack[$xdate] = $row[0];
	        }
	        
        }
        
	$stack2 = array();
        foreach ($daterange as $xdate){
                $query = "SELECT COUNT(*)
                    FROM `$xdate`
                    WHERE Validity LIKE '$validity2'";
                if ($result = $mysqli->query($query))
                {
                    //echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
                                $row = $result->fetch_row();
                        $stack2[$xdate] = $row[0];
                }

        }

	$mysqli->close();
        

	print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
          ['Date','Valid', 'Invalid'],";

         $keys = array_keys($stack);
         foreach ($keys as $key){
         	echo "['$key',".$stack[$key].", ".$stack2[$key]."],";
         }

        echo "]);

        // Create and draw the visualization.
        var options = {
        title: '$title',
        width: 800,
        height: 500,
        };
        
        var chart = new google.visualization.LineChart(document.getElementById('$chart'));
        chart.draw(data, options);
      }";
}

function newtable($as){
        $mysqli = mySQLi();

        $query = "SELECT *
            FROM `".$GLOBALS['date']."`
            WHERE ASN LIKE '$as'
            GROUP BY `IP_Prefix`";

	//if(!$result = $mysqli->query($query)){
        //            echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;}
	//$stuff = $result->fetch_row(); 
	//echo $stuff[0], $stuff[1], $stuff[2], $stuff[3];
	if ($result = $mysqli->query($query)) {

    	/* fetch associative array */
    	print "<table class=\"table table-striped\" >";
	print "<tr><td>Prefix</td><td>Validity</td></tr>";
	while ($row = $result->fetch_assoc()) {
        //if (substr('_abcdef', 0, 1) === '_')
	if (strpos($row["Validity"],"I") !== false){
		$class = "error";}
	elseif ($row["Validity"] == "V"){
		$class = "success";}
	else {$class = "warning";}
	print "<tr class=\"$class\"><td>".$row["IP_Prefix"]."</td><td>".$row["Validity"]."</td></tr>";
    	}
	print "</table>";
    	/* free result set */
    	$result->free();
	}
}

function rirtable(){
$rirs = array('RIPE', 'LACNIC', 'ARIN', 'APNIC', 'AFRINIC');

print "<table class=\"table table-striped table-bordered table-hover\">
<tr>
<td width=\"200px\"><b>RIR</b></td>
<td><b>Total</span></b></td>
<td><b>Valid</span></b></td>
<td><b>Invalid</span></b></td>
<td><b>Unknown</span></b></td>
<td><b>RPKI Adoption Rate</span></b></td>
</tr>";

foreach ($rirs as &$value) {
        $total = query_totals_per_rir('%', $value);
        $valid = query_totals_per_rir('V', $value);
        $validper = round($valid/$total*100,2);

        $invalid = query_totals_per_rir('I%', $value);
        $invalidper = round($invalid/$total*100,2);

        $unknown = query_totals_per_rir('U', $value);
        $unknownper = round($unknown/$total*100,2);

        $adop = round(($total-$unknown)/$total*100,2);

        print "<td>$value</td>
        <td><span class=\"label label-info\">$total (100%)</span></td>
        <td><span class=\"label label-success\">$valid ($validper%)</span></td>
        <td><span class=\"label label-important\">$invalid ($invalidper%)</span></td>
        <td><span class=\"label label-warning\">$unknown ($unknownper%)</span></td>
        <td><span class=\"label label-inverse\">$adop%</span></td>
        </tr>";
        };

print "</table>";
}
?>
