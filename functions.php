<?php
//include necessary files
include dirname(__FILE__) . "/incl/getytcaptions.php";
include dirname(__FILE__) . "/tempinfo.php";

//todo automatically choose category based on title
function autoselectcategory($titleinfunc)
{

    $thiscat = 0;
    $titleinfunc = strtolower($titleinfunc);
    if (strpos($titleinfunc, 'bryan') !== false) {
        $thiscat = 157;
    } elseif (strpos($titleinfunc, 'talk') !== false) {
        $thiscat = 24;
    } else {
        $thiscat = 0;
    }

    if ($thiscat != 0) {
        $defaultcat = array(2, 6, $thiscat);
    } else { $defaultcat = array(2, 6);}

    return $defaultcat;

//echo "result".
}
