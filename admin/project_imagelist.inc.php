<?php

/**
 * @author	    Dirk Louwers	<dirk_louwers@hotmail.com>
 * @copyright	copyright (c) 	2005 Dirk Louwers
 */
if (!preg_match('/index.php/', $HTTP_SERVER_VARS['PHP_SELF'])) {
    exit('Access Denied');
}

echo "<table class='outer' cellspacing='1'>\n";
echo "<tr><th colspan='2'>" . _AM_IMAGELIST . "</th></tr>\n";
$row_counter = 0;
foreach ($thumbnails as $t) {
    $class_name = (0 == $row_counter % 2) ? 'even' : 'odd';

    $img_link = XOOPS_URL . '/modules/projects/images/projects/' . $t->getVar('image_id') . '_thumb.jpg';

    $deletion_link = 'index.php?op=edit&amp;action=img_delete_confirm&amp;project_id=' . $t->getVar('project_id') . '&amp;image_id=' . $t->getVar('image_id');

    echo "<tr class='" . $class_name . "'>\n";

    echo "<td><img src='" . $img_link . "'></td>\n";

    echo "<td>[<a href='" . $deletion_link . "'>" . _AM_DELETE . "</a>]</td>\n";

    echo "</tr>\n";
}
echo "</table>\n";
