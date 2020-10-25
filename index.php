<?php

/**
 * @author	    Dirk Louwers	<dirk_louwers@hotmail.com>
 * @copyright	copyright (c) 	2005 Dirk Louwers
 */
define('PROJ_MAX_RESULTS_PER_PAGE', 5);
require dirname(__DIR__, 2) . '/mainfile.php';

/** Make an instance of the class to manage projects **/
require_once XOOPS_ROOT_PATH . '/modules/projects/class/project.php';
$projHandler = &ProjectHandler::getInstance($xoopsDB);
$imgHandler = &ProjectImageHandler::getInstance($xoopsDB);

/** Inialise and read POST vars **/
$myts = MyTextSanitizer::getInstance();
$project_name = '';
if (isset($_GET['project_name'])) {
    $project_name = trim($_GET['project_name']);
}
$project_type = '';
if (isset($_GET['project_type'])) {
    $project_type = (int)$_GET['project_type'];
}
$page = 0;
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}

/** Include the XOOPS header **/
$GLOBALS['xoopsOption']['template_main'] = 'list.html';
require XOOPS_ROOT_PATH . '/header.php';

/** Assign previous form data **/
$xoopsTpl->assign('project_name', $project_name);
$xoopsTpl->assign('project_type', $project_type);

/** Assign options for the project type **/
$project_types = ['0' => _PR_ALL, '1' => _PR_RESIDENTIAL, '2' => _PR_COMMERCIAL, '3' => _PR_HORECA];
$xoopsTpl->assign('project_types', $project_types);

/** Prepare the criteria to search the database with **/
$criteria_compo = new CriteriaCompo();
$criteria = new Criteria('project_name', '%' . $myts->addSlashes($project_name) . '%', 'LIKE');
$criteria_compo->add($criteria);
if ('' != $project_type) {
    $criteria = new Criteria('project_type', (int)$project_type);

    $criteria_compo->add($criteria);
}

/** Count the number of results **/
$project_count = $projHandler->getCountWithImages($criteria_compo);

/** Include and use the XOOPS pagenav **/
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
$extra_args = 'project_name=' . $project_name . '&amp;project_type=' . $project_type;
$pagenav = new XoopsPageNav($project_count, PROJ_MAX_RESULTS_PER_PAGE, $page, 'page', $extra_args);
$xoopsTpl->assign('pagenav', $pagenav->renderImageNav());

/** Fetch the appropriate project list **/
$criteria_compo->setSort('ORDER BY project_name');
$criteria_compo->setStart($page * PROJ_MAX_RESULTS_PER_PAGE);
$criteria_compo->setLimit(PROJ_MAX_RESULTS_PER_PAGE);

/** Fetch the projects from the database **/
$projects = &$projHandler->getObjectsWithImages($criteria_compo);

/** Work all projects into arrays to be used by the template **/
foreach (array_keys($projects) as $j) {
    $projectdata['project_name'] = $projects[$j]->getVar('project_name', 'p');

    switch ($projects[$j]->getVar('project_type')) {
        case PROJ_TYPE_RESIDENTIAL:
            $projectdata['project_type'] = _PR_RESIDENTIAL;
            break;
        case PROJ_TYPE_COMMERCIAL:
            $projectdata['project_type'] = _PR_COMMERCIAL;
            break;
        case PROJ_TYPE_HORECA:
            $projectdata['project_type'] = _PR_HORECA;
            break;
        default:
            $projectdata['project_type'] = _PR_ALL;
            break;
    }

    $projectdata['project_description'] = $projects[$j]->getVar('project_description', 'p');

    /** Fetch the first thumbnail of the project **/

    $criteria_compo = new CriteriaCompo();

    $criteria = new Criteria('project_id', (int)$projects[$j]->getVar('project_id'), '=');

    $criteria_compo->add($criteria);

    $criteria_compo->setLimit(1);

    $project_image = &$imgHandler->getObjects($criteria_compo);

    $projectdata['project_thumb'] = "<img src='" . XOOPS_URL . '/modules/projects/images/projects/' . $project_image[0]->getVar('image_id') . "_thumb.jpg'>";

    $projectdata['project_link'] = "<a href='view.php?project_id=" . $projects[$j]->getVar('project_id') . "'>";

    $xoopsTpl->append('projects', $projectdata);
}

/** Build the previous and next URL items and assign **/
$base_url = 'index.php?project_name=' . $project_name . '&amp;project_type=' . $project_type;
$prev_url = $base_url . '&amp;page=' . ($page - 1);
$next_url = $base_url . '&amp;page=' . ($page + 1);
$xoopsTpl->assign('prev_url', $prev_url);
$xoopsTpl->assign('next_url', $next_url);

/** Assign a form security token **/
$xoopsTpl->assign('security_token', $GLOBALS['xoopsSecurity']->getTokenHTML());

/** Assign all necessary language vars to the template **/
$xoopsTpl->assign('lang_search', _SEARCH);
$xoopsTpl->assign('lang_projectlist', _PR_PROJECTLIST);
$xoopsTpl->assign('lang_name', _PR_NAME);
$xoopsTpl->assign('lang_type', _PR_TYPE);
$xoopsTpl->assign('lang_description', _PR_DESCRIPTION);
$xoopsTpl->assign('lang_previous', _PR_PREV);
$xoopsTpl->assign('lang_next', _PR_NEXT);

require XOOPS_ROOT_PATH . '/footer.php';
