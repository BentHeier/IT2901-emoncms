import scipy.interpolate as inter
import numpy as np
#import pylab as plt
import time
from numpy import linspace, poly1d
from numpy.lib.function_base import append
from StringIO import StringIO 
import os 
import sys 

path='/var/www/emoncms/profiles/'

if(len(sys.argv)!=2):
    sys.exit()

profname=sys.argv[1]
csv = np.genfromtxt (path+profname+'.raw', delimiter=",")


time_data = csv[:,0]
ydata = csv[:,1]


isort = np.argsort(time_data)
xx = np.arange(time_data[isort[0]],time_data[isort[len(isort)-1]],(time_data[isort[len(isort)-1]]-time_data[isort[0]])/len(time_data))


#plt.plot(time_data[isort], ydata[isort],'b.', label="poly3")

ncoffs = np.polyfit(time_data[isort], ydata[isort],3)
#print ncoffs

updated=np.poly1d(ncoffs)


outfile = open(path+profname+'.prof', 'w')
coffs=np.append( time_data[isort[len(isort)-1]],ncoffs)

#this has done since the actors cannot handle polynomial/spline representation yet
np.savetxt(outfile,ncoffs,fmt='%.0f %.4f')
outfile.close()


#this has done since the actors cannot handle polynomial/spline representation yet
profile = np.column_stack((xx,updated(xx)))
outfile = open(path+profname+'.prof_raw', 'w')
np.savetxt(outfile,profile,fmt='%.0f %.4f')
outfile.close()





#os.remove('/var/www/emoncms/profiles/'+profname+'.raw')



#plt.plot(xx,updated(xx),'r-', label="poly3")

#plt.show()

