<html>
<body>

<h1>Generate LML from Producer and Feedback metadata URLs</h1>

<form action="http://geoviqua.dev.52north.org:8088/geolabel/v4/web/lml/generate_from_producer_feedback_urls/" method="post" enctype="multipart/form-data">
	<hr>
	<br />
	<label for="producer_url">Enter URL for the producer XML document</label>
	<input type="text" name="producer_url" id="producer_url" />
	<br />
	<br />
	<label for="feedback_url">Enter URL for feedback XML document</label>
	<input type="text" name="feedback_url" id="feedback_url" />
	<br/>
	<br />
	<label for="size">Enter GEO label size to be recorded in the lml:   </label><input type="text" name="size" id="size" >
	<br />
	<br />
	<label for="format">Enter GEO label format to be recorded in the lml:   </label><input type="text" name="format" id="format" >
	<br/>
	<hr>
	<br />
	<input type="submit" name="submit" value="Submit" />
</form>
</body>
</html