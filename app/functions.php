<?php

function microtime_float() {
list($usec, $sec) = explode(" ", microtime());
return ((float)$usec + (float)$sec);
}

function xcopy($source, $dest, $permissions = 0755) {
if (is_link($source)) return symlink(readlink($source), $dest);
if (is_file($source)) return copy($source, $dest);
if (!is_dir($dest)) mkdir($dest, $permissions);
$dir = dir($source);
while (false !== $entry = $dir->read()) {
if ($entry == '.' || $entry == '..') continue;
xcopy("$source/$entry", "$dest/$entry", $permissions);
}
$dir->close();
return true;
}

function xunlink($filename, $source) {
unlink($filename);
$di = new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS);
$ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
foreach ($ri as $file) {
$file->isDir() ?  rmdir($file) : unlink($file);
}
rmdir($source);
return true;
}

function update($filename, $source, $dest) {
$update = request('https://codeload.github.com/yung-xx/reddit-archivebot/zip/master',null,'fetch',null);
file_put_contents($filename, $update);
$zip = new ZipArchive;
$res = $zip->open($filename);
if ($res === TRUE) {
$zip->extractTo('.');
$zip->close();
xcopy($source, $dest);
xunlink($filename, $source);
}
}

function uuid_print() {
return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
mt_rand( 0, 0xffff ),
mt_rand( 0, 0x0fff ) | 0x4000,
mt_rand( 0, 0x3fff ) | 0x8000,
mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
);
}

function time_print($val) {
if ($val < 60) $val = $val . '&nbsp;SECOND(s)';
else if ($val > 60) $val = round(floor(($val / 60) % 60),1) . '&nbsp;MINUTE(s)';
else if ($val > 3600) $val = round(floor($val / 3600),0) . '&nbsp;HOUR(s)';
return $val;
}

function bytes_print($bytes, $precision = 2) { 
$units = array('bytes', 'kilobytes', 'megabytes', 'gigabytes'); 
$bytes = max($bytes, 0); 
$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
$pow = min($pow, count($units) - 1); 
$bytes /= pow(1024, $pow);
return round($bytes, $precision) . '&nbsp;' . $units[$pow]; 
} 

function request($url, $token, $type, $opt) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
if ($type=='fetch') { curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json')); }
if ($type=='grant' OR $type=='revoke') { curl_setopt($ch, CURLOPT_POST, 1); curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode($token.':'), 'Content-Type: application/x-www-form-urlencoded')); }
if ($type=='grant') curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type='.urlencode('https://oauth.reddit.com/grants/installed_client').'&device_id='.$opt);
if ($type=='revoke') curl_setopt($ch, CURLOPT_POSTFIELDS, 'token='.$opt.'&token_type_hint=access_token');
$response = curl_exec($ch);
curl_close($ch);
return $response;
}

function request_callback($response, $info) {
parse_str(parse_url($info['url'], PHP_URL_QUERY), $rc_request_param_array);
$file = $rc_request_param_array['file'] . '.json';
$path = 'data/' . $_POST['subreddit'] . '/' . $rc_request_param_array['path'];
if ($info['http_code']=='200') { file_put_contents($path.$file, $response); $GLOBALS['size_count'] = $GLOBALS['size_count']+$info['size_download'];}
else { file_put_contents('log.txt', date('M d, Y H:i') . ' ERROR: server returned code ' . $info['http_code'] . ' for ' . $info['url'] . PHP_EOL , FILE_APPEND | LOCK_EX); $GLOBALS['error_count'] = $GLOBALS['error_count']+1;}
}

function zippy($path, $filename) {
$zip = new ZipArchive();
$zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
$files = new RecursiveIteratorIterator(
new RecursiveDirectoryIterator($path),
RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($files as $name => $file){
if (!$file->isDir()){
$filePath = $file->getRealPath();
$relativePath = substr($filePath, strlen($path) + 1);
$zip->addFile($filePath, $relativePath);
}
}
$zip->close();
}

function sanitise($text) {
$utf8 = array(
'/[áàâãªä]/u'   =>   'a',
'/[ÁÀÂÃÄ]/u'    =>   'A',
'/[ÍÌÎÏ]/u'     =>   'I',
'/[íìîï]/u'     =>   'i',
'/[éèêë]/u'     =>   'e',
'/[ÉÈÊË]/u'     =>   'E',
'/[óòôõºö]/u'   =>   'o',
'/[ÓÒÔÕÖ]/u'    =>   'O',
'/[úùûü]/u'     =>   'u',
'/[ÚÙÛÜ]/u'     =>   'U',
'/ç/'           =>   'c',
'/Ç/'           =>   'C',
'/ñ/'           =>   'n',
'/Ñ/'           =>   'N',
'/–/'           =>   '-',
'/[’‘‹›‚]/u'    =>   ' ',
'/[“”«»„]/u'    =>   ' ',
'/ /'           =>   ' ',
);
return preg_replace(array_keys($utf8), array_values($utf8), $text);
}

function read_ini($primary, $key) {
$ab_ini_reader = new IniReader();
$ab_ini = $ab_ini_reader->readFile('config.ini');
return $ab_ini[$primary][$key];
}

function write_ini($ver) {
$ab_ini_writer = new IniWriter();
$ab_ini_array = array('CLIENT' => array('version' => $ver,'client-id' => '608QKh029X3p5A','uuid' => uuid_print(),'user-agent' => 'archivebot:worker'));
$ab_ini_writer->writeToFile('config.ini', $ab_ini_array, '; Do not edit this file unless you know exactly what you are doing. Please and thank you.'."\r\n\r\n");
}

?>