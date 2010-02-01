import sys
import os
import apachelogs
import json
import re
import urllib
import time
import calendar

"""
Reads apache access log and generates intermediary analysis output on the format:

hourperiod,tag,loads,rejects,previous_attempts,country
349350,79,1,0,43,us
349350,79,1,0,24,ca
349350,79,1,0,25,ca
349350,79,1,0,0,NULL

In cases where beacon req doesn't provide country, that field will be the string 'NULL'.

USAGE:
python make_analysis_format.py [access.log1] [access.log2]... 
"""

oldbrowser_pattern = re.compile('(\S+)\s\/beacon\?events=(\S+)\&\S+\s(\S+)')
newbrowser_pattern = re.compile('(\S+)\s\/beacon\?beacon=(\S+)\&\S+\s(\S+)')
event_pattern = re.compile('(\D)(\d+)pl(\S*)')

def process_logfile(logfile):
	reader = apachelogs.ApacheLogFile(logfile)
	for line in reader:
		numslots, events, country = get_numslots_events_country_from_request(line.request_line)
		if events <> None:
			hourperiod = get_hourperiod_from_apache_time(line.time)
			for event in events.split(','):
				write_stats(hourperiod, event, country)
	reader.close()
			
def write_stats(hourperiod, event, country):
	m = re.match(event_pattern, event)
	if country == None:
		country = 'NULL'
	if m <> None:
		loads = 0
		rejects = 0
		if m.group(1) == 'l':
			loads = 1
		elif m.group(1) == 'r':
			rejects = 1
		print str(hourperiod) + ',' + m.group(2) + ',' + str(loads) + ',' + str(rejects) + ',' + m.group(3) + ',' + country

def get_numslots_events_country_from_request(req):
	numslots = None
	events = None
	country = None
	unq_req = urllib.unquote(req)
	m = re.match(oldbrowser_pattern, unq_req)
	if m <> None:
		events = m.group(2)
	m = re.match(newbrowser_pattern, unq_req)
	if m <> None:
		try:
			numslots = int(json.loads(m.group(2))['numSlots'])
			events = json.loads(m.group(2))['events']
			country = json.loads(m.group(2))['country']
		except Exception, inst:
			print >> sys.stderr, 'Error parsing event:', str(inst), unq_req
	return numslots, events, country

def get_hourperiod_from_apache_time(apache_time):
	timestamp = calendar.timegm(time.strptime(apache_time[:-6], "%d/%b/%Y:%H:%M:%S"))
	return timestamp / 3600

def main(argv):
	for logfile in argv[1:]:
		process_logfile(logfile)
	
if __name__ == '__main__':
	main(sys.argv)