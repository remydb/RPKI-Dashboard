<?php
require ('include/functions.php');
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
