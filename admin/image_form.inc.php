<?php

/**
 * @author	    Dirk Louwers	<dirk_louwers@hotmail.com>
 * @copyright	copyright (c) 	2005 Dirk Louwers
 */
if (!preg_match('/index.php/', $HTTP_SERVER_VARS['PHP_SELF'])) {
    exit('Access Denied');
}

/** Build the form **/
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
$file_file = new XoopsFormFile(_AM_FILE, 'project_image', 1048576);
$submit_button = new XoopsFormButton('', 'submit', _AM_SUBMIT, 'submit');
$contact_form = new XoopsThemeForm(_AM_ADDIMAGE, 'imageform', $action_url);
$contact_form->setExtra('enctype="multipart/form-data"');
$contact_form->addElement($file_file, true);
$contact_form->addElement($submit_button);
echo $contact_form->render();
