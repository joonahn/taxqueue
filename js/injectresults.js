function loadTable() {
    $.get("./php/results.php", function(data, status){	
    	$('#result-window').html(data);
    	$('#result-window > table').addClass('table');
    });
}

$(document).ready(function() {

	loadTable();

});