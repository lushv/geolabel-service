<?php
// $producer_url = '../GVQ/4.0/example_documents/GLC_2000_GVQ_raw.xml';
//$producer_url = 'http://schemas.geoviqua.org/GVQ/4.0/example_documents/GLC_2000_GVQ_raw.xml';
//$stylesheet_url = '../GVQ/4.0/stylesheets/GVQ_Citations.xsl';
//$stylesheet_url = 'http://schemas.geoviqua.org/GVQ/4.0/stylesheets/GVQ_Citations.xsl';
$producer_url = $_REQUEST['doc'];
$stylesheet_url = $_REQUEST['xsl'];

$producer_url = urldecode($producer_url);
$stylesheet_url = urldecode($stylesheet_url);

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $producer_url);    // get the url contents
$md= curl_exec($ch); // execute curl request

curl_setopt($ch, CURLOPT_URL, $stylesheet_url);    // get the url contents
$ss= curl_exec($ch); // execute curl request
curl_close($ch);

// create a DOM document and load the XML data
$xml_doc = new DomDocument;
$xml_doc->formatOutput = true;
$xml_doc->load($producer_url);
if(!empty($xml_doc)){
	// Modify document namespaces
	$xmlRoot = $xml_doc->documentElement;
	$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gvq', 'http://www.geoviqua.org/QualityInformationModel/4.0');
	$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:updated19115', 'http://www.geoviqua.org/19115_updates');
	$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gmx', 'http://www.isotc211.org/2005/gmx');
	$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xlink', 'http://www.w3.org/1999/xlink');
	$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
	$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gmd', 'http://www.isotc211.org/2005/gmd');
	$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gco', 'http://www.isotc211.org/2005/gco');
	$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gml', 'http://www.opengis.net/gml/3.2');
	$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gmd19157', 'http://www.geoviqua.org/gmd19157');
	$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:un', 'http://www.uncertml.org/2.0');
	$xmlRoot->setAttribute('xsi:schemaLocation', 'http://www.isotc211.org/2005/gmx http://schemas.opengis.net/iso/19139/20070417/gmx/gmx.xsd 
	http://www.geoviqua.org/QualityInformationModel/4.0 http://schemas.geoviqua.org/GVQ/3.1/GeoViQua_PQM_UQM.xsd
	http://www.uncertml.org/2.0 http://www.uncertml.org/uncertml.xsd');
	$xmlRoot->setAttribute('id', 'dataset_MD');
}

$xp = new XsltProcessor();
  // create a DOM document and load the XSL stylesheet
  $xsl = new DomDocument;
  $xsl->load($stylesheet_url);
  
  // import the XSL styelsheet into the XSLT process
  $xp->importStylesheet($xsl);
// transform the XML into HTML using the XSL file
  if ($html = $xp->transformToXML($xml_doc)) {
      echo $html;
  } else {
      trigger_error('XSL transformation failed.', E_USER_ERROR);
  } // if 
?>