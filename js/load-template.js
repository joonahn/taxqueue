  //Synchronously load the templates
  var $templates = $('script[type="text/template"]');
  $templates.each(function() { 
    //Only get non-inline templates
    //No need to change code for production.
    if ($(this).attr("src")) { 
      $.ajax(
        $(this).attr("src"), 
        {
          async:false, 
          context:this, 
          success:function(data) { $(this).html(data); }
        }
      );
    }
  });
  //From this point onwards (in the same script, in the 
  //following scripts) all the "text/html" script tags 
  //will be loaded as if they were inline.
