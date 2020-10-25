<?php

/**
 * @author	    Dirk Louwers	<dirk_louwers@hotmail.com>
 * @copyright	copyright (c) 	2005 Dirk Louwers
 */
if (!preg_match('/index.php/', $HTTP_SERVER_VARS['PHP_SELF'])) {
    exit('Access Denied');
}

global $xoopsModuleConfig;

echo "<table class='outer' cellspacing='1'>\n";
echo '<tr><th>' . $project->getVar('project_name', 'p') . "</th>\n";
echo "<tr><td>\n";
echo "<table class='outer' cellspacing='1'>\n";
$project_type_text = _AM_RESIDENTIAL;
switch ($project->getVar('project_type')) {
    case 1:
    $project_type_text = _AM_RESIDENTIAL;
    break;
    case 2:
    $project_type_text = _AM_COMMERCIAL;
    break;
    case 3:
    $project_type_text = _AM_HORECA;
    break;
}
$counter = 2;
echo "<tr><td class='head'>" . _AM_TYPE . "</td><td class='odd'>{$project_type_text}</td>\n";
$lid = $xoopsModuleConfig['lid'];
$maptype = $xoopsModuleConfig['maptype'];
$country = $xoopsModuleConfig['country'];
$map_link = 'http://link2.nl.map24.com/?lid=' . $lid . '&amp;maptype=' . $maptype . '&amp;&amp;country=' . $country . '&amp;zip=' . (preg_replace('/\s\s+/', '', $project->getVar('project_areacode')));
$map_script = 'javascript:window.open("","mapWindow");';
echo "<td class='head'>" . _AM_LOCATION . "</td><td class='odd'><a onClick='{$map_script}' href='{$map_link}' target='mapWindow'>" . $project->getVar('project_location', 'p') . "</a></td></tr>\n";
$project_status_text = _AM_INPROGRESS;
switch ($project->getVar('project_status')) {
    case 1:
    $project_status_text = _AM_INPROGRESS;
    break;
    case 2:
    $project_status_text = _AM_DONE;
    break;
}
echo "<tr><td class='head'>" . _AM_STATUS . "</td><td class='even' colspan='3'>{$project_status_text}</td></tr>\n";
if ('' != $project->getVar('project_description')) {
    $rowtype = (0 == $counter % 2) ? 'odd' : 'even';

    echo "<tr><td  class='head'>" . _AM_DESCRIPTION . "</td><td class='{$rowtype}' colspan='3'>" . $project->getVar('project_description', 'p') . "</td><tr>\n";

    $counter++;
}
if ('' != $project->getVar('project_details')) {
    $rowtype = (0 == $counter % 2) ? 'odd' : 'even';

    echo "<tr><td class='head'>" . _AM_DETAILS . "</td><td class='{$rowtype}' colspan='3'>" . $project->getVar('project_details', 'p') . "</td></tr>\n";

    $counter++;
}
if ('' != $project->getVar('project_enrollment')) {
    $rowtype = (0 == $counter % 2) ? 'odd' : 'even';

    echo "<tr><td class='head'>" . _AM_ENROLLMENT . "</td><td class='{$rowtype}' colspan='3'>" . $project->getVar('project_enrollment', 'p') . "</td></tr>\n";

    $counter++;
}
if ('' != $project->getVar('project_salesinfo')) {
    $rowtype = (0 == $counter % 2) ? 'odd' : 'even';

    echo "<tr><td class='head'>" . _AM_SALESINFO . "</td><td class='{$rowtype}' colspan='3'>" . $project->getVar('project_salesinfo', 'p') . "</td></tr>\n";

    $counter++;
}
if ('' != $project->getVar('project_rentinfo')) {
    $rowtype = (0 == $counter % 2) ? 'odd' : 'even';

    echo "<tr><td class='head'>" . _AM_RENTINFO . "</td><td class='{$rowtype}' colspan='3'>" . $project->getVar('project_rentinfo', 'p') . "</td></tr>\n";

    $counter++;
}
echo "</table></td></tr></table>\n";
echo "<br>\n";
