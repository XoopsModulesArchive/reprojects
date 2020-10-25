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
$name_text = new XoopsFormText(_AM_NAME, 'project_name', 50, 255, $project->getVar('project_name', 'f'));
$description_dhtmltextarea = new XoopsFormDhtmlTextArea(_AM_DESCRIPTION, 'project_description', $project->getVar('project_description', 'f'));
$type_select = new XoopsFormSelect(_AM_TYPE, 'project_type', $project->getVar('project_type'));
$type_select->addOption(1, _AM_RESIDENTIAL);
$type_select->addOption(2, _AM_COMMERCIAL);
$type_select->addOption(3, _AM_HORECA);
$location_text = new XoopsFormText(_AM_LOCATION, 'project_location', 50, 255, $project->getVar('project_location'));
$areacode_text = new XoopsFormText(_AM_AREACODE, 'project_areacode', 50, 10, $project->getVar('project_areacode', 'f'));
$status_select = new XoopsFormSelect(_AM_STATUS, 'project_status', $project->getVar('project_status'));
$status_select->addOption(1, _AM_INPROGRESS);
$status_select->addOption(2, _AM_DONE);
$details_dhtmltextarea = new XoopsFormDhtmlTextArea(_AM_DETAILS, 'project_details', $project->getVar('project_details', 'f'));
$enrollment_dhtmltextarea = new XoopsFormDhtmlTextArea(_AM_ENROLLMENT, 'project_enrollment', $project->getVar('project_enrollment', 'f'));
$salesinfo_dhtmltextarea = new XoopsFormDhtmlTextArea(_AM_SALESINFO, 'project_salesinfo', $project->getVar('project_salesinfo', 'f'));
$rentinfo_dhtmltextarea = new XoopsFormDhtmlTextArea(_AM_RENTINFO, 'project_rentinfo', $project->getVar('project_rentinfo', 'f'));
$action_select = new XoopsFormSelect(_AM_ACTION, 'action', 'preview');
$action_select->addOption('preview', _AM_PREVIEW);
$action_select->addOption('save', _AM_SAVE);
$submit_button = new XoopsFormButton('', 'submit', _AM_SUBMIT, 'submit');
$contact_form = new XoopsThemeForm(_AM_PROJECT, 'projectform', $action_url);
$contact_form->addElement($name_text, true);
$contact_form->addElement($description_dhtmltextarea);
$contact_form->addElement($type_select);
$contact_form->addElement($location_text, true);
$contact_form->addElement($areacode_text, true);
$contact_form->addElement($status_select);
$contact_form->addElement($details_dhtmltextarea);
$contact_form->addElement($enrollment_dhtmltextarea);
$contact_form->addElement($salesinfo_dhtmltextarea);
$contact_form->addElement($rentinfo_dhtmltextarea);
$contact_form->addElement($action_select);
$contact_form->addElement($submit_button);
echo $contact_form->render();
