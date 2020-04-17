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
}