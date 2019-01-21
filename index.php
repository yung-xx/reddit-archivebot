<?php

/* ARCHIVEBOT - INI */

ini_set('max_execution_time', 0);
require('library/IniReader.php');
require('library/IniReadingException.php');
require('library/IniWriter.php');
require('library/IniWritingException.php');
require('app/functions.php');
if (!file_exists('config.ini')) $ab_client_version = 0;
else $ab_client_version = read_ini('CLIENT', 'version');
$ab_latest_version = request('https://raw.githubusercontent.com/yung-xx/reddit-archivebot/master/version.md',null,'fetch',null);

/* ARCHIVEBOT - SELF UPDATE */

if (rtrim($ab_latest_version) > $ab_client_version) { 
update('master.tmp', 'reddit-archivebot-master/', '.');
write_ini(rtrim($ab_latest_version));
$ab_status_update = true;
$ab_client_version = read_ini('CLIENT', 'version');
}

require('archivebot.php');

?>