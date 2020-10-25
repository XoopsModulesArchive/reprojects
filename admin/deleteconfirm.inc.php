<?php

/**
 * @author	    Dirk Louwers	<dirk_louwers@hotmail.com>
 * @copyright	copyright (c) 	2005 Dirk Louwers
 */
if (!preg_match('/index.php/', $HTTP_SERVER_VARS['PHP_SELF'])) {
    exit('Access Denied');
}

echo "<table class='outer' cellspacing='1'>\n";
echo sprintf("<tr><th colspan='2'>" . _AM_DELETECONFIRMQUESTION . "</th></tr>\n", $item_to_delete);
echo "<tr><td align='center'><a href='" . $confirm_link . "'>" . _AM_YES . "</a></td><td align='center'><a href='javascript:history.go(-1)'>" . _AM_NO . "</a></td></tr>\n";
echo "</table>\n";
