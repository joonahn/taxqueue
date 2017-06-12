var dataArray = [];

$(document).ready(function() {

	// Get all of the data URIs and put them in an array
	var taxAssignOption = {};

	var ProgressBar = {
		setPercent : function(val) {
			$('#loading-bar .loading-color').css({'width' : val +'%'});
		},
		setHTML : function(htmltext) {
			$('#loading-content').html(htmltext);
		},
		concatHTML : function(htmltext) {
			$('#loading-content').html($('#loading-content').html() + htmltext);
		},
		hide: function() {
			$('#loading').css({'display' : 'none'});
		},
		show: function() {
			$("#loading").show();
		}
	};

	$('#checkFwd').prop('checked', true);
	

	// Reset Forms
	function restartFiles() {
	
		// This is to set the loading bar back to its default state
		$('#loading-bar .loading-color').css({'width' : '0%'});
		$('#loading').css({'display' : 'none'});
		$('#loading-content').html(' ');
		// --------------------------------------------------------
		
		// We need to remove all the images and li elements as
		// appropriate. We'll also make the upload button disappear
		
		$('#upload-button').hide();
		$('#extra-files #file-list li').remove();
		$('#extra-files').hide();
		$('#uploaded-holder').hide();
		$('#taskname').val("");
	
		// And finally, empty the array/set z to -40
		dataArray = cancelUpload(dataArray);
		uploader.reset();
		return false;
	}

	function uploadFiles(toBeUploadedArray, taxAssignOptions) {
		$("#loading").show();
		$('#uploaded-holder').hide();
		$('#upload-button').hide();
		$('#result-link').hide();
		$('#result-link span').html("");
		ProgressBar.setHTML('Uploading files...');
		ProgressBar.setPercent(0);

		var rfolder = makeid();
		var rfolderJSON = {'randomfolder': rfolder};
		taxAssignOptions['randomfolder'] = rfolder;

		$.post ('php/reorder.php', 
			combineJSON(makeOTUTarget(toBeUploadedArray),rfolderJSON), 
			function(data) {
				ProgressBar.setPercent(100);
				ProgressBar.setHTML('Upload complete!');
				dataArray = [];
				var uploadFinishedArray = changeFolder(toBeUploadedArray, rfolder);
				setTimeout(function() {enqueueTask(uploadFinishedArray, taxAssignOptions)}, 500);
				// DEBUG!!
				// setTimeout(restartFiles(), 500);
			});
	}

	function enqueueTask(uploadFinishedArray, taxAssignOptions) {
		ProgressBar.setPercent(0);
		ProgressBar.setHTML('Enqueuing task');

		if (0 == uploadFinishedArray.length)
		{
			ProgressBar.setHTML('upload failed!');
			setTimeout(restartFiles(), 500);
			return;
		}

		$.post('php/enqueue.php', 
			combineJSON(makeOTUTarget(uploadFinishedArray),taxAssignOptions), 
			function(data) {
			
			// Change the bar to represent how much has loaded
			ProgressBar.setPercent(100);

			// Show the upload is complete
			ProgressBar.setHTML('Enqueuing task complete!');

			// CALL
			// setTimeout(restartFiles(), 500);
			setTimeout(location.reload(), 500);
		});
	}

	function validateForm() {
		var taxAssignOptions = {};
		if ((!($('#checkFwd').is(":checked")))
			 && (!($('#checkRev').is(":checked"))))
		{
			alert('One of Fwd seq, Rev seq check boxes should be checked.');
			return {};
		}

		if ($('#taskname').val() == "")
		{
			alert('Please fill taskname');
			return {};
		}

	    var x = $("#optionForm").serializeArray();
	    $.each(x, function(i, field){
	        taxAssignOptions[field.name]=field.value;
	    });
		return taxAssignOptions;
	}

	function changeFolder(arr, folder) {
		var resultarr = [];
		$.each(arr, function(index, value) {
			var filename = value.replace(/^.*[\\\/]/, '');
			resultarr.push(folder + '/' + filename);
		});
		return resultarr;
	}

	function combineJSON(obj1, obj2) {
		var result = {};
		for(var key in obj1) result[key] = obj1[key];
		for(var key in obj2) result[key] = obj2[key];	
		return result;	
	}

	function makeOTUTarget(filelist) {
		var OTUTarget = {};
		$.each(filelist, function(index, value) {
			OTUTarget[index.toString()] = value;
		});
		OTUTarget["count"] = filelist.length;
		return OTUTarget;
	}

	function printMSG(str) {
		var innerHTMLtext = $('#result-link span').html();
			$('#result-link').show();
			if (innerHTMLtext != "")
			{
				$('#result-link span').html(innerHTMLtext + '<br>' + str);
			} else
			{
				$('#result-link span').html(str);
			}
	}

	function makeid() {
		var text = "";
		var possible = "abcdefghijklmnopqrstuvwxyz0123456789";

		for( var i=0; i < 12; i++ )
		    text += possible.charAt(Math.floor(Math.random() * possible.length));

		return text;		
	}

	function cancelUpload(arr) {
		$.each(arr, function(index, value) {
			$.post('php/cancelUpload.php', {'filename':value}, 
				function (data) {

				});
		});
		return [];
	}

	// Upload
	$('#upload-button .upload').click(function() {
		$('#result-link span').html("");
		var taxAssignOptions = validateForm();
		if (Object.keys(taxAssignOptions).length == 0)
		{
			printMSG("You choose wrong options");
			return restartFiles();
		}
		else
		{
			var taskname_data = {};
			taskname_data['taskname'] = $('#taskname').val();

			// Validate taskname with ajax request
			$.post('./php/validate.php', taskname_data)
			  .done(function(data) {
			  	if (data == "Good")
			  	{
					return uploadFiles(dataArray, taxAssignOptions);
			  	}
				else
				{
					alert("The taskname already exists!!");
					return restartFiles();
				}
			});
		}
	});
	
	
	// For the file list
	$('#extra-files .number').toggle(function() {
		$('#file-list').show();
	}, function() {
		$('#file-list').hide();
	});
	
	$('#dropped-files #upload-button .delete').click(restartFiles);
	
	// Append the localstorage the the uploaded files section
	if(window.localStorage.length > 0) {
		$('#uploaded-files').show();
		for (var t = 0; t < window.localStorage.length; t++) {
			var key = window.localStorage.key(t);
			var value = window.localStorage[key];
			// Append the list items
			if(value != undefined || value != '') {
				$('#uploaded-files').append(value);
			}
		}
	} else {
		$('#uploaded-files').hide();
	}
});