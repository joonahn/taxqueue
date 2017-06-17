# taxqueue
Taxonomy assignment with task queue

# Front End

## Files consist of main page
- `index.html` : Main page HTML
- `uploader.html` : Fine uploader HTML
- `js/filetransfer.js` : Enqueue file to the queue
- `js/injectresults.js` : Add result table to the site

# Back End

## RabbitMQ Message Format
- JSON
- keys: 

```
{	randomfolder, 
	count, 
	taskname, 
	primerseq, 
	checkFwd, 
	checkRev, 
	taxalg, 
	rdpdb, 
	conflevel, 
	trlen, 
	"#"			}
```

## Files touching queue.txt/results.txt
- `enqueue.php` :	(w)/()
- `dequeue.php` :	(r,w)/(w)
- `vallidate.php` :	(r)
- `results.php` :	(r)
- `deletefolder.php` : ()/(w)

## queue.txt File format
- tab separated string
- {$taskname} <tab> queued

## results.txt File format
- tab separated string
- {$taskname} <tab> succeeded <tab> {$archivePath} <tab> succeeded `<tab> ID`
- {$taskname} <tab> failed <tab> - <tab> {$reason} `<tab> ID`