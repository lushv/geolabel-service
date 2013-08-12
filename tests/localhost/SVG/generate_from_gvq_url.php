<html>
<body>

<h1>Generate SVG from a GVQ metadata URL</h1>

<form action="http://api.geolabel.local/svg/generate_from_gvq_url/" method="post" enctype="multipart/form-data">
	<h3>Send XML metadata records to see the GEO label representation.<h3>
	<hr>
	<br />
	<label for="gvq_url">Enter URL for the aggregated XML document</label>
	<input type="text" name="gvq_url" id="gvq_url" />
	<br />
	<label for="size">Enter GEO label size to be recorded in the lml:   </label><input type="text" name="size" id="size" >
	<br />
	<br />
	<label for="format">Enter GEO label format to be recorded in the lml:   </label><input type="text" name="format" id="format" >
	<br />
	<br />
	<hr>
	<br />
	<input type="submit" name="submit" value="Submit" />
</form>
</body>
</html>