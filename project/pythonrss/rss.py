#!/usr/bin/python

# number of workers in parallel
# 
size_pool_workers = 50

import time
import feedparser                        # for RSS
import MySQLdb	                         # for mysql
import ImageFile                         # for image
from eventlet import db_pool, coros, httpc, util  # for io/async co-routines
from BeautifulSoup import BeautifulSoup  # For processing HTML
 
# replace socket with a cooperative coroutine socket because httpc
# uses httplib, which uses socket.  Removing this serializes the http
# requests, because the standard socket is blocking.
util.wrap_socket_with_coroutine_socket()

urls_feed = [] # the urls to grab (feeds)
waiters = []   # the current pool of waiting greenlets

# 
# create a pool of connections to mysql
#
def startDB():
   global connector
   connector = db_pool.DatabaseConnector(MySQLdb, 
	{'localhost': {'user': 'feedcrawler', 'passwd': 'slurpem'}})

#
# create a pool of workers
#
def startWorkers():
   global pool_workers
   pool_workers = coros.CoroutinePool(max_size=size_pool_workers) # create the pool of workers

#
# +open master conf 
# +load the rss feeds to monitor
#
def loadFeeds():
   global urls_feed	
   try:
     file = open("./master.feeds.conf", 'rU')
   except:
     print "Cannot find master.feeds.conf"
     exit(0)
   try:
     urls_feed = [x.strip() for x in file]
   except:
     print "Cannot parse master.feeds.conf"
     exit(0)
   file.close() 
#

#
# fetch an Img, the best one according the criteria
#
def fetchImg(url):
    
    try:
      p = ImageFile.Parser()		
      data = httpc.get(url)	
      print "\tgot img %s" % url.strip()
      p.feed(data)
      if p.image:
	if (p.image.size[0] > 100 and p.image.size[1]>100):
          print p.image.size
    except:
      0
 


#
# fetch an WebPage
#
def fetchWebPage(url):

    data = httpc.get(url)	
    print "\tgot url %s" % url
    soup = BeautifulSoup(data)
    imgs = soup.findAll('img')

    for img in imgs:
       waiters.append(pool_workers.execute(fetchImg, img['src']))	
       # post the request for images
#


#
# a single worker
#   + download url
#   + parse rss/atom
#   + store in a db, using the db pool
# 
def fetch(url):

    global connector

    data = httpc.get(url)
    rss = feedparser.parse(data)		

    pool_db = connector.get('localhost', 'feedcrawler')
    conn = pool_db.get()	  # get the connection from the pool 
    curs = conn.cursor()

    print "title %s" % (rss['feed']['title'])

    for entry in rss['entries']:

        waiters.append(pool_workers.execute(fetchWebPage, entry['link']))
	try :
          #
          # posturl is primary key so will be inserted just when is new
	  #
	  curs.execute("insert into postings (posturl, title, descr) values (%s, %s, %s)", ( entry['link'], entry['title'], entry['description']))	

# if not already inserted
#         print "\t setting a greenlet on wp: %s" % entry['link']		
	except:
          #print "Duplicate %s" % entry['link']
	  0

#
# + spawn all the greenlets of computation
#
def start():

  loadFeeds()    # load the feeds to monitor
  startDB()      # pool of db connections
  startWorkers() # pool of workers
  
  for url in urls_feed:
     print "url: %s" % url
     waiters.append(pool_workers.execute(fetch, url))
 
# wait for all the coroutines to come back before exiting the process
  for waiter in waiters:
     waiter.wait()

#
# let the ball roll
#
start()