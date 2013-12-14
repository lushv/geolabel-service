<?php
/**
* XMLProcessor class provides functionality for processing producer, feedback or aggregated XML documents.
* This class stores all the GEO label related XPath expressions.
*
* PHP version 5
*
* @author		Original Author Victoria Lush
* @version		1.0
*/
namespace GeoViQua\GeoLabel\Drilldown;
use GeoViQua\GeoLabel\XML\XMLProcessor as XMLProcessor;
use \DOMDocument;
use \XsltProcessor;
use \DOMXpath;

class Drilldown{

	/* Constructor
	*/
	public function __construct(){

	}
	
	public function getDrilldown($producerXML, $feedbackXML, $xsl){
		// check if metadata and feedback provided
		// join metadata documents of both provided
		// get facet xsl
	
		// Join two documents
		$gvqXML = null;
		if(!empty($producerXML) && !empty($feedbackXML)){
			$xmlProcessor = new XMLProcessor();
			$gvqXML = $xmlProcessor->joinXMLDoms($this->updateNamespaces($producerXML), $this->updateNamespaces($feedbackXML));
		}
		elseif(!empty($producerXML)){
			$gvqXML = $this->updateNamespaces($producerXML);
		}
		elseif(!empty($feedbackXML)){
			$gvqXML = $this->updateNamespaces($feedbackXML);
		}

		// Load xsl document
		$xsltProcessor = new XsltProcessor();

		// import the XSL styelsheet into the XSLT process
		$xsltProcessor->importStylesheet($xsl);		
		// transform the XML into HTML using the XSL file
		if($html = $xsltProcessor->transformToXML($gvqXML)) {
			return $html;
		} 
		else {
			// If no document is supplied, return an empty styled page by default
			$dom = new DOMDocument('1.0', 'UTF-8');
			return $xsltProcessor->transformToXML($dom);
			//return 'XSL transformation failed.';
		}
	}
	
	public function updateNamespaces($xml_doc){
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
		return $xml_doc;
	}
}
?>