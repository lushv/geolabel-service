<?php
/**
* LMLParser class provides functionality to generate an LML GEO label representation based on XML file.
*
* PHP version 5
*
* @author		Original Author Victoria Lush
* @version		1.0
*/
namespace GeoViQua\GeoLabel\LML;
use GeoViQua\GeoLabel\XML\XMLProcessor as XMLProcessor;
use \DOMDocument;
use \DOMXpath;

class LMLParser{
	// Private class variables
	private $gvqNameSpace = 'xmlns:gvq="http://www.geoviqua.org/QualityInformationModel/3.1"';
	private $lml;
	private $xmlProcessor;
	//private $availabilityArray;
	//private $hoveroverTextArray;
	//private $drilldownURLsArray;
	
	
	/* Constructor
	*/
	public function __construct(){
		$this->lml = new LML();
		$this->xmlProcessor = new XMLProcessor();
	}
	
	/* Function constructFromAggregatedXML
	 * Constructs a full LML representation of the GEO label from an aggregated XML file
	 * This function takes the URL of the XML file as it is provided. It assumes that the 
	 * URL is not the URL of the actual XML document but a link to some related documentation.
	 * 
	 * @param $aggregatedXML DomDocument an XML document to process
	 * @param $aggregatedURL String URL of the document
	 * @param $size String size
	 * @param $format String format
	 * @return DomDocument lml document if generated successfully
	 */
	public function constructFromAggregatedXML($aggregatedXML, $aggregatedURL, $size, $format){
		if(empty($aggregatedXML)){
			return null;
		}
		// Get all data from the XML document into 3 arrays
		$availabilityArray = $this->xmlProcessor->getAvailabilityEncodings($aggregatedXML);
		$hoveroverTextArray = $this->xmlProcessor->getLmlHoveroverText($aggregatedXML);
		$drilldownURLsArray = $this->xmlProcessor->getStaticURLs($aggregatedURL, $aggregatedURL);
		
		// 1. Set source documents:
		$this->setSourceDocument($aggregatedXML, $aggregatedURL, 'glb:aggregatedDocument');
		// 2. Set GEO label parameters
		$this->setParameters($size, $format);
		// 3. Set facets elements
		$this->setFacets($availabilityArray, $hoveroverTextArray, $drilldownURLsArray);

		return $this->lml->getLmlDom();
	}
	
	
	/* Function constructFromAggregatedURL
	 * Constructs a full LML representation of the GEO label from an aggregated XML file
	 * This function will convert the URL of the aggregated file into a drill-down link
	 * 
	 * @param $aggregatedXML DomDocument an XML document to process
	 * @param $aggregatedURL String URL of the document
	 * @param $size String size
	 * @param $format String format
	 * @return DomDocument lml document if generated successfully
	 */
	public function constructFromAggregatedURL($aggregatedURL, $size, $format){
		if(empty($aggregatedURL)){
			return null;
		}
		
		$aggregatedXML = $this->xmlProcessor->getXmlFromURL($aggregatedURL);
		
		// Get all data from the XML document into 3 arrays
		$availabilityArray = $this->xmlProcessor->getAvailabilityEncodings($aggregatedXML);
		$hoveroverTextArray = $this->xmlProcessor->getLmlHoveroverText($aggregatedXML);
		$drilldownURLsArray = $this->xmlProcessor->getDrilldownURLs($aggregatedURL, $aggregatedURL);
		
		// 1. Set source documents:
		$this->setSourceDocument($aggregatedXML, $aggregatedURL, 'glb:aggregatedDocument');
		// 2. Set GEO label parameters
		$this->setParameters($size, $format);
		// 3. Set facets elements
		$this->setFacets($availabilityArray, $hoveroverTextArray, $drilldownURLsArray);
		
		return $this->lml->getLmlDom();
	}
	
	/* Function constructFromXMLs
	 * Constructs a full LML representation of the GEO label from an aggregated XML file
	 * 
	 * @param $aggregatedXML DomDocument an XML document to process
	 * @param $aggregatedURL String URL of the document
	 * @param $size String size
	 * @param $format String format
	 * @return DomDocument lml document if generated successfully
	 */
	public function constructFromXMLs($producerXML, $producerURL, $feedbackXML, $feedbackURL, $size, $format){
		if(empty($producerXML) && empty($feedbackXML)){
			return null;
		}
		// Join two documents
		$gvqXML = null;
		if(!empty($producerXML) && !empty($feedbackXML)){
			$gvqXML = $this->xmlProcessor->joinXMLDoms($producerXML, $feedbackXML);
		}
		elseif(!empty($producerXML)){
			$gvqXML = $producerXML;
		}
		elseif(!empty($feedbackXML)){
			$gvqXML = $feedbackXML;
		}		
		// Get all data from the XML document into 3 arrays
		$availabilityArray = $this->xmlProcessor->getAvailabilityEncodings($gvqXML);
		$hoveroverTextArray = $this->xmlProcessor->getLmlHoveroverText($gvqXML);
		$drilldownURLsArray = $this->xmlProcessor->getDrilldownURLs($producerURL, $feedbackURL);
		
		// 1. Set source documents:
		$this->setSourceDocument($producerXML, $producerURL, 'glb:producerDocument');
		$this->setSourceDocument($feedbackXML, $feedbackURL, 'glb:feedbackDocument');
		// 2. Set GEO label parameters
		$this->setParameters($size, $format);
		// 3. Set facets elements
		$this->setFacets($availabilityArray, $hoveroverTextArray, $drilldownURLsArray);
		
		return $this->lml->getLmlDom();
	}
	
	/* Function constructFromURLs
	 * Constructs a full LML representation of the GEO label from two URLs
	 * 
	 * @param $aggregatedXML DomDocument an XML document to process
	 * @param $aggregatedURL String URL of the document
	 * @param $size String size
	 * @param $format String format
	 * @return DomDocument lml document if generated successfully
	 */
	public function constructFromURLs($producerURL, $feedbackURL, $size, $format){
		if(empty($producerURL) && empty($feedbackURL)){
			return null;
		}
		$gvqXML = null;
		$producerXML = $this->xmlProcessor->getXmlFromURL($producerURL);
		$feedbackXML = $this->xmlProcessor->getXmlFromURL($feedbackURL);	
	
		// Join two documents
		$gvqXML = null;
		if(!empty($producerXML) && !empty($feedbackXML)){
			$gvqXML = $this->xmlProcessor->joinXMLDoms($producerXML, $feedbackXML);
		}
		elseif(!empty($producerXML)){
			$gvqXML = $producerXML;
		}
		elseif(!empty($feedbackXML)){
			$gvqXML = $feedbackXML;
		}		
		// Get all data from the XML document into 3 arrays
		$availabilityArray = $this->xmlProcessor->getAvailabilityEncodings($gvqXML);
		$hoveroverTextArray = $this->xmlProcessor->getLmlHoveroverText($gvqXML);
		$drilldownURLsArray = $this->xmlProcessor->getDrilldownURLs($producerURL, $feedbackURL);
		
		// 1. Set source documents:
		$this->setSourceDocument($producerXML, $producerURL, 'glb:producerDocument');
		$this->setSourceDocument($feedbackXML, $feedbackURL, 'glb:feedbackDocument');
		// 2. Set GEO label parameters
		$this->setParameters($size, $format);
		// 3. Set facets elements
		$this->setFacets($availabilityArray, $hoveroverTextArray, $drilldownURLsArray);
		
		return $this->lml->getLmlDom();
	}
	
	/* Function setSourceDocument
	 * Adds a source document to the LML
	 * 
	 * @param $xml DomDocument an XML document to process
	 * @param $url String URL of the document
	 * @param $qualifiedName qualified name (e.g. glb:producerDocument) of the source document element
	 * @return boolean true if the element has been set successfully
	 */
	public function setSourceDocument($xml, $url, $qualifiedName){
		$childArray = array(
								'glb:documentID' => $this->xmlProcessor->getXMLFileIdentifier($xml),
								'glb:documentURL' => $url,);
		$parentElement = $this->lml->createElement('http://geolabel.info', $qualifiedName, $childArray);
		$this->lml->appendElement('http://geolabel.info', 'sourceDocuments', $parentElement);
		
		return true;
	}
	
	/* Function setParameters
	 * Adds parameter elements to the LML
	 * 
	 * @param $size String GEO label size
	 * @param $format String GEO label format
	 * @return boolean true if the element has been set successfully
	 */
	public function setParameters($size, $format){
		if(empty($size)){
			$size = '200';
		}
		if(empty($format)){
			$format = 'svg';
		}
		$sizeNode = $this->lml->createNode('http://geolabel.info', 'glb:size', $size);
		$formatNode = $this->lml->createNode('http://geolabel.info', 'glb:format', $format);
		$this->lml->appendElement('http://geolabel.info', 'labelParameters', $sizeNode);
		$this->lml->appendElement('http://geolabel.info', 'labelParameters', $formatNode);
		
		return true;
	}
	
	/* Function setFacets
	 * Adds facet elements to LML
	 *
	 * @return boolean true if the elements have been set successfully
	 */
	public function setFacets($availabilityArray, $hoveroverTextArray, $drilldownURLsArray){
		// 1. Set producer profile elements
		$childArray = array(
						'glb:availability' => $availabilityArray['producerProfile'],
						'glb:producerOrganisationName' => $hoveroverTextArray['organisationName'],
						'glb:drilldownURL' => $drilldownURLsArray['producerProfile'],);
		$parentElement = $this->lml->createElement('http://geolabel.info', 'glb:producerProfile', $childArray);
		$this->lml->appendElement('http://geolabel.info', 'facets', $parentElement);
		
		// 2. Set lineage elements
		$childArray = array(
				'glb:availability' => $availabilityArray['lineage'],
				'glb:totalProcessSteps' => $hoveroverTextArray['processStepCount'],
				'glb:drilldownURL' => $drilldownURLsArray['lineage'],);
		$parentElement = $this->lml->createElement('http://geolabel.info', 'glb:lineage', $childArray);
		$this->lml->appendElement('http://geolabel.info', 'facets', $parentElement);
		
		// 3. Set producer comments elements
		$childArray = array(
				'glb:availability' => $availabilityArray['produerComments'],
				'glb:commentsText' => $hoveroverTextArray['supplementalInformation'],
				'glb:drilldownURL' => $drilldownURLsArray['produerComments'],);
		$parentElement = $this->lml->createElement('http://geolabel.info', 'glb:producerComments', $childArray);
		$this->lml->appendElement('http://geolabel.info', 'facets', $parentElement);
		
		// 4. Set standards compliance elements
		$childArray = array(
				'glb:availability' => $availabilityArray['standardsComplaince'],
				'glb:metadataStandardName' => $hoveroverTextArray['standardName'],
				'glb:metadataStandardVersion' => $hoveroverTextArray['standardVersion'],
				'glb:drilldownURL' => $drilldownURLsArray['standardsComplaince'],);
		$parentElement = $this->lml->createElement('http://geolabel.info', 'glb:standardsCompliance', $childArray);
		$this->lml->appendElement('http://geolabel.info', 'facets', $parentElement);
		
		// 5. Set quality information elements
		$childArray = array(
				'glb:availability' => $availabilityArray['qualityInformation'],
				'glb:scopeLevel' => $hoveroverTextArray['scopeLevel'],
				'glb:drilldownURL' => $drilldownURLsArray['qualityInformation'],);
		$parentElement = $this->lml->createElement('http://geolabel.info', 'glb:qualityInformation', $childArray);
		$this->lml->appendElement('http://geolabel.info', 'facets', $parentElement);
		
		// 6. User feedback elements
		$childArray = array(
				'glb:availability' => $availabilityArray['userFeedback'],
				'glb:totalFeedbacks' => $hoveroverTextArray['feedbacksCount'],
				'glb:totalRatings' => $hoveroverTextArray['ratingsCount'],
				'glb:averageRating' => $hoveroverTextArray['feedbacksAverageRating'],
				'glb:drilldownURL' => $drilldownURLsArray['userFeedback'],);
		$parentElement = $this->lml->createElement('http://geolabel.info', 'glb:userFeedback', $childArray);
		$this->lml->appendElement('http://geolabel.info', 'facets', $parentElement);
		
		// 7. Expert reveiws elements
		$childArray = array(
				'glb:availability' => $availabilityArray['expertReview'],
				'glb:totalReviews' => $hoveroverTextArray['expertReviewsCount'],
				'glb:totalRatings' => $hoveroverTextArray['expertRatingsCount'],
				'glb:averageRating' => $hoveroverTextArray['expertAverageRating'],
				'glb:drilldownURL' => $drilldownURLsArray['expertReview'],);
		$parentElement = $this->lml->createElement('http://geolabel.info', 'glb:expertReview', $childArray);
		$this->lml->appendElement('http://geolabel.info', 'facets', $parentElement);
		
		// 8. Citations elements
		$childArray = array(
				'glb:availability' => $availabilityArray['citations'],
				'glb:totalCitations' => $hoveroverTextArray['citationsCount'],
				'glb:drilldownURL' => $drilldownURLsArray['citations'],);
		$parentElement = $this->lml->createElement('http://geolabel.info', 'glb:citationInformation', $childArray);
		$this->lml->appendElement('http://geolabel.info', 'facets', $parentElement);
		
		return true;
	}
	
	/* Function getLmlDom
	 * @return DomDocument representation of the GEO label LML.
	 */
	public function getLmlDom(){
		return $this->lml->getLmlDom();
	}
	
	/* Function getLmlString
	 * @return String representation of the LML
	 */
	public function getLmlString(){
		return $this->lml->getLmlString();
	}
	
	/* Function getLmlString
	 * @param $xml DomDocument to validate
	 * @return String representation of the DomDocument XML or null if document is not valid.
	 */
	public function getString($xml){
		return preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~','$1', $xml->saveXML());
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------
	 * Function isValidXml
	 * Takes XML string and returns a boolean result where valid XML returns true
	 *
	 * @param string $xml string of an XML document
	 * @return boolean result where valid XML returns true
	 */
	public function isValidXml($xml){
		libxml_use_internal_errors( true );
		$doc = new DOMDocument('1.0', 'utf-8');
		$doc->loadXML($xml);
		$errors = libxml_get_errors();
		return empty($errors);
	}
	
	/* Function constructLML
	 * Constructs a full LML representation of the GEO label from an aggregated XML file
	 * 
	 * @param $aggregatedXML DomDocument an XML document to process
	 * @param $aggregatedURL String URL of the document
	 * @param $size String size
	 * @param $format String format
	 * @return boolean true if the element has been set successfully
	 */
	public function constructLML($producerXML, $producerURL, $feedbackXML, $feedbackURL, $size, $format){
		$gvqXML = new DomDocument();
		$gvqURL = null;
		$feedbackURL = null;
		if(!empty($producerXML) && !empty($feedbackXML)){
			$gvqXML = $this->xmlProcessor->joinXMLDoms($producerXML, $feedbackXML);
			$gvqURL = $producerURL;
			$feedbackURL = $feedbackURL;
		}
		else{
			$gvqXML = $producerXML;
			$gvqURL = $producerURL;
			$feedbackURL = $producerURL;
		}
		// Get all data from the XML document into 3 arrays
		$availabilityArray = $this->xmlProcessor->getAvailabilityEncodings($gvqXML);
		$hoveroverTextArray = $this->xmlProcessor->getLmlHoveroverText($gvqXML);
		$drilldownURLsArray = $this->xmlProcessor->getDrilldownURLs($gvqURL, $feedbackURL);
		
		// 1. Set source documents:
		$this->setSourceDocument($aggregatedXML, $aggregatedURL, 'glb:aggregatedDocument');
		// 2. Set GEO label parameters
		$this->setParameters($size, $format);
		// 3. Set facets elements
		$this->setFacets($availabilityArray, $hoveroverTextArray, $drilldownURLsArray);
	}	
	
}
// ********************************************************************************************************************************
	// Class testing
	/*
	$lmlParserClass = new LMLParser();
	$tmpXML;
	$dom;
	if (file_exists('test.xml')) {
		$tmpXML = simplexml_load_file('test.xml');
		$dom_sxe = dom_import_simplexml($tmpXML);
		if ($dom_sxe) {
			$dom = new DOMDocument('1.0');
			$dom_sxe = $dom->importNode($dom_sxe, true);
			$dom_sxe = $dom->appendChild($dom_sxe);
		}
	} else {
		exit('Failed to open test.xml.');
	}
	//$lmlParserClass->constructFromXMLs($dom, "PRODUCER URL", $dom, "FEEDBACK URL", "200", "svg");
	$lmlParserClass->constructFromAggregatedXML($dom, "FEEDBACK URL", "200", "svg");
	header('Content-Type:  text/xml', true, 200);
	echo $lmlParserClass->getLmlString();

	$string_1 = '<root><node></node></root>';
	$string_2 = '<root><node></node></root>';
	$tmpXML_1 = new DOMDocument('1.0', 'UTF-8');
	$tmpXML_2 = new DOMDocument('1.0', 'UTF-8');
	$tmpXML_1->loadXML($string_1);
	$tmpXML_2->loadXML($string_2);
	
	echo $lmlParserClass->joinXMLDoms($tmpXML_1, $tmpXML_2)->saveXML();
	*/
?>