<?php
require_once __DIR__.'/../vendor/autoload.php';

use GeoViQua\GeoLabel\XML\XMLProcessor as XMLProcessor;
use GeoViQua\GeoLabel\LML\LMLParser as LMLParser;
use GeoViQua\GeoLabel\SVG\SVGParser as SVGParser;
use GeoViQua\GeoLabel\Drilldown\Drilldown as Drilldown;

use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\HttpFoundation\Request as Request;

$app = new Silex\Application();
// enable error messages
//$app['debug'] = true;
error_reporting(E_ERROR);

$app->get('/', function() {
	return '<!DOCTYPE html>
			<html>
				<body>
					<p>GEO label API documentation is coming soon. Meanwhile, you can test the API by using this page: <a href="http://www.geolabel.net/geolabel.html">www.geolabel.net/geolabel.html</a>.</p>
					<p>For more information about the GEO label please visit <a href="http://www.geolabel.info">www.geolabel.info</a>.</p>
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

// ************************************  GET and POST for drilldown functionality  ****************************************

$app->get('/api/v1/drilldown', function(Request $request) {
	$metadataURL = $request->query->get('metadata');
	$feedbackURL = $request->query->get('feedback');
	$facet = $request->query->get('facet');
	
	$metadataURL = $request->query->get('metadata');
	$feedbackURL = $request->query->get('feedback');
	
	if(empty($facet)){
		return new Response('<b>Bad request:</b> "facet" query parameter is missing.', 400);
	}
	
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
	
	$stylesheet_url = null;
	// get xsl location
	switch ($facet) {
		case "producer_profile":
			$stylesheet_url = "stylesheets/GVQ_ProducerProfile.xsl";
			break;
		case "producer_comments":
			$stylesheet_url = "stylesheets/GVQ_ProducerComments.xsl";
			break;
		case "lineage":
			$stylesheet_url = "stylesheets/GVQ_Lineage.xsl";
			break;
		case "standards_complaince":
			$stylesheet_url = "stylesheets/GVQ_StandardsCompliance.xsl";
			break;
		case "quality":
			$stylesheet_url = "stylesheets/GVQ_Quality.xsl";
			break;
		case "user_feedback":
			$stylesheet_url = "stylesheets/GVQ_UserFeedback.xsl";
			break;
		case "expert_review":
			$stylesheet_url = "stylesheets/GVQ_ExpertReviews.xsl";
			break;
		case "citations":
			$stylesheet_url = "stylesheets/GVQ_Citations.xsl";
			break;
		default:
			break;
	}
	
	if(empty($stylesheet_url)){
		return new Response('<b>Bad request:</b> invalid "facet" parameter value supplied.', 400);
	}
	
	// create a DOM document and load the XSL stylesheet
	$xsl = new DomDocument;
	$xsl->load($stylesheet_url);
		
	$drilldown = new Drilldown();
	$drilldownResp = $drilldown -> getDrilldown($metadataXML, $feedbackXML, $xsl);
	
	return new Response($drilldownResp, 200);
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

$app->run();

?>