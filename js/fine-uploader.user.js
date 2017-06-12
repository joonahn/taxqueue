var uploader = new qq.FineUploader({
	element: document.getElementById("upload-area"),
	request: {
		endpoint: './php/endpoint.php'
	},
	callbacks: {onComplete: function (id, name, res, obj) {
		var filename = res['uuid'] + '/' + res['uploadName'];
		dataArray.push(filename);
		console.log('filename:' + filename);
		// Show the upload holder
		$('#uploaded-holder').show();
		$('#upload-button span').html("files are ready to be uploaded");
		$('#upload-button').css({'display' : 'block'});
	}}
});