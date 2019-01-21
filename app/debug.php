<?php

/* DEBUG */

echo '<div class="content" style="padding:20px;border:1px solid #bbb;min-height: 0px;">';
echo '<p><span class="param">DEBUG</span></p>';
echo '<p><span class="param code">PHP VERSION</span>&nbsp;&nbsp;' . phpversion() . '</p>';
echo '<p><span class="param code">API CALL</span>&nbsp;&nbsp;' . '?subreddit=' . $_POST['subreddit'] . '&after=' . $_POST['after'] . $ps_url_params . $ps_url_filters . '</p>';
echo '<p><span class="param code">SHARDS</span>&nbsp;&nbsp;' . $ps_successful_shards . '&nbsp;out of&nbsp;' . $ps_shards . '</p>';
echo '<p><span class="param code">TIMESTAMP: FROM</span>&nbsp;&nbsp;' . $_POST['after'] . '</p>';
if (empty($_POST['before'])) $_POST['before'] = 'none';
echo '<p><span class="param code">TIMESTAMP: TO</span>&nbsp;&nbsp;' . $_POST['before'] . '</p>';
if (!isset($current_date)) $current_date = 'none';
echo '<p><span class="param code">TIMESTAMP: CURRENT</span>&nbsp;&nbsp;' . $current_date . '</p>';
echo '<p><span class="param code">TIMESTAMP: PAGINATION</span>&nbsp;&nbsp;';
print_r($debug_date_array);
echo '</p>';
echo '<p><span class="param code">USER-AGENT</span>&nbsp;&nbsp;' . $oauth_user_agent . '</p>';
echo '<p><span class="param code">CLIENT ID</span>&nbsp;&nbsp;' . $oauth_client_id . '</p>';
echo '<p><span class="param code">ONE-TIME ACCESS TOKEN</span>&nbsp;&nbsp;' . $oauth_access_token . '</p>';
echo '<p><span class="param code">UUID</span>&nbsp;&nbsp;' . $oauth_uuid . '</p>';
echo '<p><span class="param code">MEMORY USAGE</span>&nbsp;&nbsp;' . round(memory_get_usage()/1048576, 2) . '&nbsp;megabytes, peaked at&nbsp;' . round(memory_get_peak_usage()/1048576, 2) . '</p>';
if($GLOBALS['size_count'] > 0) echo '<p><span class="param code">DOWNLOAD SIZE</span>&nbsp;&nbsp;' . bytes_print($GLOBALS['size_count']) . '</p>';
if($ab_count > 0) echo '<p><span class="param code">EXECUTION TIME</span>&nbsp;&nbsp;' . $debug_requests . '&nbsp;page(s) for&nbsp;' . strtolower(time_print(round($time/$debug_requests, 3))) . '&nbsp;per itineration,&nbsp;' . $ab_count . '&nbsp;post(s) for&nbsp;' . strtolower(time_print(round($time/$ab_count, 3))) . '&nbsp;per request</p>';
echo '</div>';

?>