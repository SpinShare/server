<?php

namespace App\Utils;

use App\Utils\HelperFunctions;

class HelperFunctions {
    public static function delTree($dir) {
        $hf = new HelperFunctions();

        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $hf->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    // Source: https://davidwalsh.name/create-image-thumbnail-php
    // Creating a Thumbnail without Imagick
    public static function generateThumbnail($src, $dest, $thumbnailSize) {
        /* Using imagecreatfromstring and file_get_contents because we don't if the file will be png/gif/jpg */
        $source_image = imagecreatefromstring(file_get_contents($src));
        $width = imagesx($source_image);
        $height = imagesy($source_image);
        
        // Resizing the image
        $virtual_image = imagecreatetruecolor($thumbnailSize, $thumbnailSize);
        
        // Resample Image
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $thumbnailSize, $thumbnailSize, $width, $height);
        
        // Save Image
        imagejpeg($virtual_image, $dest);
    }
}