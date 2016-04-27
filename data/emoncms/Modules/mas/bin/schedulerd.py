import httplib2
import sys
import glob
from threading import Timer
import datetime
import logging
import csv
import os
from  daemon import Daemon
from watchdog.observers import Observer
from watchdog.events import LoggingEventHandler
from watchdog.events import FileSystemEventHandler
import numpy as np

class LoadEventHandler(FileSystemEventHandler):
    
    # List of task scheduled
    scheduled = {}
    #task executed
    executed = {}
    #First input  set into the main 
    dropboxdir=""
    #Second input  set into the main 
    apikey ="";
    #Third input set into the main. It is necessary to understand which file must be observed
    username=""
    
    def startup(self):
        metafiles = glob.glob(self.dropboxdir+"/neighbour/schedule/*.metaload")
        for i in range(0,len(metafiles)):
            self.update_task(metafiles[i]);
    
    #This method should start the task
    
    def taskexec(self, taskid):
        #switch on the device
        
        
        print >> sys.stderr,"executing"+taskid
        self.executed[taskid] = 'started'      
        #emoncms execute task --> remove
        h = httplib2.Http("/tmp/emoncms/.cache")
        h.request("http://localhost/emoncms/mas/execute.json?id="+taskid+"&apikey="+self.apikey, "GET")
        
   
    def on_created(self, ev):
            print self.username, ev.src_path
            if(ev.is_directory==False): 
                if(ev.src_path.endswith(".metaload") and (self.username in ev.src_path)):
                    print "updating"+ev.src_path
                    self.update_task(ev.src_path)
                    
    def on_modified(self, ev):
            print self.username, ev.src_path
            if(ev.is_directory==False): 
                if(ev.src_path.endswith(".metaload") and (self.username in ev.src_path)):
                    print "updating"+ev.src_path
                    self.update_task(ev.src_path)      
               
                
    #This is used to delete a task from the list of schedules
    def on_deleted(self, ev):
        if(ev.is_directory==False):
            taskid = os.path.splitext(os.path.basename(ev.src_path))[0]
            self.scheduled[taskid].cancel()          
            del self.scheduled[taskid]
            print taskid
       
    def update_task(self, filename):
        #set scheduled
        with open(filename) as f:
            d = dict(filter(None, csv.reader(f,  delimiter=' ', skipinitialspace=True))) 
            taskid = d['taskID']
            AST= d['AST']                 
            f.close()
    
        #update task status in emoncms
        h = httplib2.Http("/tmp/emoncms/.cache")
        minutes=np.int(AST)%3600
        minutes=minutes/60
        hours=np.int(AST)/3600 
             
        request = "{'status':1,'AST':'"+np.str(hours)+":"+np.str(minutes)+"'}"
        h.request("http://localhost/emoncms/mas/update.json?id="+taskid+"&json="+request+"&apikey="+self.apikey, "GET")
        sys.stderr.write("http://localhost/emoncms/mas/update.json?id="+taskid+"&json="+request+"&apikey="+self.apikey)
        #delay should be not 20 but AST-Time.time
        now = datetime.datetime.now()
        midnight = now.replace(hour=0, minute=0, second=0, microsecond=0)
        seconds = (now - midnight).seconds
        countdown = np.int(AST)-seconds
        #countdown =20
        if(countdown > 0):
            t=Timer(countdown, self.taskexec, [taskid])
            t.start()
            self.scheduled[taskid]=0
       
       
                 

    

class SchedD(Daemon):
    
 
      
   
    def __init__(self,pidfile):
        Daemon.__init__(self,pidfile, '/dev/null', '/dev/null', '/tmp/err.log')
        #Daemon.__init__(self,pidfile)
    def run(self):
        sys.stderr.write("dropboxdr:"+self.load_handler.dropboxdir)
        self.load_handler.startup()
        
        self.observer = Observer()
        
        logging.basicConfig(level=logging.INFO,
                    format='%(asctime)s - %(message)s',
                    datefmt='%Y-%m-%d %H:%M:%S')
        event_handler = LoggingEventHandler()
        self.observer.schedule(event_handler, self.load_handler.dropboxdir+"/neighbour/schedule", recursive=False)
        
        
        #self.observer.schedule(self.load_handler, self.load_handler.dropboxdir+"/neighbour/schedule", recursive=False)
        self.observer.start()
        self.observer.join()

if __name__ == "__main__":

    daemon = SchedD("/tmp/"+os.environ.get('USERNAME')+".pid")
    daemon.load_handler=LoadEventHandler()
    daemon.load_handler.dropboxdir = os.environ.get('DROPBOXDIR')   
    daemon.load_handler.apikey = os.environ.get('APIKEY')
    daemon.load_handler.username = os.environ.get('USERNAME')
    
    
    
    
    if(daemon.load_handler.dropboxdir ==None):
        print "DROPBOXDIR is not set"
    elif(daemon.load_handler.apikey ==None): 
        print "APIKEY is not set"
    elif(daemon.load_handler.username ==None): 
        print "USERNAME is not set"
               
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
    
