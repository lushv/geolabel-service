<html>
<body>

<h1>Generate SVG from Producer and Feedback metadata XML files</h1>

<form action="http://api.geolabel.local/api/v1/geolabel" method="post" enctype="multipart/form-data">
	<hr>
	<br />
	<label for="metadata">Select a <u>producer</u> XML document:</label>
	<input type="file" name="metadata" id="metadata" />
	<br />
	<label for="metadata_url">Enter URL for the producer XML document (optional)</label>
	<input type="text" name="metadata_url" id="metadata_url" />
	<br />
	<br />
	<label for="feedback">Select a <u>feedback</u> XML document:</label>
	<input type="file" name="feedback" id="feedback" />
	<br />
	<label for="feedback_url">Enter URL for feedback XML document (optional)</label>
	<input type="text" name="feedback_url" id="feedback_url" />
	<br />
	<br />
	<label for="size">Enter GEO label size:   </label><input type="text" name="size" id="size" >
	<br />
	<br />
	<hr>
	<br />
	<input type="submit" name="submit" value="Submit" />
</form>
</body>
</html