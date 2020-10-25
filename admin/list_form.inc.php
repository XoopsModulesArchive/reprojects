<?php

/**
 * @author	    Dirk Louwers	<dirk_louwers@hotmail.com>
 * @copyright	copyright (c) 	2005 Dirk Louwers
 */
if (!preg_match('/index.php/', $HTTP_SERVER_VARS['PHP_SELF'])) {
    exit('Access Denied');
}

xoops_cp_header();
echo '<h4>' . _AM_PROJECTLIST . "</h4>\n";
echo "<form name='list_form' method='get' action='index.php'>\n";
$GLOBALS['xoopsSecurity']->getTokenHTML();
echo "<table><tr>\n";
echo "<input type='hidden' value='list' name='op'>";
echo "<td>&nbsp;</td>\n";
echo "<td><input type='text' name='project_name' value='" . $project_name . "'></td>\n";
echo "<td><select name='project_type'>\n";
echo "<option value=''";
if ('' == $project_type) {
    echo ' selected';
}
echo '>' . _AM_ALL . "</option>\n";
echo "<option value='1'";
if ('1' == $project_type) {
    echo ' selected';
}
echo '>' . _AM_RESIDENTIAL . "</option>\n";
echo "<option value='2'";
if ('2' == $project_type) {
    echo ' selected';
}
echo '>' . _AM_COMMERCIAL . "</option>\n";
echo "<option value='3'";
if ('3' == $project_type) {
    echo ' selected';
}
echo '>' . _AM_HORECA . "</option>\n";
echo "</select></td>\n";
echo "<td><input type='submit' value='" . _AM_SEARCH . "'></td>\n";
echo "<td>&nbsp;</td></tr>\n";

echo '<tr><th>' . _AM_ID . '</th><th>' . _AM_NAME . '</th><th>' . _AM_TYPE . '</th><th>' . _AM_DESCRIPTION . '</th><th>&nbsp;</th></tr>';
$counter = 0;
foreach ($projects as $p) {
    $class = 0 == ($counter % 2) ? 'even' : 'odd';

    $counter++;

    echo "<tr class='" . $class . "'>\n";

    echo '<td>' . $p->getVar('project_id', 'p') . "</td>\n";

    echo '<td>' . $p->getVar('project_name', 'p') . "</td>\n";

    switch ($p->getVar('project_type')) {
        case 1:
        $project_type = _AM_RESIDENTIAL;
        break;
        case 2:
        $project_type = _AM_COMMERCIAL;
        break;
        case 3:
        $project_type = _AM_HORECA;
        break;
        default:
        break;
    }

    echo '<td>' . $project_type . "</td>\n";

    echo '<td>' . $p->getVar('project_description') . "</td>\n";

    echo "<td>[<a href='index.php?op=edit&amp;project_id=" . $p->getVar('project_id') . "'>" . _AM_EDIT . "</a>]<br>[<a href='index.php?op=delete_confirm&amp;project_id=" . $p->getVar('project_id') . "'>" . _AM_DELETE . '</a>]</td></tr>';
}

$colspan = 5;
echo "<tr><td colspan='" . $colspan . "' align='right'><b>";
if ($counter > 0) {
    if ($page > 0) {
        echo "< <a href='" . $prev_url . "'>" . _AM_PREV . '</a> ';
    }

    echo($page + 1) . '/' . $num_pages;

    if ($page < $num_pages - 1) {
        echo " <a href='" . $next_url . "'>" . _AM_NEXT . '</a> >';
    }
} else {
    echo _AM_NOPROJECTS;
}

echo "\n</b></td></tr>";
echo "</table></form>\n";

xoops_cp_footer();
