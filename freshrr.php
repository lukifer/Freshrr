<?php

# A PHP script that keeps your website fresh? That's called a "Freshrr". I'm goin' on break!
#
# @version 1.1
# @copyright 2012 Luke Dennis
# @license MIT: opensource.org/licenses/mit-license.php
# @requires jQuery or Zepto
#
# 
# USAGE: <script type="text/javascript" src="/freshrr.php?files=index.php,path/to/file.js&ms=1000"></script>
#
#
# ARGUMENTS: (Put these in the URL, son)
# ======================================
# - files:		Comma-separated list of paths to files, relative to freshrr.php.
# - longpoll:	Boolean to indicate a long-lived request. This gives the best performance.
# - ms:			Milliseconds between polls for updates (min 100; defaults to 250 for longpoll, and 1000 for ajax).
# - max:		Stop polling after this many minutes. Prevents server kersplosion when you fall asleep at your desk.
#
# To temporarily disable, add "?refresh=0" to the URL bar, or add "freshrr(false);" to your Javascript.
#
#
# FIERCELY ASKED QUESTION(S)
# ==========================
# Q: How do I handle files with unusual characters in the name, such as a comma or ampersand?
# A: The doctor says, "don't do that".
#
# Q: How come you pollute the global namespace in Javascript?
# A: Cry me a river, hippie.

 

# Manual kill switch
#die();

# URL-based kill switch: domain.com?refresh=0
#if(isset($_GET['refresh']) && empty($_GET['refresh']))
#	die();

# Retrieve timer options
$longpoll = !isset($_GET['longpoll']) || $_GET['longpoll'] == false;
$longpoll_sec = 8;
$default_ms = $longpoll ? 250 : 1000;
$refresh_ms = !@empty($_GET['ms']) && $_GET['ms'] >= 100 ? intval($_GET['ms']) : $default_ms;
$max_time = !@empty($_GET['max']) ? intval($_GET['max']) : 60; // stop trying after an hour or so
$max_refreshes = $longpoll ? ($max_time * 60 / $longpoll_sec) : ($max_time * 60 * 1000 / $refresh_ms);


# Begin Javascript
if(@empty($_GET['timestamps'])) { header("Content-Type: application/javascript"); ?>


// Manually enable/disable from JS console
function freshrr(bool)
{
	if(bool)
	{
		if(!freshrrTimer)
			freshrrRequest();
	}
	else
	{
		clearTimeout(freshrrTimer);
		freshrrTimer = false;
	}
}

 
function freshrrCallback(json)
{
	if(!freshrrTimer) return;

	var timestamps = window.freshrrTimestamps;
	if(timestamps)
	{
		for(var site in json)
		{
			if(!timestamps[site]) continue;
			if(timestamps[site] <?php echo '<'; ?> json[site])
				window.location.reload();
		}
		window.refreshCount++;
	}
	else window.refreshCount = 1;

	window.freshrrTimestamps = json;
	
	if(json.off == undefined && window.refreshCount <?php echo '< ' . $max_refreshes; ?>)
		freshrrTimer = setTimeout(freshrrRequest, <?php echo $refresh_ms; ?>);
}

function freshrrRequest()
{
	var url = "<?php echo isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : 'freshrr.php';
		?>?timestamps=1<?php if(!@empty($_GET['files'])) echo '&files='.$_GET['files']; ?>";

	if(!window.freshrrTimestamps) url += '&first=1';

	$.getJSON(url, freshrrCallback);
}

$(function(){
	if(!document.location.search || document.location.search.indexOf("refresh=0") == -1)
	{
		freshrrTimer = true;
		freshrrRequest();
	}
});

<?php die(); }
# End Javascript
 
 
 

# Begin PHP/JSON
header('application/json');

$start_time = time();
$files = @empty($_GET['files']) ? array('index.php') : explode(',', $_GET['files']);

function getFileTimestamp($file)
{
	$f = trim($file);

	// todo: recurseplz
	if(@is_dir($f))
	{
		$stamp = 0;
		$d = @opendir($f);
		while(($df = @readdir($d)) !== false)
		{
			$stamp = max($stamp, @filemtime($df));
			@clearstatcache($df);
		}
		if($d) closedir($d);
	}
	else
	{
		$stamp = @filemtime($f);
		@clearstatcache($f);
	}
	
	return $stamp;
}


# Single-run mode (Ajax)
if(!$longpoll || !@empty($_GET['first']))
{
	$stamps = array();
	foreach($files as $f)
		$stamps[$f] = getFileTimestamp($f);

	# Echo file modification timestamps
	die(json_encode($stamps));
}


# Long-polling mode (Comet)

# First Run
$initial_stamps = array();
foreach($files as $f)
	$initial_stamps[$f] = getFileTimestamp($f);

# Loop until a change is found
while(time() < $start_time + $longpoll_sec)
{
	$stamps = array();
	foreach($files as $f)
	{
		$stamps[$f] = getFileTimestamp($f);

		if($stamps[$f] != $initial_stamps[$f])
			die(json_encode($stamps));
	}
	
	usleep($refresh_ms * 1000);
}

# Echo file modification timestamps
die(json_encode($stamps));