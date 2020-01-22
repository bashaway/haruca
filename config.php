<?php

// To show the haruca Tab, set to TRUE
// To show it as a menu option, set to FALSE

$haruca_tab = TRUE;
//$haruca_tab = FALSE;

$basepath = getcwd()."/";
$binpath = getcwd()."/bin/";
$datpath = getcwd()."/dat/";
$datoldpath = getcwd()."/dat/old/";
$tmpimgpath = getcwd()."/images/haruca_";
$tmppath = "/tmp/haruca_";
$tmpdir  = "/tmp/";
$perlpath = "/usr/bin/perl";
$cactipath = preg_replace("/(.*)(\/[^\/]+)\/[^\/]+\/[^\/]+/","$2",getcwd());

?>
