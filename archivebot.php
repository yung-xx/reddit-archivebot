<?php

/* ARCHIVEBOT - API CALLS */

if(isset($_POST['submit'])){
$_POST['subreddit'] = sanitise(str_replace('!','',$_POST['subreddit']));
$_POST['after'] = strtotime($_POST['after']);
$ps_url_params = '';
if (!empty($_POST['author'])) { $_POST['author'] = sanitise(preg_replace('/\s+/','', $_POST['author'])); $ps_url_params = $ps_url_params . '&author=' . $_POST['author'];}
if (!empty($_POST['keywords'])) { $_POST['keywords'] = preg_replace('/\s+/','%20', $_POST['keywords']); $ps_url_params = $ps_url_params . '&q=' . $_POST['keywords'];}
if (!empty($_POST['score'])) { $_POST['score'] = preg_replace('[^0-9,<>]','', $_POST['score']); $ps_url_params = $ps_url_params . '&score=' . $_POST['score'];}
if (!empty($_POST['before'])) { $_POST['before'] = strtotime($_POST['before']); $ps_url_params = $ps_url_params . '&before=' . $_POST['before'];}
if (isset($_POST['simple'])) $ps_url_filters = '&filter=created_utc,id,subreddit_id,full_link';
else $ps_url_filters = '&filter=author,created_utc,id,num_comments,score,subreddit_id,title,full_link';

$reddit_wiki = json_decode(request('https://www.reddit.com/r/' . $_POST['subreddit'] . '/wiki/pages.json',null,'fetch',null),true);
$reddit_about = json_decode(request('https://www.reddit.com/r/' . $_POST['subreddit'] . '/about.json',null,'fetch',null), true);
$pushshift_about = json_decode(request('https://api.pushshift.io/reddit/search/submission/?subreddit=' . $_POST['subreddit'] . '&metadata=true&size=0',null,'fetch',null), TRUE);
$ps_url = 'https://api.pushshift.io/reddit/submission/search/?subreddit=' . $_POST['subreddit'] . '&after=' . $_POST['after'] . '&size=1000'.$ps_url_filters.$ps_url_params;
$pushshift = json_decode(request($ps_url,null,'fetch',null), TRUE);

/* ARCHIVEBOT - VARIABLES */

$ab_count = 0;
$GLOBALS['error_count'] = 0;
$ps_shards = $pushshift_about['metadata']['shards']['total'];
$ps_successful_shards = $pushshift_about['metadata']['shards']['successful'];

if (isset($_POST['debug'])) { 
$GLOBALS['size_count'] = 0;
$debug_requests = 0;
$debug_first_call = $ps_url;
$debug_date_array = array();
}
}

?>

<html>
<head>
<meta charset="utf-8"/>
<title>Reddit ArchiveBot</title>
<link rel="stylesheet" type="text/css" href="index.css">
<link rel="stylesheet" type="text/css" href="font/fontawesome.min.css">
</head>
<body>

<div class="main">
<h1 style='margin-bottom:-4px;'>
Reddit ArchiveBot
<svg style="height: 32px;width: 32px;vertical-align:middle;margin-top: -4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><g><circle fill="<?php if (isset($reddit_about['data']['primary_color']) AND !empty($reddit_about['data']['primary_color'])) echo $reddit_about['data']['primary_color']; else echo '#FF4500';?>" cx="10" cy="10" r="10"></circle><path fill="#FFF" d="M16.67,10A1.46,1.46,0,0,0,14.2,9a7.12,7.12,0,0,0-3.85-1.23L11,4.65,13.14,5.1a1,1,0,1,0,.13-0.61L10.82,4a0.31,0.31,0,0,0-.37.24L9.71,7.71a7.14,7.14,0,0,0-3.9,1.23A1.46,1.46,0,1,0,4.2,11.33a2.87,2.87,0,0,0,0,.44c0,2.24,2.61,4.06,5.83,4.06s5.83-1.82,5.83-4.06a2.87,2.87,0,0,0,0-.44A1.46,1.46,0,0,0,16.67,10Zm-10,1a1,1,0,1,1,1,1A1,1,0,0,1,6.67,11Zm5.81,2.75a3.84,3.84,0,0,1-2.47.77,3.84,3.84,0,0,1-2.47-.77,0.27,0.27,0,0,1,.38-0.38A3.27,3.27,0,0,0,10,14a3.28,3.28,0,0,0,2.09-.61A0.27,0.27,0,1,1,12.48,13.79Zm-0.18-1.71a1,1,0,1,1,1-1A1,1,0,0,1,12.29,12.08Z"></path></g></svg>
<br>
<small style="font-weight:lighter;font-size:14px;text-align:left;">by <a href='https://www.reddit.com/user/yung_xx'>u/yung_xx</a></small></h1>

<input type="radio" id="tab-1" name="show" checked/>
<input type="radio" id="tab-2" name="show" />
<input type="radio" id="tab-3" name="show" />
<input type="radio" id="tab-4" name="show" />

<div class="tab">
<label for="tab-1">INFO</label>
<label for="tab-2">OPTIONS</label>
<label for="tab-3">USAGE</label>
<label for="tab-4">ABOUT</label>
</div>

<div class="content">
<?php require('app/info.php'); require('app/options.php'); require('app/usage.html'); require('app/about.php'); ?>
</div>

<br>
<div class="content" style="padding:20px;border:1px solid #bbb;min-height: 0px;">
<?php

$time_start = microtime_float();

if (isset($reddit_about) AND !empty($reddit_about) AND !isset($reddit_about['error'])) {
	
/* ARCHIVEBOT - OAUTH UUID */

$oauth_uuid = read_ini('CLIENT', 'uuid');
if (!preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $oauth_uuid)) $oauth_uuid = 'DO_NOT_TRACK_THIS_DEVICE';
	
/* ARCHIVEBOT - OAUTH GRANT */

$oauth_client_id = read_ini('CLIENT', 'client-id');
$oauth_user_agent = read_ini('CLIENT', 'user-agent') . '@' . $_POST['subreddit'];
$oauth = json_decode(request('https://www.reddit.com/api/v1/access_token',$oauth_client_id,'grant',$oauth_uuid), TRUE);
$oauth_access_token = $oauth['access_token'];

/* ARCHIVEBOT - BACKUP */

if ($_POST['backup']=='yes') {
$rc_post_id_array[] = array("id"=>'about',"path"=>'',"date"=>'about/');
foreach($reddit_wiki['data'] as $page){
if (strpos($page, "/")) $rc_post_id_array[] = array("id"=>substr($page, strpos($page, "/") + 1),"path"=>'wiki/'.substr($page, 0, strrpos($page, '/')).'/',"date"=>'');
else $rc_post_id_array[] = array("id"=>$page,"path"=>'wiki/',"date"=>'');
}
}

/* ARCHIVEBOT - LISTING */

while ($pushshift['data']) {

foreach($pushshift['data'] as $post){
	
$directory = 'data/' . $_POST['subreddit'] . '/comments/' . date("M-Y", $post['created_utc']);
$current_date = $post['created_utc'];

if (isset($_POST['smart'])) { if (!file_exists($directory . '/' . $post['id'] . '.json')) $ab_smart_ignore = false; else $ab_smart_ignore = true; }
else $ab_smart_ignore = false;

if ($ab_smart_ignore == false) {
$ab_count = $ab_count+1;
if (isset($_POST['simple'])) echo "<span class='cfont'>[<u>" . $ab_count . "</u>]&nbsp;<a class='no-style' href='" . $post['full_link'] . "'>" . $post['id'] . "</a>&nbsp;[<u>" . date("M d, Y G:i", $post['created_utc']) . "</u>]</span></span><br>";
else { $post_title = strip_tags((strlen($post['title']) > 50) ? substr($post['title'], 0, 50) . '...' : $post['title']);
echo "<span class='cfont'>[<u>" . $ab_count . "</u>] [<span class='tooltip-top' data-tooltip='" . $post['id'] . "' style='cursor:pointer;'>+</span>] " . "<a class='no-style' href='" . $post['full_link'] . "'>" . $post_title . "</a> <span style='font-size:14px;'>[by <a class='no-style' style='font-weight:bold;' href='https://www.reddit.com/user/" . $post['author'] . "'>" . $post['author'] . "</a> <u>" . $post['num_comments'] . " comment(s)</u>, <u>" . $post['score'] . " point(s)</u>] [<u>" . date("M d, Y G:i", $post['created_utc']) . "</u>]</span></span><br>";
}
if (isset($_POST['backup']) AND $_POST['backup']=='yes') 
$rc_post_id_array[] = array("id"=>$post['id'],"path"=>'comments/',"date"=>date("M-Y", $post['created_utc']).'/');
}

}

if (isset($_POST['debug'])) {
$debug_old_date = $current_date;
array_push($debug_date_array, $debug_old_date);
}
$ps_url = 'https://api.pushshift.io/reddit/submission/search/?subreddit=' . $_POST['subreddit'] . '&after=' . $current_date . '&size=1000'.$ps_url_filters.$ps_url_params;
$pushshift = json_decode(request($ps_url,null,'fetch',null), TRUE);
if (isset($_POST['debug'])) $debug_requests = $debug_requests+1;

}

/* ARCHIVEBOT - BACKUP */

if (isset($_POST['backup']) AND $_POST['backup']=='yes') {
require('library/RollingCurl.php');
$rc = new RollingCurl("request_callback");
$rc->options = array(CURLOPT_CONNECTTIMEOUT => $_POST['timeout'], CURLOPT_TIMEOUT => $_POST['timeout'], CURLOPT_USERAGENT  => $oauth_user_agent, CURLOPT_HTTPHEADER => array('Authorization: bearer ' . $oauth_access_token));
$rc->window_size = $_POST['rate'];
foreach ($rc_post_id_array as $post) {
$directory = 'data/' . $_POST['subreddit'] . '/' . $post['path'] . $post['date'];
if (!file_exists($directory)) mkdir($directory, 0777, true);
$request = new RollingCurlRequest('https://oauth.reddit.com/r/' . $_POST['subreddit'] . '/' . $post['path'] . $post['id'] . '.json?path=' . $post['path'].$post['date'] . '&file=' . $post['id']);
$rc->add($request);
}
$rc->execute();
unset($rc);
}

/* ARCHIVEBOT - COMPRESSION */

if ($_POST['backup']=='yes' AND isset($_POST['compression']) AND $ab_count > 0) zippy(realpath('data/' . $_POST['subreddit']), 'data/' . $_POST['subreddit'] . '.zip');

/* ARCHIVEBOT - OAUTH REVOKE */

if (isset($_POST['submit'])) $oauth = json_decode(request('https://www.reddit.com/api/v1/revoke_token',$oauth_client_id,'revoke',$oauth_access_token), TRUE);

}

/* ARCHIVEBOT - DONE */

$time_end = microtime_float(); 
$time = $time_end - $time_start;

if(isset($ab_count) AND $ab_count > 0) echo "<p class='cfont' style='color:#238a23;'>" . $ab_count . " SUBMISSION(s) IN " . time_print(round($time, 2)) . "</p>";
else echo "<p class='cfont' style='color:#238a23;'>NOTHING TO RETRIEVE</p>";
if(isset($GLOBALS['error_count']) AND $GLOBALS['error_count'] > 0) echo "<p class='cfont' style='color:#e23f3f;'>" . $GLOBALS['error_count'] . " REQUEST(s) FAILED (SEE: USAGE – MAX. CONNECTIONS)</p>";

?>
</div>
<br>
<?php

/* ARCHIVEBOT - DEBUG */

if (isset($_POST['debug'])) require('app/debug.php');

?>
<br>
</body>
</html>