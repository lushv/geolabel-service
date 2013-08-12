<html>
<body>

<h1>Generate LML from a GVQ metadata URL</h1>

<form action="http://geoviqua.dev.52north.org:8088/geolabel/v4/web/lml/generate_from_gvq_url/" method="post" enctype="multipart/form-data">
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