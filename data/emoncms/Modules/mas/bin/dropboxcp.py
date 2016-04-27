import ntpath
import sys
import random
import logging
import csv
import os
import os.path
import shutil
import numpy as np
from  daemon import Daemon
from watchdog.observers import Observer
from watchdog.events import LoggingEventHandler
from watchdog.events import FileSystemEventHandler


class RandomLoadEventHandler(FileSystemEventHandler):
    

    #First input  set into the main 
    mainuserdir=""
    dropboxdir=""
    
    
     
   
    def on_created(self, ev):
        if(ev.is_directory==False): 
            self.update_task(ev)
                    
    def on_modified(self, ev):
        if(ev.is_directory==False): 
            self.update_task(ev)      
               
                
    #This is used to delete a task from the list of schedules
    def on_deleted(self, ev):
        if(ev.is_directory==False):  
			if(os.path.exists(self.dropboxdir+"/neighbour/schedule/"+ntpath.basename(ev.src_path))):              
				os.remove(self.dropboxdir+"/neighbour/schedule/"+ntpath.basename(ev.src_path))
			if(os.path.exists(self.mainuserdir+"/neighbour/schedule/"+ntpath.basename(ev.src_path))):
				os.remove(self.mainuserdir+"/neighbour/schedule/"+ntpath.basename(ev.src_path))
	
    def update_task(self, ev):
        #set scheduled
        with open(ev.src_path) as f:
				'''
				d = dict(filter(None, csv.reader(f,  delimiter=' ', skipinitialspace=True)))           
				EST = np.int(d['EST'])
				LST = np.int(d['LST'])
				f.close()          
				r = random.randint(EST, LST)
				AST=r
				shutil.copy2(ev.src_path, "/tmp/"+ntpath.basename(ev.src_path))    
				with open("/tmp/"+ntpath.basename(ev.src_path), "a") as f:
					f.write("AST "+ np.str(AST))
					f.close()
				'''   
				loadfile=ntpath.basename(ev.src_path).rsplit( ".", 1 )[ 0 ] 
				shutil.copy2(ev.src_path,self.mainuserdir+"/neighbour/loads/"+ntpath.basename(ev.src_path))	
				loadfile=loadfile+".dataload"
				shutil.copy2(self.dropboxdir+"/neighbour/profiles/"+loadfile,self.mainuserdir+"/neighbour/profiles/"+loadfile)     
			 
				'''	
				os.chmod(self.mainuserdir+"/neighbour/profiles/"+loadfile,0666)
				shutil.copy2("/tmp/"+ntpath.basename(ev.src_path),self.dropboxdir+"/neighbour/schedule/"+ntpath.basename(ev.src_path))
				shutil.copy2("/tmp/"+ntpath.basename(ev.src_path),self.mainuserdir+"/neighbour/schedule/"+ntpath.basename(ev.src_path))
				os.chmod(self.mainuserdir+"/neighbour/schedule/"+ntpath.basename(ev.src_path),0666)
				'''
            
    
class RandomSchedD(Daemon):
    
 
      
   
    def __init__(self,pidfile):
        Daemon.__init__(self,pidfile, '/dev/null', '/dev/null', '/dev/null')
        #Daemon.__init__(self,pidfile)
    def run(self):
        self.observer = Observer()
        
        logging.basicConfig(level=logging.INFO,
                    format='%(asctime)s - %(message)s',
                    datefmt='%Y-%m-%d %H:%M:%S')
        event_handler = LoggingEventHandler()
        self.observer.schedule(event_handler, self.load_handler.dropboxdir+"/neighbour/loads", recursive=False)
        
        
        #self.observer.schedule(self.load_handler, self.load_handler.dropboxdir+"/neighbour/loads", recursive=False)
        self.observer.start()
        self.observer.join()

if __name__ == "__main__":

    daemon = RandomSchedD("/tmp/randomsched.pid")
    daemon.load_handler=RandomLoadEventHandler()
    daemon.load_handler.dropboxdir = os.environ.get('DROPBOXDIR')
    daemon.load_handler.mainuserdir = os.environ.get('MAINUSERDIR')
     
    
    if(daemon.load_handler.dropboxdir ==None):
        print "DROPBOXDIR is not set"
               
    elif len(sys.argv) == 2:
        if 'start' == sys.argv[1]:
            daemon.start()
        elif 'stop' == sys.argv[1]:
            daemon.stop()
        elif 'restart' == sys.argv[1]:
            daemon.restart()
        else:
            print "Unknown command"
            sys.exit(2)
        sys.exit(0)
    else:
        print "usage: %s start|stop|restart" % sys.argv[0]
        sys.exit(2)
    
