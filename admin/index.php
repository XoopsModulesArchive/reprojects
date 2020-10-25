<?php

/**
 * @author	    Dirk Louwers	<dirk_louwers@hotmail.com>
 * @copyright	copyright (c) 	2005 Dirk Louwers
 */
require dirname(__DIR__, 3) . '/include/cp_header.php';
define('PROJ_MAX_RESULTS_PER_PAGE', 10);

/** Read all POST vars for later use
if ( isset( $_POST ) )
{
    foreach ( $_POST as $k => $v )
    {
        ${$k} = $v;
    }
} **/

/** Make sure $op is set with a valid value. Otherwise revert to default 'list' **/
$op = $_GET['op'] ?? 'list';

if ('list' == $op) {
    listProjects(false);
} elseif ('add' == $op) {
    $action = $_POST['action'] ?? '';

    if ('save' == $action) {
        saveNewProject();
    } elseif ('preview' == $action) {
        addProject(true);
    } else {
        addProject();
    }
} elseif ('delete' == $op) {
    deleteProject();
} elseif ('delete_confirm' == $op) {
    deleteProjConfirm();
} elseif ('edit' == $op) {
    $action = '';

    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    } elseif (isset($_GET['action'])) {
        $action = $_GET['action'];
    }

    if ('save' == $action) {
        saveEditedProject();
    } elseif ('preview' == $action) {
        editProject(true);
    } elseif ('upload_image' == $action) {
        uploadImage();
    } elseif ('img_delete_confirm' == $action) {
        deleteImgConfirm();
    } elseif ('img_delete' == $action) {
        deleteImage();
    } else {
        editProject();
    }
}

function listProjects($admin = false)
{
    global $xoopsDB, $_GET, $HTTP_SERVER_VARS;

    /** Make an instance of the class to manage projects **/

    require_once XOOPS_ROOT_PATH . '/modules/projects/class/project.php';

    $projHandler = &ProjectHandler::getInstance($xoopsDB);

    /** Get the variables involved in assembling the list **/

    $page = 0;

    if (isset($_GET['page'])) {
        $page = (int)$_GET['page'];
    }

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

    /** Prepare the criteria to search the database with **/

    $criteria_compo = new CriteriaCompo();

    $criteria = new Criteria('project_name', '%' . $myts->addSlashes($project_name) . '%', 'LIKE');

    $criteria_compo->add($criteria);

    if ('' != $project_type) {
        $criteria = new Criteria('project_type', $project_type);

        $criteria_compo->add($criteria);
    }

    /** Count the number of results **/

    $project_count = $projHandler->getCount($criteria_compo);

    /** Determine number of pages and wether the page param is within bounds, revert to last page if not **/

    $num_pages = ceil($project_count / PROJ_MAX_RESULTS_PER_PAGE);

    if ($page > $num_pages - 1) {
        $page = $num_pages;
    }

    if ($page < 0) {
        $page = 0;
    }

    /** Fetch the appropriate project list **/

    $criteria_compo->setSort('ORDER BY project_name');

    $criteria_compo->setStart($page * PROJ_MAX_RESULTS_PER_PAGE);

    $criteria_compo->setLimit(PROJ_MAX_RESULTS_PER_PAGE);

    /** Fetch the projects from the database **/

    $projects = &$projHandler->getObjects($criteria_compo);

    /** Build the previous and next URL items **/

    $base_url = XOOPS_URL . '/modules/projects/admin/index.php?op=list&amp;project_name=' . $project_name
        . '&amp;project_type=' . $project_type;

    $prev_url = $base_url . '&amp;page=' . ($page - 1);

    $next_url = $base_url . '&amp;page=' . ($page + 1);

    /** Include the list renderer if there are projects to display **/

    require_once __DIR__ . '/list_form.inc.php';
}

function addProject($preview = false)
{
    global $_POST, $HTTP_SERVER_VARS;

    $myts = MyTextSanitizer::getInstance();

    /** Include classes for handling projects **/

    require_once XOOPS_ROOT_PATH . '/modules/projects/class/project.php';

    /** Fetch variables **/

    extract($_POST, EXTR_SKIP);

    /** Show header **/

    xoops_cp_header();

    /** If preview fetch variables and show it **/

    $project = new Project();

    if ($preview) {
        /** Fill the project container object **/

        $project->assignVar('project_name', $project_name);

        $project->assignVar('project_description', $project_description);

        $project->assignVar('project_type', $project_type);

        $project->assignVar('project_location', $project_location);

        $project->assignVar('project_details', $project_details);

        $project->assignVar('project_status', $project_status);

        $project->assignVar('project_salesinfo', $project_salesinfo);

        $project->assignVar('project_rentinfo', $project_rentinfo);

        $project->assignVar('project_enrollment', $project_enrollment);

        $project->assignVar('project_areacode', $project_areacode);

        /** Display the preview **/

        require __DIR__ . '/project_preview.inc.php';
    }

    $action_url = 'index.php?op=add';

    require __DIR__ . '/project_form.inc.php';

    xoops_cp_footer();
}

function saveNewProject()
{
    global $_POST, $xoopsDB;

    /** Include Project Class **/

    require_once XOOPS_ROOT_PATH . '/modules/projects/class/project.php';

    /** Extract Variables **/

    extract($_POST, EXTR_SKIP);

    /** Create storage class **/

    $project = new Project();

    $project->assignVar('project_name', $project_name);

    $project->assignVar('project_type', $project_type);

    $project->assignVar('project_status', $project_status);

    $project->assignVar('project_description', $project_description);

    $project->assignVar('project_areacode', $project_areacode);

    $project->assignVar('project_location', $project_location);

    $project->assignVar('project_details', $project_details);

    $project->assignVar('project_enrollment', $project_enrollment);

    $project->assignVar('project_salesinfo', $project_salesinfo);

    $project->assignVar('project_rentinfo', $project_rentinfo);

    /** Create Handler Class **/

    $projHandler = &ProjectHandler::getInstance($xoopsDB);

    if (false !== $projHandler->insert($project)) {
        /** Correctly Inserted **/

        redirect_header('index.php', 3, _AM_ADDSUCCES);
    } else {
        /** Error On Insert **/

        redirect_header('index.php', 3, _AM_ADDERROR);
    }
}

function deleteProjConfirm()
{
    global $_GET, $HTTP_SERVER_VARS;

    $confirm_link = 'index.php?op=delete&amp;project_id=' . (int)$_GET['project_id'];

    $item_to_delete = _AM_THEPROJECT;

    xoops_cp_header();

    require __DIR__ . '/deleteconfirm.inc.php';

    xoops_cp_footer();
}

function deleteImgConfirm()
{
    global $_GET, $HTTP_SERVER_VARS;

    $confirm_link = 'index.php?op=edit&amp;action=img_delete&amp;image_id=' . (int)$_GET['image_id'] . '&amp;project_id=' . (int)$_GET['project_id'];

    $item_to_delete = _AM_THEIMAGE;

    xoops_cp_header();

    require __DIR__ . '/deleteconfirm.inc.php';

    xoops_cp_footer();
}

function deleteProject()
{
    global $_GET, $xoopsDB;

    /** Include Project class **/

    require_once XOOPS_ROOT_PATH . '/modules/projects/class/project.php';

    /** Create Handler Class **/

    $projHandler = &ProjectHandler::getInstance($xoopsDB);

    /** Get project ID **/

    $project_id = 0;

    if (isset($_GET['project_id'])) {
        $project_id = (int)$_GET['project_id'];
    }

    /** Get the project's images **/

    $criteria_compo = new CriteriaCompo();

    $criteria = new Criteria('project_id', $project_id, '=');

    $criteria_compo->add($criteria);

    $images = $projHandler->getImagesByProject($criteria_compo);

    /** Delete the project's images **/

    $imageHandler = new ProjectImageHandler($xoopsDB);

    foreach ($images as $image) {
        unlink(XOOPS_ROOT_PATH . '/modules/projects/images/projects/' . $image->getVar('image_id') . '_thumb.jpg');

        unlink(XOOPS_ROOT_PATH . '/modules/projects/images/projects/' . $image->getVar('image_id') . '_main.jpg');

        $imageHandler->delete($image);
    }

    /** Delete the project **/

    $project = new Project();

    $project->assignVar('project_id', $project_id);

    if ($projHandler->delete($project)) {
        /** Correctly deleted **/

        redirect_header('index.php', 3, _AM_DELETESUCCES);
    } else {
        /** Error on deletion **/

        redirect_header('index.php', 3, _AM_DELETEERROR);
    }
}

function editProject($preview = false)
{
    global $_GET, $_POST, $HTTP_SERVER_VARS, $xoopsDB;

    /** Include Project class **/

    require_once XOOPS_ROOT_PATH . '/modules/projects/class/project.php';

    /** Create handler class **/

    $projHandler = &ProjectHandler::getInstance($xoopsDB);

    /** Get the project's id, return if not set **/

    if (isset($_GET['project_id'])) {
        $project_id = (int)$_GET['project_id'];
    } else {
        redirect_header('index.php');
    }

    $project = new Project();

    /** Get form data form data from POST vars **/

    if ($preview) {
        /** Extract post vars, skip the ones that exist on the symbol table **/

        extract($_POST, EXTR_SKIP);

        /** Fill the storage class **/

        $project->assignVar('project_id', $project_id);

        $project->assignVar('project_name', $project_name);

        $project->assignVar('project_type', $project_type);

        $project->assignVar('project_status', $project_status);

        $project->assignVar('project_description', $project_description);

        $project->assignVar('project_areacode', $project_areacode);

        $project->assignVar('project_location', $project_location);

        $project->assignVar('project_details', $project_details);

        $project->assignVar('project_enrollment', $project_enrollment);

        $project->assignVar('project_salesinfo', $project_salesinfo);

        $project->assignVar('project_rentinfo', $project_rentinfo);
    }

    /** Get form data from database **/

    else {
        /** Fetch the project from the database plain and simple **/

        $project = $projHandler->get($project_id);
    }

    xoops_cp_header();

    /** Preview the project info **/

    require __DIR__ . '/project_preview.inc.php';

    /** Display the project info form **/

    $action_url = 'index.php?op=edit&amp;project_id=' . (int)$project->getVar('project_id');

    require __DIR__ . '/project_form.inc.php';

    /** Display the image listing **/

    $criteria_compo = new CriteriaCompo();

    $criteria = new Criteria('project_id', (int)$project_id, '=');

    $criteria_compo->add($criteria);

    $thumbnails = &$projHandler->getImagesByProject($criteria_compo);

    require __DIR__ . '/project_imagelist.inc.php';

    /** Display the image adder form **/

    $action_url = 'index.php?op=edit&amp;action=upload_image&amp;project_id=' . (int)$project->getVar('project_id');

    require __DIR__ . '/image_form.inc.php';

    xoops_cp_footer();
}

function saveEditedProject()
{
    global $_GET, $_POST, $xoopsDB;

    /** Include Project class **/

    require_once XOOPS_ROOT_PATH . '/modules/projects/class/project.php';

    /** Create handler class **/

    $projHandler = &ProjectHandler::getInstance($xoopsDB);

    /** Get the project's id, return if not set **/

    if (isset($_GET['project_id'])) {
        $project_id = (int)$_GET['project_id'];
    } else {
        redirect_header('index.php');
    }

    $project = new Project();

    /** Extract post vars, skip the ones that exist on the symbol table **/

    extract($_POST, EXTR_SKIP);

    /** Fill the storage class **/

    $project->assignVar('project_id', $project_id);

    $project->assignVar('project_name', $project_name);

    $project->assignVar('project_type', $project_type);

    $project->assignVar('project_status', $project_status);

    $project->assignVar('project_description', $project_description);

    $project->assignVar('project_areacode', $project_areacode);

    $project->assignVar('project_location', $project_location);

    $project->assignVar('project_details', $project_details);

    $project->assignVar('project_enrollment', $project_enrollment);

    $project->assignVar('project_salesinfo', $project_salesinfo);

    $project->assignVar('project_rentinfo', $project_rentinfo);

    /** Update the record in the database **/

    if ($projHandler->insert($project)) {
        /** Correctly updated **/

        redirect_header('index.php', 3, _AM_UPDATESUCCES);
    } else {
        /** Error on update **/

        redirect_header('index.php', 3, _AM_UPDATEERROR);
    }
}

function uploadImage()
{
    global $_POST, $_GET, $xoopsDB;

    /** Get the project's id, return if not set **/

    $project_id = 0;

    if (isset($_GET['project_id'])) {
        $project_id = (int)$_GET['project_id'];
    } else {
        redirect_header('index.php');
    }

    /** Include Project class **/

    require_once XOOPS_ROOT_PATH . '/modules/projects/class/project.php';

    /** Include Upload handler **/

    require_once XOOPS_ROOT_PATH . '/class/uploader.php';

    /** Create an uploader class **/

    $allowed_mimetypes = ['image/jpeg', 'image/pjpeg'];

    $maxfilesize = 1048576; /** 1MB **/

    $uploader = new XoopsMediaUploader(XOOPS_ROOT_PATH . '/modules/projects/images/projects', $allowed_mimetypes, $maxfilesize);

    /** Generate a random prefix for the temporary file **/

    require_once XOOPS_ROOT_PATH . '/modules/projects/include/functions.php';

    $prefix = randomString();

    $uploader->setPrefix($prefix);

    /** Upload the file **/

    if (!$uploader->fetchMedia('project_image')) {
        /** Error uploading **/

        redirect_header('index.php', 3, _AM_UPLOADERROR);
    }

    if (!$uploader->upload()) {
        /** Error uploading **/

        redirect_header('index.php', 3, _AM_UPLOADERROR);
    }

    /** Create a image handler **/

    $imgHandler = &ProjectImageHandler::getInstance($xoopsDB);

    /** Create an image and insert it into the database **/

    $image = new ProjectImage();

    $image->setVar('project_id', $project_id);

    $image_id = $imgHandler->insert($image);

    /** Create a main and a thumbnail from the image and resize if needed **/

    process_image($uploader->getSavedDestination(), $image_id, XOOPS_ROOT_PATH . '/modules/projects/images/projects/', true);

    /** Confirm process and redirect **/

    redirect_header('index.php?op=edit&amp;project_id=' . (int)$project_id, 3, _AM_UPLOADSUCCES);
}

function deleteImage()
{
    global $_GET, $xoopsDB;

    /** Include the project class file **/

    require_once XOOPS_ROOT_PATH . '/modules/projects/class/project.php';

    /** Errors initialy set to none **/

    $error = false;

    /** Get the image id **/

    $image_id = 0;

    if (isset($_GET['image_id'])) {
        $image_id = (int)$_GET['image_id'];
    } else {
        $error = 'Http Get-var image_id not set';
    }

    /** Get the project id **/

    $project_id = 0;

    if (isset($_GET['project_id'])) {
        $project_id = (int)$_GET['project_id'];
    } else {
        $error = 'Http Get-var project_id not set';
    }

    /** Get a ProjectImageHandler object **/

    $proj_imgHandler = &ProjectImageHandler::getInstance($xoopsDB);

    /** Get a ProjectImage object **/

    $proj_image = new ProjectImage();

    $proj_image->setVar('image_id', $image_id);

    /** Remove the image from the database **/

    if (!$proj_imgHandler->delete($proj_image)) {
        $error = 'Error deleting image from darabase';
    }

    /** Unlink the associated files **/

    if (!unlink(XOOPS_ROOT_PATH . '/modules/projects/images/projects/' . $image_id . '_thumb.jpg')) {
        $error = "Unable to delete the image's thumbnail image from filesystem";
    }

    if (!unlink(XOOPS_ROOT_PATH . '/modules/projects/images/projects/' . $image_id . '_main.jpg')) {
        $error = "Unable to delete the image's main image from filesystem";
    }

    /** Redirect **/

    if ($error) {
        redirect_header('javascript:history(-1)', 3, _AM_IMG_DELETE_ERROR);
    } else {
        redirect_header('index.php?op=edit&amp;project_id=' . $project_id, 3, _AM_IMG_DELETE_SUCCES);
    }
}
