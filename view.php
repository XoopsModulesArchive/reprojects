<?php

/**
 * @author	    Dirk Louwers	<dirk_louwers@hotmail.com>
 * @copyright	copyright (c) 	2005 Dirk Louwers
 */
require dirname(__DIR__, 2) . '/mainfile.php';

/** Make an instance of the class to manage projects **/
require_once XOOPS_ROOT_PATH . '/modules/projects/class/project.php';
$projHandler = &ProjectHandler::getInstance($xoopsDB);
$imgHandler = &ProjectImageHandler::getInstance($xoopsDB);

/** Initialise and read GET vars **/
if (isset($_GET['project_id'])) {
    $id = $_GET['project_id'];
}

/** Grab the requested project from the database **/
$project = $projHandler->get($id);

/** Build criteria to fetch the images **/
$criteria_compo = new CriteriaCompo();
$criteria = new Criteria('project_id', $id);
$criteria_compo->add($criteria);

/** Grab the images belonging to the project **/
$images = &$imgHandler->getObjects($criteria_compo);

/** Include the XOOPS header **/
$GLOBALS['xoopsOption']['template_main'] = 'view.html';
require XOOPS_ROOT_PATH . '/header.php';

/** Assign the language definitions **/
$xoopsTpl->assign('lang_project_type', _PR_TYPE);
$xoopsTpl->assign('lang_project_location', _PR_LOCATION);
$xoopsTpl->assign('lang_project_status', _PR_STATUS);
$xoopsTpl->assign('lang_project_description', _PR_DESCRIPTION);
$xoopsTpl->assign('lang_map', _PR_MAP);
$xoopsTpl->assign('lang_project_details', _PR_DETAILS);
$xoopsTpl->assign('lang_project_enrollment', _PR_ENROLLMENT);
$xoopsTpl->assign('lang_project_salesinfo', _PR_SALESINFO);
$xoopsTpl->assign('lang_project_rentinfo', _PR_RENTINFO);

/** Build an array of url's to the main and thumbnail images and assign**/
foreach (array_keys($images) as $j) {
    $imagedata['main'] = XOOPS_URL . '/modules/projects/images/projects/'
        . $images[$j]->getVar('image_id') . '_main.jpg';

    $imagedata['thumb'] = XOOPS_URL . '/modules/projects/images/projects/'
        . $images[$j]->getVar('image_id') . '_thumb.jpg';

    $xoopsTpl->append('images', $imagedata);
}

/** Assign all other variables involved **/
$xoopsTpl->assign('project_name', $project->getVar('project_name', 'p'));
$xoopsTpl->assign('project_description', $project->getVar('project_description', 'p'));
$project_type = _PR_RESIDENTIAL;
switch ($project->getVar('project_type')) {
    case 1:
        $project_type = _PR_RESIDENTIAL;
        break;
    case 2:
        $project_type = _PR_COMMERCIAL;
        break;
    case 3:
        $project_type = _PR_HORECA;
        break;
}
$xoopsTpl->assign('project_type', $project_type);
$lid = $xoopsModuleConfig['lid'];
$maptype = $xoopsModuleConfig['maptype'];
$country = $xoopsModuleConfig['country'];
$map_link = 'http://link2.nl.map24.com/?lid=' . $lid . '&amp;maptype=' . $maptype . '&amp;&amp;country=' . $country . '&amp;zip=' . (preg_replace('/\s\s+/', '', $project->getVar('project_areacode')));
$map_script = 'javascript:window.open("","mapWindow");';
$anchor = "<a onClick='{$map_script}' href='{$map_link}' target='mapWindow'>";
$xoopsTpl->assign('project_location', $project->getVar('project_location', 'p'));
$xoopsTpl->assign('anchor', $anchor);
switch ($project->getVar('project_status')) {
    case 1:
        $project_status = _PR_INPROGRESS;
        break;
    case 2:
        $project_status = _PR_DONE;
        break;
}
$xoopsTpl->assign('project_status', $project_status);
$xoopsTpl->assign('project_description', $project->getVar('project_description'));
$xoopsTpl->assign('project_details', $project->getVar('project_details'));
$xoopsTpl->assign('project_enrollment', $project->getVar('project_enrollment'));
$xoopsTpl->assign('project_salesinfo', $project->getVar('project_salesinfo'));
$xoopsTpl->assign('project_rentinfo', $project->getVar('project_rentinfo'));
require XOOPS_ROOT_PATH . '/footer.php';
