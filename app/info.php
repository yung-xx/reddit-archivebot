<?php

/* INFO */

echo '<div class="content-dis">';
if (isset($_POST['subreddit'])) {
if ($reddit_about AND !isset($reddit_about['error'])) {
echo "<p><span class='param'>SUBREDDIT</span>&nbsp;&nbsp;" . $reddit_about['data']['display_name_prefixed'] . "</p>";
if ($reddit_about['data']['public_description']!='') echo "<p><span class='param'>DESCRIPTION</span>&nbsp;&nbsp;" . $reddit_about['data']['public_description'] . "</p>";
echo "<p><span class='param'>SUBSCRIBERS</span>&nbsp;&nbsp;" . $reddit_about['data']['subscribers'] . "&nbsp;(" . $reddit_about['data']['active_user_count'] . " online)</p>";
echo "<p><span class='param'>CREATED</span>&nbsp;&nbsp;" . date("M d, Y G:i", $reddit_about['data']['created']) . "</p>";
echo "<p><span style='cursor:help;' class='param'>POST COUNT</span>&nbsp;&nbsp;" . $pushshift_about['metadata']['total_results'];
if ($ps_shards!=$ps_successful_shards) echo '&nbsp;<span style="cursor:pointer;" class="tooltip-top" data-tooltip="Only '.$ps_successful_shards.' out of '.$ps_shards.' data shards could be reached. Some posts may not have been retrieved.">&nbsp;<i style="color:#FF4500;" class="fas fa-exclamation-circle"></i></span>';
echo "</p>";
}

else {
echo "<p><span class='param'>SUBREDDIT</span>&nbsp;&nbsp;r/" . $_POST['subreddit'] . "</p>";
if (isset($reddit_about['error'])) echo "<p><span style='cursor:help;' class='param tooltip-top' data-tooltip='Error code'>STATUS</span>&nbsp;&nbsp;" . $reddit_about['error'] . "</p>";
else echo "<p><span style='cursor:help;' class='param tooltip-top' data-tooltip='Error code'>NOTE</span>&nbsp;&nbsp;404</p>";
if (isset($reddit_about['reason'])) echo "<p><span style='cursor:help;' class='param tooltip-top' data-tooltip='Reason given'>NOTE</span>&nbsp;&nbsp;" . $reddit_about['reason'] . "</p>";
else echo "<p><span style='cursor:help;' class='param tooltip-top' data-tooltip='Reason given'>NOTE</span>&nbsp;&nbsp;not found</p>";
}
}
else {
echo "<p><span class='param'>WELCOME</span>&nbsp;&nbsp;Please select a subreddit to get started.</p>";
if (isset($ab_status_update)) echo "<p><span class='param'>UPDATE</span>&nbsp;&nbsp; Successfully updated to version&nbsp;" . rtrim($ab_latest_version) . "!</p>";
}
echo '</div>';

?>