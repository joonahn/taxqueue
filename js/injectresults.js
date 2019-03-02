function loadTable() {
    $.get("http://asdf:8787/list", function(data, status){	
		$('#result-window tbody').html("");
		for (const job of data) {
			console.log(job);
			var tr = document.createElement('tr')
			for (const col of ["task_name", "job_state", "result_path", "created_time"]) {
				var td = document.createElement('td');
				var text = document.createTextNode(job[col]);
				td.appendChild(text);
				tr.appendChild(td);
			}
			$('#result-window tbody')[0].appendChild(tr);
		}
    });
}

$(document).ready(function() {
	console.log('before loadTable');
	loadTable();
	console.log('after loadTable');
	setInterval(loadTable, 30000);
});
