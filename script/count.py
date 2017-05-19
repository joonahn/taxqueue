#!/usr/bin/python

import sys
import os.path

taxfilename = sys.argv[2]
otufilename = sys.argv[1]
directory = os.path.dirname(taxfilename)
taxassn = os.path.dirname(taxfilename)
otus = os.path.dirname(otufilename)
outfilename = []
outfilename.append(os.path.join(directory, '1.txt'))
outfilename.append(os.path.join(directory, '2.txt'))
outfilename.append(os.path.join(directory, '3.txt'))
outfilename.append(os.path.join(directory, '4.txt'))
outfilename.append(os.path.join(directory, '5.txt'))
outfilename.append(os.path.join(directory, '6.txt'))
outfilename.append(os.path.join(directory, '7.txt'))

# linear
otulist = []
filelist = []

# 7-level linear
taxlist = []
otuTaxMapList = []
fileTaxCntDictList = []
taxTotalCntMapList = []

# Generate otu list
with open(otufilename, 'r') as f:
	# remove firstline(table header)
	line = f.readline()
	headerlist = line.split('\t')
	headerlist.pop(0)
	headerlist = map((lambda x: x.strip()), headerlist)
	filelist.extend(headerlist)
	while True:
		line = f.readline()
		if not line: break
		if len(line.split('\t')) > 1:
			otulist.append(line.split('\t')[0])

# Generate genus-otu mapping
for i in range(0, 7):
	taxlist.append(['unknown'])
	fileTaxCntDictList.append(reduce(lambda a,b: a.update({b:{"unknown":0}}) or a, filelist, {}))
	taxTotalCntMapList.append({"unknown":0})
	otuTaxMapList.append(reduce(lambda a,b: a.update({b:"unknown"}) or a, otulist, {}))

with open(taxfilename, 'r') as f:
	while True:
		line = f.readline()
		if not line: break
		otuID = line.split('\t')[0].split(';')[0]
		taxs = line.split('\t')[1].split(';')
		for i in range(0, 7):
			if i < len(taxs):
				if not (taxs[i] in taxlist[i]):
					taxlist[i].append(taxs[i])
				taxTotalCntMapList[i][taxs[i]] = 0
				otuTaxMapList[i][otuID] = taxs[i]
			else:
				pass

# Generate filestr-(bac-cnt dict) dict
with open(otufilename, 'r') as f:
	# remove firstline(table header)
	f.readline()
	while True:
		line = f.readline()
		if not line: break
		cntdata = line.split('\t')
		cntdata.pop(0)
		otuID = line.split('\t')[0]
		for i in range(0,7):
			#fileTaxCntDictList
			#taxTotalCntMapList
			if len(cntdata) != len(filelist):
				print "otu filecount and datacount mismatch!"
				exit()
			else:
				for j in range(0, len(filelist)):
					if not otuTaxMapList[i][otuID] in fileTaxCntDictList[i][filelist[j]]:
						fileTaxCntDictList[i][filelist[j]][otuTaxMapList[i][otuID]] = 0

					tmp = fileTaxCntDictList[i][filelist[j]][otuTaxMapList[i][otuID]]
					fileTaxCntDictList[i][filelist[j]][otuTaxMapList[i][otuID]] = tmp + int(cntdata[j])
					tmp = taxTotalCntMapList[i][otuTaxMapList[i][otuID]]
					taxTotalCntMapList[i][otuTaxMapList[i][otuID]] = tmp + int(cntdata[j])


for i in range(0,7):
	with open (outfilename[i], 'w') as f:
		#print header
		f.write("FileID\t" + "\t".join(taxlist[i]) + "\n")
		for filename in filelist:
			outstr = filename + "\t"
			for tax in taxlist[i]:
				outstr = outstr + str(fileTaxCntDictList[i][filename][tax]) + "\t"
			f.write(outstr + "\n")
		f.write("\n\n\n")
		#print header
		f.write("FileID\t" + "\t".join(taxlist[i]) + "\n")
		for filename in filelist:
			outstr = filename + "\t"
			for tax in taxlist[i]:
				totalcnt = taxTotalCntMapList[i][tax]
				thiscnt = fileTaxCntDictList[i][filename][tax]
				try:
					ratio = (thiscnt/float(totalcnt))
					outstr = outstr + str(ratio) + "\t"
				except ZeroDivisionError as e:
					outstr = outstr + "div0" + "\t"
					
			f.write(outstr + "\n")









#/Species	/Genus	/Family	/Order	/Class	/Phylum (animal)	/Kingdom /
#/7			/6		/5		/4		/3		/2					/1