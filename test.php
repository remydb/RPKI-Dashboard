<?php
require ('include/functions.php');
require ('include/messages.php');
print "<table class=\"table table-striped\"><tr>
        <td>RIPE</td>
        <td><span class=\"label label-info\">".query_totals_per_rir('%','RIPE')."</span></td>
        <td><span class=\"label label-info\">".query_totals_per_rir('V','RIPE')."</span></td>
        <td><span class=\"label label-info\">".query_totals_per_rir('I%','RIPE')."</span></td>
        <td><span class=\"label label-info\">".query_totals_per_rir('U','RIPE')."</span></td>
</tr></table>";
?>
