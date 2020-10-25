<?php

/**
 * @author	    Dirk Louwers	<dirk_louwers@hotmail.com>
 * @copyright	copyright (c) 	2005 Dirk Louwers
 */
function randomString()
{
    return md5(uniqid(mt_rand()));
}

function process_image($source, $id, $destination_path, $resample = false)
{
    global $xoopsModuleConfig;

    $im = imagecreatefromjpeg($source);

    // Get the size of the image

    $dx = imagesx($im);

    $dy = imagesy($im);

    // Determine wether the thumb image needs to be shrunk to the max

    // size of 160x120 and calculate dimensions and prceed if need be.

    $thumb_im;

    if (($dx > 160) or ($dy > 120)) {
        $thumb_x;

        $thumb_y;

        if (($dx / 160) > ($dy / 120)) {
            $thumb_x = $dx * (160 / $dx);

            $thumb_y = $dy * (160 / $dx);
        } else {
            $thumb_x = $dx * (120 / $dy);

            $thumb_y = $dy * (120 / $dy);
        }

        // Create thumbnail image, save it and free resources

        $thumb_im = imagecreatetruecolor($thumb_x, $thumb_y);

        if ($resample) {
            imagecopyresampled($thumb_im, $im, 0, 0, 0, 0, $thumb_x, $thumb_y, $dx, $dy);
        } else {
            imagecopyresized($thumb_im, $im, 0, 0, 0, 0, $thumb_x, $thumb_y, $dx, $dy);
        }

        imagejpeg($thumb_im, $destination_path . $id . '_thumb.jpg');

        imagedestroy($thumb_im);
    } else {
        // Image doesn't need resizing, it will be copied to the right location

        copy($source, $destination_path . $id . '_thumb.jpg');
    }

    // Determine wether the main image needs to be shrunk to the max

    // size of 800x600 and calculate dimensions

    $main_im;

    if (($dx > 400) or ($dy > 300)) {
        $main_x;

        $main_y;

        if (($dx / 400) > ($dy / 300)) {
            $main_x = $dx * (400 / $dx);

            $main_y = $dy * (400 / $dx);
        } else {
            $main_x = $dx * (300 / $dy);

            $main_y = $dy * (300 / $dy);
        }

        // Create main image, save it and free resources

        $main_im = imagecreatetruecolor($main_x, $main_y);

        if ($resample) {
            imagecopyresampled($main_im, $im, 0, 0, 0, 0, $main_x, $main_y, $dx, $dy);
        } else {
            imagecopyresized($main_im, $im, 0, 0, 0, 0, $main_x, $main_y, $dx, $dy);
        }

        imagejpeg($main_im, $destination_path . $id . '_main.jpg');

        imagedestroy($main_im);
    } else {
        // Image doesn't need resizing, it will be copied to the right location

        copy($source, $destination_path . $id . '_main.jpg');
    }

    // Delete the original image file as it is no longer needed

    unlink($source);
}
