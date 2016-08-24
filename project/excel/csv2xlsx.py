from xlsxwriter.workbook import Workbook
from csv import reader
import sys
import string
import random
import os


def id_generator(size=6, chars=string.ascii_uppercase + string.digits):
	return ''.join(random.choice(chars) for x in range(size))


def main():
	arr = [[cell.decode('cp1251') for cell in row] for row in reader(sys.stdin)]
	spreadsheetFilename = id_generator() + '.xlsx'
	workbook = Workbook(spreadsheetFilename)
	worksheet = workbook.add_worksheet()
	# Some data we want to write to the worksheet.
	colidx = 0
	rowidx = 0 
	# Iterate over the data and write it out row by row.
	for row in arr:
	    worksheet.write_row(rowidx, colidx, (row[:]))
	    rowidx += 1
	# Write a total using a formula.
	workbook.close()
	mysh = open(spreadsheetFilename,'rb')
	sys.stdout.write(mysh.read())
	mysh.close()
	os.remove(spreadsheetFilename)

if __name__ == '__main__':
	sys.stdout.write(b"abba")
	# main()