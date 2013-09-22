<?php
require_once __DIR__.'/../vendor/autoload.php';

use GeoViQua\GeoLabel\XML\XMLProcessor as XMLProcessor;
use GeoViQua\GeoLabel\LML\LMLParser as LMLParser;
use GeoViQua\GeoLabel\SVG\SVGParser as SVGParser;
use GeoViQua\GeoLabel\Request\RequestProcessor as RequestProcessor;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\HttpFoundation\Request as Request;

$app = new Silex\Application();
// enable error messages
//$app['debug'] = true;
//error_reporting(E_ERROR);

$app->get('/', function() {
	return '<!DOCTYPE html>
			<html>
				<body>
					<p>GEO label API documentation is coming soon. Meanwhile, you can test the API by using this page: <a href="http://www.geolabel.net/geolabel.html">www.geolabel.net/geolabel.html</a>.</p>
				</body>
			</html>';
});

// ************************************  GET and POST to obtain GEO label representation  ****************************************

$app->get('/api/v1/geolabel', function(Request $request) {
	$metadataURL = $request->query->get('metadata');
	$feedbackURL = $request->query->get('feedback');
	if(empty($metadataURL) && empty($feedbackURL)){
		return new Response('<b>Bad request:</b> "metadata" and "feedback" query parameters are missing.', 400);
	}
	$xmlProcessor = new XMLProcessor();
	$metadataXML = null;
	$feedbackXML = null;
    $size = $request->query->get('size');
    $format = $request->query->get('format');
	// Check if metadata query parameter is set up and 
	// try to obtain XML document
	if(!empty($metadataURL)){
		// Decode URLs
		$metadataURL = urldecode($metadataURL);
		$metadataXML = $xmlProcessor->getXmlFromURL($metadataURL);
		if(empty($metadataXML)){
			return new Response('<b>Bad request:</b> could not retrieve an XML file from "metadata" URL.', 400);
		}
	}
	// Check if feedback query parameter is set up and 
	// try to obtain XML document
	if(!empty($feedbackURL)){
		// Decode URLs
		$feedbackURL = urldecode($feedbackURL);
		$feedbackXML = $xmlProcessor->getXmlFromURL($feedbackURL);
		if(empty($feedbackXML)){
			return new Response('<b>Bad request:</b> could not retrieve an XML file from "feedback" URL.', 400);
		}
	}	
	
	$svgParser = new SVGParser();
	// Construct an svg from URL files
	$svg = $svgParser->constructFromURLFiles($metadataXML, $metadataURL, $feedbackXML, $feedbackURL, $size);
	if(empty($svg)){
		return new Response('<b>Internal server error</b>: could not generate an SVG representation.', 500);
	}
	//return $svg;
	return new Response($svg, 200, array('Content-Type' => 'image/svg+xml'));
});

$app->post('/api/v1/geolabel', function (Request $request) {
	$producerFile = $request->files->get('metadata');
	$feedbackFile = $request->files->get('feedback');	
	if(empty($producerFile) && empty($feedbackFile)){
		return new Response('<b>Bad request:</b> "metadata" and "feedback" XML documents are missing.', 400);
	}
	$svgParser = new SVGParser();
	$producerXML = null;
	$feedbackXML = null;
	if(!empty($producerFile)){
		//libxml_use_internal_errors(true);
		$producerXML = new DOMDocument('1.0', 'utf-8');
		if(!$producerXML->load($producerFile)){
			return new Response('<b>Bad request:</b> invalid "metadata" XML document.', 400);
		}
	}
	if(!empty($feedbackFile)){
		//libxml_use_internal_errors(true);
		$feedbackXML = new DOMDocument('1.0', 'utf-8');
		if(!$feedbackXML->load($feedbackFile)){
			return new Response('<b>Bad request:</b> invalid "feedback" XML document.', 400);
		}
	}
    $producerURL = $request->get('metadata_url');
	$feedbackURL = $request->get('feedback_url');
    $size = $request->get('size');
	
	$svg = $svgParser->constructFromXMLs($producerXML, $producerURL, $feedbackXML, $feedbackURL, $size);
	if(empty($svg)){
		return new Response('<b>Internal server error</b>: could not generate an SVG representation.', 500);
	}
	return new Response($svg, 200, array('Content-Type' => 'image/svg+xml'));
});

// *********************************  GET and POST to obtain GEO label availability encodings  **********************************

$app->get('/api/v1/facets', function(Request $request) {
	$metadataURL = $request->query->get('metadata');
	$feedbackURL = $request->query->get('feedback');
	if(empty($metadataURL) && empty($feedbackURL)){
		return new Response('<b>Bad request:</b> "metadata" and "feedback" query parameters are missing.', 400);
	}
	$xmlProcessor = new XMLProcessor();
	$metadataXML = null;
	$feedbackXML = null;

	// Check if metadata query parameter is set up and 
	// try to obtain XML document
	if(!empty($metadataURL)){
		// Decode URLs
		$metadataURL = urldecode($metadataURL);
		$metadataXML = $xmlProcessor->getXmlFromURL($metadataURL);
		if(empty($metadataXML)){
			return new Response('<b>Bad request:</b> could not retrieve an XML file from "metadata" URL.', 400);
		}
	}
	// Check if feedback query parameter is set up and 
	// try to obtain XML document
	if(!empty($feedbackURL)){
		// Decode URLs
		$feedbackURL = urldecode($feedbackURL);
		$feedbackXML = $xmlProcessor->getXmlFromURL($feedbackURL);
		if(empty($feedbackXML)){
			return new Response('<b>Bad request:</b> could not retrieve an XML file from "feedback" URL.', 400);
		}
	}	
	
	$xmlProcessor = new xmlProcessor();

	$json = $xmlProcessor->getJsonDatasetSummary($metadataXML, $feedbackXML);
	if(empty($json)){
		return new Response('<b>Internal server error</b>: could not generate JSON response.', 500);
	}
	//return $json;
	return new Response($json, 200, array('Content-Type' => 'application/json'));
});

// *****************************************    INSPIRE Demo    *************************************************

$app->post('/api/v1/geolabel/demo', function(Request $request) {
	$producerFile = $request->files->get('metadata');
	$producerURL = $request->get('metadata_url');
	$geonetworkID = $request->get('geonetwork_id');
	if(empty($producerFile) && empty($producerURL) && empty($geonetworkID)){
		return new Response('<b>Bad request:</b> "metadata" XML document is missing.', 400);
	}
	if(!empty($geonetworkID)){
		$producerURL = 'http://uncertdata.aston.ac.uk:8080/geonetwork/srv/eng/xml_geoviqua?id=' . $geonetworkID .'&styleSheet=xml_iso19139.geoviqua.xsl';
	}
	$xmlProcessor = new XMLProcessor();
	$svgParser = new SVGParser();
	
	$producerXML = null;
	$feedbackXML = null;
	$feedbackURL = null;
	$svg = null;
	if(!empty($producerFile)){
		//libxml_use_internal_errors(true);
		$producerXML = new DOMDocument('1.0', 'utf-8');
		if(!$producerXML->load($producerFile)){
			return new Response('<b>Bad request:</b> invalid "metadata" XML document.', 400);
		}	
	}
	elseif(!empty($producerURL)){
		// Decode URLs
		$producerURL = urldecode($producerURL);
		$producerXML = $xmlProcessor->getXmlFromURL($producerURL);
		if(empty($producerXML)){
			if(!empty($geonetworkID)){
				return new Response('<b>Bad request:</b> could not retrieve an XML file from GeoNetwork. No such XML file ID.', 400);
			}
			else{
				return new Response('<b>Bad request:</b> could not retrieve an XML file from "metadata" URL.', 400);
			}
		}	
	}
	$targetCode = $request->get('target_code');
	$targetCodespace = $request->get('target_codespace');
	$size = $request->get('size');
	if(!empty($targetCode)){
		$feedbackURL = 'https://geoviqua.stcorp.nl/api/v1/feedback/items/search?target_code=' . $targetCode . '&target_codespace=' . $targetCodespace . '&view=full&format=xml';
		$feedbackXML = $xmlProcessor->getXmlFromURL($feedbackURL);
		if(empty($feedbackXML)){
			return new Response('<b>Bad request:</b> could not retrieve an XML file from feedback server.', 400);
		}
	}
	$svg = $svgParser->constructFromURLFiles($producerXML, $producerURL, $feedbackXML, $feedbackURL, $size);
	if(empty($svg)){
		return new Response('<b>Internal server error</b>: could not generate an SVG representation.', 500);
	}
	//return $svg;
	return new Response($svg, 200, array('Content-Type' => 'image/svg+xml'));
});


/*
// *****************************************      TEST URLS      *************************************************
// *********************************************   LML   *********************************************************
$app->post('/lml/generate_from_gvq_xml/', function (Request $request) {
	$lmlParser = new LMLParser();
	$gvqXML = null;

	$file = $request->files->get('gvq_xml');
	if (!empty($file)){
		//libxml_use_internal_errors(true);
		$gvqXML = new DOMDocument('1.0', 'utf-8');
		$gvqXML->load($file);
	}
	else{
		return new Response('GVQ XML file is missing.', 400);
	}
    $gvqURL = $request->get('gvq_url');
    $size = $request->get('size');
    $format = $request->get('format');
	$lml = $lmlParser->constructFromAggregatedXML($gvqXML, $gvqURL, $size, $format);
	if(empty($lml)){
		return new Response('Could not generate LML.', 500);
	}
	return new Response($lmlParser->getString($lml), 200, array('Content-Type' => 'text/xml'));
});

$app->post('/lml/generate_from_gvq_url/', function (Request $request) {
	$lmlParser = new LMLParser();
    $gvqURL = $request->get('gvq_url');
	if(empty($gvqURL)){
		return new Response('GVQ URL is missing.', 400);
	}
	
    $size = $request->get('size');
    $format = $request->get('format');
	
	$lml = $lmlParser->constructFromAggregatedURL($gvqURL, $size, $format);
	if(empty($lml)){
		return new Response('Could not generate LML.', 500);
	}
	return new Response($lmlParser->getString($lml), 200, array('Content-Type' => 'text/xml'));
});

$app->post('/lml/generate_from_producer_feedback_xmls/', function (Request $request) {
	$lmlParser = new LMLParser();
	$producerXML = null;
	$feedbackXML = null;
	$producerFile = $request->files->get('producer_xml');
	$feedbackFile = $request->files->get('feedback_xml');
	
	if(empty($producerFile) && empty($feedbackFile)){
		return new Response('Producer and Feedback XML files are missing.', 400);
	}

	if(!empty($producerFile)){
		//libxml_use_internal_errors(true);
		$producerXML = new DOMDocument('1.0', 'utf-8');
		$producerXML->load($producerFile);
	}
	if(!empty($feedbackFile)){
		//libxml_use_internal_errors(true);
		$feedbackXML = new DOMDocument('1.0', 'utf-8');
		$feedbackXML->load($feedbackFile);
	}
    $producerURL = $request->get('producer_url');
	$feedbackURL = $request->get('feedback_url');
    $size = $request->get('size');
    $format = $request->get('format');
	
	$lml = $lmlParser->constructFromXMLs($producerXML, $producerURL, $feedbackXML, $feedbackURL, $size, $format);
	if(empty($lml)){
		return new Response('Could not generate LML.', 500);
	}	
	return new Response($lmlParser->getString($lml), 200, array('Content-Type' => 'text/xml'));
});

$app->post('/lml/generate_from_producer_feedback_urls/', function (Request $request) {
	$lmlParser = new LMLParser();
    $producerURL = $request->get('producer_url');
    $feedbackURL = $request->get('feedback_url');
	if(empty($producerURL) && empty($feedbackURL)){
		return new Response('Producer and Feedback URLs are missing.', 400);
	}
    $size = $request->get('size');
    $format = $request->get('format');
	
	$lml = $lmlParser->constructFromURLs($producerURL, $feedbackURL, $size, $format);
	if(empty($lml)){
		return new Response('Could not generate LML.', 500);
	}
	return new Response($lmlParser->getString($lml), 200, array('Content-Type' => 'text/xml'));
});

$app->post('/lml/generate_from_availability_encoding/', function (Request $request) {

	return "[TO DO]";
	
});

$app->post('/lml/generate_from_producer_xml_feedback_id/', function (Request $request) {

	return "[TO DO]";
	
});

$app->post('/lml/generate_from_producer_url_feedback_id/', function (Request $request) {

	return "[TO DO]";
	
});

$app->post('/lml/generate_from_feedback_id/', function (Request $request) {

	return "[TO DO]";
	
});

// *********************************************   SVG   *********************************************************
$app->post('/svg/generate_from_gvq_xml/', function (Request $request) {
	$svgParser = new SVGParser();
	$gvqXML = null;

	$file = $request->files->get('gvq_xml');
	if (!empty($file)){
		//libxml_use_internal_errors(true);
		$gvqXML = new DOMDocument('1.0', 'utf-8');
		$gvqXML->load($file);
	}
	else{
		return new Response('GVQ XML file is missing.', 400);
	}
    $gvqURL = $request->get('gvq_url');
    $size = $request->get('size');
	
	$svg = $svgParser->constructFromAggregatedXML($gvqXML, $gvqURL, $size);
	if(empty($svg)){
		return new Response('Could not generate SVG.', 500);
	}
	return new Response($svg, 200, array('Content-Type' => 'image/svg+xml'));
});

$app->post('/svg/generate_from_gvq_url/', function (Request $request) {
	$svgParser = new SVGParser();
    $gvqURL = $request->get('gvq_url');
	if(empty($gvqURL)){
		return new Response('GVQ URL is missing.', 400);
	}
    $size = $request->get('size');
	
	$svg = $svgParser->constructFromAggregatedURL($gvqURL, $size);
	if(empty($svg)){
		return new Response('Could not generate SVG.', 500);
	}
	return new Response($svg, 200, array('Content-Type' => 'image/svg+xml'));
});

$app->post('/svg/generate_from_producer_feedback_xmls/', function (Request $request) {
	$svgParser = new SVGParser();
	$producerXML = null;
	$feedbackXML = null;
	$producerFile = $request->files->get('producer_xml');
	$feedbackFile = $request->files->get('feedback_xml');
	
	if(empty($producerFile) && empty($feedbackFile)){
		return new Response('Producer and Feedback XML files are missing.', 400);
	}

	if(!empty($producerFile)){
		//libxml_use_internal_errors(true);
		$producerXML = new DOMDocument('1.0', 'utf-8');
		$producerXML->load($producerFile);
	}
	if(!empty($feedbackFile)){
		//libxml_use_internal_errors(true);
		$feedbackXML = new DOMDocument('1.0', 'utf-8');
		$feedbackXML->load($feedbackFile);
	}
    $producerURL = $request->get('producer_url');
	$feedbackURL = $request->get('feedback_url');
    $size = $request->get('size');
	
	$svg = $svgParser->constructFromXMLs($producerXML, $producerURL, $feedbackXML, $feedbackURL, $size);
	if(empty($svg)){
		return new Response('Could not generate SVG.', 500);
	}
	return new Response($svg, 200, array('Content-Type' => 'image/svg+xml'));
});

$app->post('/svg/generate_from_producer_feedback_urls/', function (Request $request) {
	$svgParser = new SVGParser();
    $producerURL = $request->get('producer_url');
    $feedbackURL = $request->get('feedback_url');
	if(empty($producerURL) && empty($feedbackURL)){
		return new Response('Producer and Feedback URLs are missing.', 400);
	}
	
    $size = $request->get('size');
    $format = $request->get('format');
	
	$svg = $svgParser->constructFromURLs($producerURL, $feedbackURL, $size);
	if(empty($svg)){
		return new Response('Could not generate SVG.', 500);
	}
	return new Response($svg, 200, array('Content-Type' => 'image/svg+xml'));	
});

$app->post('/svg/generate_from_availability_encoding/', function (Request $request) {

	return "[TO DO]";
	
});

$app->post('/svg/generate_from_producer_xml_feedback_id/', function (Request $request) {

	return "[TO DO]";
	
});

$app->post('/svg/generate_from_producer_url_feedback_id/', function (Request $request) {

	return "[TO DO]";
	
});

$app->post('/svg/generate_from_feedback_id/', function (Request $request) {

	return "[TO DO]";
	
});
*/

$app->run();

?>