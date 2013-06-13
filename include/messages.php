<?php
$vartotal = query_total_prefixes('%');
$varunknown = query_total_prefixes('U');
$varinvalid = query_total_prefixes('I%');
$varvalid = query_total_prefixes('V');
$varvalidation = $vartotal - $varunknown;
$varpercent = round($varvalidation / $vartotal * 100, 2);

$rirmesg = "<div class=\"well\">currently RIPE ".query_totals_per_rir('%','RIP%')."</div>";

$welcome= "<h1>Welcome!</h1><div class=\"well\"><p>This web page has been made to monitor the adoption rate of Route Origin 
	Validation for BGP. All BGP messages received through BGPmon during each day are stored in a database, 
	this database can then be used to create graphs.<p>";

$message = "<div class=\"well\">This page provides an overview of the current state of <strong>RPKI adoption</strong>.
Yesterdays routing table holds <span class=\"badge badge-success\">$vartotal</span> prefixes. The
valdation state has been determined for <span class=\"badge badge-success\">$varvalidation</span>
<strong>prefixes</strong>. This means that <span class=\"badge badge-success\">$varpercent%</span>
of the prefixes in the routing table is <strong>configured for RPKI.</strong></div>";

$message2 = "<div class=\"well\">From the <span class=\"badge badge-success\">$varvalidation</span> prefixes that
are <strong>configured to use RPKI</strong>, <span class=\"badge badge-important\">$varinvalid</span>
are <strong>invalid</strong>. The <strong>reason</strong> for these <strong>invalidated prefixes</strong>
is shown in the <strong>pie chart below.</strong></div>";

$trends = "<h2>Trends</h2><div class=\"well\">From the <span class=\"badge badge-success\">$vartotal</span> prefixes that
are currently in the routing table, <span class=\"badge badge-success\">$varvalidation</span> match at least
one ROA. From these matched prefixes <span class=\"badge badge-important\">$varinvalid</span>
are <strong>invalid</strong> while <span class=\"badge badge-success\">$varvalid</span> are valid. The 
line chart below shows the valid and invalid routes over the course of time.</div>"; 

$select = "<div class=\"well\">Please <strong>select an AS number </strong>from the dropdown list below.</div>";

$location = "<div class=\"well\"> To determine the geographical location of a prefix we use
the IP to nation database. This database uses the ARIN Whois database as a source and claims to be 95%+ accurate. 
ARIN provides a mechanism for finding contact and registration information for IP resources registered with ARIN.</div>"; 

$placeholder = "<div class=\"well\">This is a <strong>placeholder message!</strong></div>";
?>
