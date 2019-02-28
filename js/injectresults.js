function loadTable() {
    $.get("asdf:8888/list", function(data, status){	
		$('#result-window > tbody').html("");
		for (const job of data) {
			var tr = document.createElement('tr')
			for (const col in ["task_name", "job_status", "result_path", "created_time"]) {
				var td = document.createElement('td');
				var text = document.createTextNode(job[col]);
				td.appendChild(text);
				tr.appendChild(td);
			}
			$('#result-window > tbody').appendChild(tr);
		}
    });
}

$(document).ready(function() {
	loadTable();
	setInterval(loadTable, 30000);
});
