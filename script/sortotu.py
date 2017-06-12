#!/usr/bin/python

import sys
import os.path

filename = sys.argv[1]
directory = os.path.dirname(filename)
outfilename = os.path.join(directory, 'sotus.txt')

with open(filename, 'r') as f:
	with open(outfilename, 'w') as wf:
		line = f.readline()
		headers = line.split('\t')
		first = headers.pop(0)
		sheaders = sorted(headers)
		mapping = map((lambda x: headers.index(x)),sheaders)
		sheaders.insert(0, first)
		wf.write("\t".join(sheaders))
		while True:
			line = f.readline()
			if not line: break
			tmpdata = line.split('\t')
			first = tmpdata.pop(0)
			printstr = first
			for idx in range(0, len(tmpdata)):
				printstr = printstr + "\t" + tmpdata[mapping[idx]] 
			wf.write(printstr)

