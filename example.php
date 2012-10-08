<html>
<head>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/zepto/1.0rc1/zepto.min.js"></script>
	<script type="text/javascript" src="/freshrr.php?files=example.php,freshrr.php"></script>
</head>
<body>

<div>
	<p>Loaded on <?php echo date("g:i:s"); ?></p>
	<p>Time since last refresh: <span id="timer"></span></p>
</div>

<script type="text/javascript">
var start = (new Date()).getTime();
window.timer = setInterval(function()
{
	var sec = (new Date()).getTime() - start;
	$("#timer").html(parseInt(sec/1000/60)+"m "+parseInt(sec/1000)%60+"s");
}, 1000);
</script>

</body>
</html>