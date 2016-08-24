#!/usr/bin/env python
# Email Spider/Crawler/Harvester
# Made by BlueMelon
# Project-Melon.com
import urllib,threading,re,time
 
r = re.compile('(?<=href\=\"mailto:).*?@.*?.[\w]{0,3}(?=\")') # Mails
r1 = re.compile('(?<=href\=\").*?(?=\")') # Links
 
count = int(0)
 
class Crawl(threading.Thread):
	def __init__(self,url):
		self.url = url
		threading.Thread.__init__ ( self )
 
	def run(self):
		try:
			global count
			source = urllib.urlopen(self.url).read() # Get page source
			mails = r.findall(source) # Get all eMails 
			mails = list(set(mails)) # Remove dupes if found
			log = open('log.txt','a')
			for i in mails: # For every eMail is found mails, append it to log 
				if re.match("^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,4}$", i) != None: # Check for a valid Email 
					if (i+'\n') not in (open('log.txt','r').readlines()): # If it does not exist in file
						print 'Saved: ',i
						log.write(i+'\n') #Append it
						count += 1
			log.close()
			urls = r1.findall(source) # Find all urls on that page
			for url in urls:
					Crawl(url).start() # Start a crawl for every url found
 
		except: #Error
			pass 
 
 
Crawl("http://www.ianr.unl.edu/internet/mailto.html").start() # Starting URL
 
while True:
	time.sleep(1)
	print 'Threads: ',threading.activeCount(), 'Saved: ', count