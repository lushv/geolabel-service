$(document).ready(function() {
	// Clear all Query Constraints fields
	$("#loader_1").hide();
	$("#loader_2").hide();
	$("#loader_3").hide();
});

// ************************************************  TAB 1 FUNCTIONS *************************************************
$(function() {
  $("#submit_btn_1").click(function() {
	// First of all clear all the previous results
	$("#geolabel_1").empty();
	
	var http = location.protocol;
	var slashes = http.concat("//");
	var host = slashes.concat(window.location.hostname) + "/api/v1/geolabel?";
	
	// validate and process form here
	var metadata_url = $("#metadata_url_1").val();
	var feedback_url = $("#feedback_url_1").val();
	var target_code = $("#target_code_1").val();
	var target_codespace = $("#target_codespace_1").val();
	var size = $("#size_1").val();
	
	// encode URLs
	metadata_url = encodeURIComponent(metadata_url);
	feedback_url = encodeURIComponent(feedback_url);
	if(target_code != ''){
		feedback_url = encodeURIComponent('https://geoviqua.stcorp.nl/api/v1/feedback/collections/?format=xml&target_code=' + target_code + '&target_codespace=' + target_codespace);
	}
	
	dataString = 'metadata=' + metadata_url + '&feedback=' + feedback_url + '&size=' + size;
	
	// Make a get request using jQuery ajax (see Example 1 for alternative implementation)
    $.ajax({
		  type: "GET",
		  url: host,
		  dataType: "xml",
		  mimeType: "image/svg+xml",
		  data: dataString,
		  beforeSend: function() {
			$('#loader_1').show();
		  },
		  complete: function(){
			$('#loader_1').hide();
		  },
		  success: function(data){
			var xml = $(data).find('svg');
			$("#geolabel_1").append(xml);
		  },
		  error:function(){
			$("#geolabel_1").html("An error occured.");
		  },
		}); // -- End of Ajax request to get dataset details
	}) // -- End of on label click function
}); // -- End of $(function() { ... });

// ************************************************  TAB 2 FUNCTIONS *************************************************
$(function() {
  $("#submit_btn_2").click(function() {
	// First of all clear all the previous results
	$("#geolabel_2").empty();
	
	var http = location.protocol;
	var slashes = http.concat("//");
	var host = slashes.concat(window.location.hostname) + "/api/v1/geolabel";
	
	var onProgressHandler = function() {
		$('#loader_2').show();
	}
	var onLoadHandler = function() {
		$('#loader_2').hide();
	}
	var onErrorHandler = function() {
		$('#loader_2').hide();
	}
	
    var formData = new FormData($('#geolabel_form_2')[0]);
	
	xhr = new XMLHttpRequest();
	xhr.open("POST", host, false);
	// Following line is just to be on the safe side; not needed if your server delivers SVG with correct MIME type
	xhr.overrideMimeType("image/svg+xml");
	xhr.upload.addEventListener('progress', onProgressHandler, false);
	xhr.upload.addEventListener('load', onLoadHandler, false);
	xhr.upload.addEventListener('error', onErrorHandler, false);
	xhr.send(formData);
	document.getElementById("geolabel_2").appendChild(xhr.responseXML.documentElement);
		
	}) // -- End of on label click function
}); // -- End of $(function() { ... });

// ************************************************  TAB 3 FUNCTIONS *************************************************
$(function() {
  $("#submit_btn_3").click(function() {
	// First of all clear all the previous results
	$("#geolabel_3").empty();
	
	var http = location.protocol;
	var slashes = http.concat("//");
	var host = slashes.concat(window.location.hostname) + "/api/v1/geolabel?";
	
	// validate and process form here
	var metadata_url = $("#metadata_url_3").val();
	var target_code = $("#target_code_3").val();
	var target_codespace = $("#target_codespace_3").val();
	var size = $("#size_3").val();
	
	// encode URLs
	metadata_url = encodeURIComponent(metadata_url);
	var feedback_url = "";
	if(target_code != ''){
		feedback_url = encodeURIComponent('https://geoviqua.stcorp.nl/api/v1/feedback/collections/?format=xml&target_code=' + target_code + '&target_codespace=' + target_codespace);
	}
	
	dataString = 'metadata=' + metadata_url + '&feedback=' + feedback_url + '&size=' + size;
	
	// Make a get request using jQuery ajax (see Example 1 for alternative implementation)
    $.ajax({
		  type: "GET",
		  url: host,
		  dataType: "xml",
		  mimeType: "image/svg+xml",
		  data: dataString,
		  beforeSend: function() {
			$('#loader_3').show();
		  },
		  complete: function(){
			$('#loader_3').hide();
		  },
		  success: function(data){
			var xml = $(data).find('svg');
			$("#geolabel_3").append(xml);
		  },
		  error:function(){
			$("#geolabel_3").html("An error occured.");
		  },
		}); // -- End of Ajax request to get dataset details
	}) // -- End of on label click function
}); // -- End of $(function() { ... });

/* -----------------------------------   EXAMPLE 1: non-ajax GEO label request   ---------------------------------

	// Code extracted from:
	// http://stackoverflow.com/questions/14068031/embedding-external-svg-in-html-for-javascript-manipulation
	
	xhr = new XMLHttpRequest();
	xhr.open("GET","http://api.geolabel.local/api/v1/geolabel?" + dataString, false);
	// Following line is just to be on the safe side; not needed if your server delivers SVG with correct MIME type
	xhr.overrideMimeType("image/svg+xml");
	xhr.send("");
	document.getElementById("geolabel_1_div").appendChild(xhr.responseXML.documentElement);
	
*/

/*
send xml document
var xmlDocument = [create xml document];
var xmlRequest = $.ajax({
url: "page.php",
processData: false,
data: xmlDocument
});
xmlRequest.done( handleResponse );


*/




	