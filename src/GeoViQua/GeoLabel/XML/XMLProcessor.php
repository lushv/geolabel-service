<?php
/**
* XMLProcessor class provides functionality for processing producer, feedback or aggregated XML documents.
* This class loads and stores all the GEO label related XPath expressions.
*
* PHP version 5
*
* @author		Original Author Victoria Lush
* @version		1.0
*/
namespace GeoViQua\GeoLabel\XML;
use \DOMDocument;
use \DOMXpath;

class XMLProcessor{
	private $gvqNameSpace = 'xmlns:gvq="http://www.geoviqua.org/QualityInformationModel/3.1"';

	// XPath expressions for dataset IDs:
	private $fileIdentifierXPath = '//*[local-name()=\'fileIdentifier\']/*[local-name()=\'CharacterString\']';

	// ***********************************************   AVAILABILITY XPATHS   **************************************************
	private $producer_profile_xpath;
	private $producer_comments_xpath;
	private $lineage_xpath;
	private $standards_xpath;
	private $quality_xpath;
	private $feedback_xpath;
	private $review_xpath;
	private $citations_xpath;
	
	// *************************************************   HOVER-OVER XPATHS   **************************************************
	private $organisation_name_xpath;
	
	private $supplemental_information_xpath;
	private $known_problems_xpath;
	
	private $process_step_count_xpath;
	
	private $standard_name_xpath;
	private $standard_version_xpath;
	
	private $scope_level_xpath;
	
	private $feedbacks_count_xpath;
	private $ratings_count_xpath;
	private $average_rating_xpath;
	
	private $reviews_count_xpath;
	private $reviews_ratings_count_xpath;
	private $reviews_average_rating_xpath;
	
	private $citations_count_xpath;

	// ***********************************************   HOVER-OVER TEMPLATES   **************************************************
	private $producer_profile_facet_title;
	private $producer_comments_facet_title;
	private $lineage_facet_title;
	private $standards_complaince_facet_title;
	private $quality_facet_title;
	private $user_feedback_facet_title;
	private $expert_review_facet_title;
	private $citations_facet_title;
	
	private $producer_profile_text_template;
	private $producer_comments_text_template;
	private $lineage_text_template;
	private $standards_complaince_text_template;
	private $quality_information_text_template;
	private $user_feedback_text_template;
	private $expert_review_text_template;
	private $citations_text_template;
	
	// ************************************************   DRILLDOWN TEMPLATES   **************************************************
	private $producer_profile_drilldown_url;
	private $producer_comments_drilldown_url;
	private $lineage_drilldown_url;
	private $standards_drilldown_url;
	private $quality_information_drilldown_url;
	private $user_feedback_drilldown_url;
	private $expert_review_drilldown_url;
	private $citations_drilldown_url;
	
	private $base_drilldown_url;
	
	/* Constructor
	*/
	public function __construct($app){

		// ***************************************   INITIALISE AVAILABILITY XPATHS   ********************************************
		$this->producer_profile_xpath = $app['transformationDescription']['facetDescriptions'][0]['producerProfile']["availabilityPath"];
		$this->producer_comments_xpath = $app['transformationDescription']['facetDescriptions'][1]["producerComments"]["availabilityPath"];
		$this->lineage_xpath = $app['transformationDescription']['facetDescriptions'][2]["lineage"]["availabilityPath"];
		$this->standards_xpath = $app['transformationDescription']['facetDescriptions'][3]["standardsCompliance"]["availabilityPath"];
		$this->quality_xpath = $app['transformationDescription']['facetDescriptions'][4]["qualityInformation"]["availabilityPath"];
		$this->feedback_xpath = $app['transformationDescription']['facetDescriptions'][5]["userFeedback"]["availabilityPath"];
		$this->review_xpath = $app['transformationDescription']['facetDescriptions'][6]["expertReview"]["availabilityPath"];
		$this->citations_xpath = $app['transformationDescription']['facetDescriptions'][7]["citations"]["availabilityPath"];
	
		// **************************************   INITIALISE HOVER-OVER XPATHS   ***********************************************
		$this->organisation_name_xpath = $app['transformationDescription']['facetDescriptions'][0]['producerProfile']['hoverover']['text']['organizationNamePath'];
		
		$this->supplemental_information_xpath = $app['transformationDescription']['facetDescriptions'][1]['producerComments']['hoverover']['text']['supplementalInformation'];
		$this->known_problems_xpath = $app['transformationDescription']['facetDescriptions'][1]['producerComments']['hoverover']['text']['knownProblemsPath'];
		
		$this->process_step_count_xpath = $app['transformationDescription']['facetDescriptions'][2]['lineage']['hoverover']['text']['processStepCountPath'];
		
		$this->standard_name_xpath = $app['transformationDescription']['facetDescriptions'][3]['standardsCompliance']['hoverover']['text']['standardNamePath'];
		$this->standard_version_xpath = $app['transformationDescription']['facetDescriptions'][3]['standardsCompliance']['hoverover']['text']['standardVersion'];

		$this->scope_level_xpath = $app['transformationDescription']['facetDescriptions'][4]['qualityInformation']['hoverover']['text']['scopeLevelPath'];
		
		$this->feedbacks_count_xpath = $app['transformationDescription']['facetDescriptions'][5]['userFeedback']['hoverover']['text']['feedbacksCountPath'];
		$this->ratings_count_xpath = $app['transformationDescription']['facetDescriptions'][5]['userFeedback']['hoverover']['text']['averageRatingPath'];
		$this->average_rating_xpath = $app['transformationDescription']['facetDescriptions'][5]['userFeedback']['hoverover']['text']['ratingsCountPath'];

		$this->reviews_count_xpath = $app['transformationDescription']['facetDescriptions'][6]['expertReview']['hoverover']['text']['reviewsCountPath'];
		$this->reviews_ratings_count_xpath = $app['transformationDescription']['facetDescriptions'][6]['expertReview']['hoverover']['text']['averageRatingPath'];
		$this->reviews_average_rating_xpath = $app['transformationDescription']['facetDescriptions'][6]['expertReview']['hoverover']['text']['ratingsCountPath'];
		
		$this->citations_count_xpath = $app['transformationDescription']['facetDescriptions'][7]['citations']['hoverover']['text']['citationsCountPath'];

		// *************************************   INITIALISE HOVER-OVER TEMPLATES   *********************************************
		$this->producer_profile_facet_title = $app['transformationDescription']['facetDescriptions'][0]['producerProfile']['hoverover']['facetName'];
		$this->producer_comments_facet_title = $app['transformationDescription']['facetDescriptions'][1]["producerComments"]['hoverover']['facetName'];
		$this->lineage_facet_title = $app['transformationDescription']['facetDescriptions'][2]["lineage"]['hoverover']['facetName'];
		$this->standards_complaince_facet_title = $app['transformationDescription']['facetDescriptions'][3]["standardsCompliance"]['hoverover']['facetName'];
		$this->quality_facet_title = $app['transformationDescription']['facetDescriptions'][4]["qualityInformation"]['hoverover']['facetName'];
		$this->user_feedback_facet_title = $app['transformationDescription']['facetDescriptions'][5]["userFeedback"]['hoverover']['facetName'];
		$this->expert_review_facet_title = $app['transformationDescription']['facetDescriptions'][6]["expertReview"]['hoverover']['facetName'];
		$this->citations_facet_title = $app['transformationDescription']['facetDescriptions'][7]["citations"]['hoverover']['facetName'];
		
		$this->producer_profile_text_template = $app['transformationDescription']['facetDescriptions'][0]['producerProfile']['hoverover']['template'];
		$this->producer_comments_text_template = $app['transformationDescription']['facetDescriptions'][1]["producerComments"]['hoverover']['template'];
		$this->lineage_text_template = $app['transformationDescription']['facetDescriptions'][2]["lineage"]['hoverover']['template'];
		$this->standards_complaince_text_template = $app['transformationDescription']['facetDescriptions'][3]["standardsCompliance"]['hoverover']['template'];
		$this->quality_information_text_template = $app['transformationDescription']['facetDescriptions'][4]["qualityInformation"]['hoverover']['template'];
		$this->user_feedback_text_template = $app['transformationDescription']['facetDescriptions'][5]["userFeedback"]['hoverover']['template'];
		$this->expert_review_text_template = $app['transformationDescription']['facetDescriptions'][6]["expertReview"]['hoverover']['template'];
		$this->citations_text_template = $app['transformationDescription']['facetDescriptions'][7]["citations"]['hoverover']['template'];
		
		// **************************************   INITIALISE DRILLDOWN TEMPLATES   *********************************************
		$this->producer_profile_drilldown_url = $app['transformationDescription']['facetDescriptions'][0]['producerProfile']['drilldown']['url'];
		$this->producer_comments_drilldown_url = $app['transformationDescription']['facetDescriptions'][1]['producerComments']['drilldown']['url'];
		$this->lineage_drilldown_url = $app['transformationDescription']['facetDescriptions'][2]['lineage']['drilldown']['url'];
		$this->standards_drilldown_url = $app['transformationDescription']['facetDescriptions'][3]['standardsCompliance']['drilldown']['url'];
		$this->quality_information_drilldown_url = $app['transformationDescription']['facetDescriptions'][4]['qualityInformation']['drilldown']['url'];
		$this->user_feedback_drilldown_url = $app['transformationDescription']['facetDescriptions'][5]['userFeedback']['drilldown']['url'];
		$this->expert_review_drilldown_url = $app['transformationDescription']['facetDescriptions'][6]['expertReview']['drilldown']['url'];
		$this->citations_drilldown_url = $app['transformationDescription']['facetDescriptions'][7]['citations']['drilldown']['url'];
		
	}

	/* Function getAvailabilityEncodings
	 * Generates an array populated with GEO label facets' availability encodings
	 * 
	 * @param $xml DomDocument an XML document to process
	 * @return array of integers where key is a GEO label facet name and value is an integer availability encoding,
	 * or returns null if supplied xml is empty
	 */
	public function getAvailabilityEncodings($xml){
		if(empty($xml)){
			return null;
		}
		
		$availabilityArray = array(
							'producerProfile' => $this->evaluateAvailability($xml, $this->producer_profile_xpath),
							'producerComments' => $this->evaluateAvailability($xml, $this->producer_comments_xpath),
							'lineage' => $this->evaluateAvailability($xml, $this->lineage_xpath),
							'standardsComplaince' => $this->evaluateAvailability($xml, $this->standards_xpath),
							'qualityInformation' => $this->evaluateAvailability($xml, $this->quality_xpath),
							'userFeedback' => $this->evaluateAvailability($xml, $this->feedback_xpath),
							'expertReview' => $this->evaluateAvailability($xml, $this->review_xpath),
							'citations' => $this->evaluateAvailability($xml, $this->citations_xpath),
							);

		return $availabilityArray;
	}
	
	/* Function getHoveroverText
	 * Generates an array populated with hover-over text for each GEO label facet
	 * 
	 * @param $xml DomDocument an XML document to process
	 * @return array an array populated with hover-over text for each GEO label facet,
	 * or null if $xml is empty
	 */
	public function getHoveroverText($xml){
		if(empty($xml)){
			return null;
		}
		
		// Evaluate hoverover XPath expressions
		$organisationName = $this->evaluateXPath($xml, $this->organisation_name_xpath, "undefined");
		
		$supplementalInformation = $this->evaluateXPath($xml, $this->supplemental_information_xpath, "undefined");
		$knownProblems = $this->evaluateXPath($xml, $this->known_problems_xpath, "undefined");
		
		$processStepCount = $this->evaluateXPath($xml, $this->process_step_count_xpath, 0);
		
		$standardName = $this->evaluateXPath($xml, $this->standard_name_xpath, "undefined");
		$standardVersion = $this->evaluateXPath($xml, $this->standard_version_xpath, "undefined");
		
		$scopeLevel = $this->evaluateXPath($xml, $this->scope_level_xpath, "undefined");
		
		$feedbacksCount = $this->evaluateXPath($xml, $this->feedbacks_count_xpath, 0);
		$feedbacksAverageRating = round($this->evaluateXPath($xml, $this->average_rating_xpath, 0), 1);
		$ratingsCount = $this->evaluateXPath($xml, $this->ratings_count_xpath, 0);

		$expertReviewsCount = $this->evaluateXPath($xml, $this->reviews_count_xpath, 0);
		$expertAverageRating = round($this->evaluateXPath($xml, $this->reviews_average_rating_xpath, 0), 1);
		$expertRatingsCount = $this->evaluateXPath($xml, $this->reviews_ratings_count_xpath, 0);
		
		$citationsCount = $this->evaluateXPath($xml, $this->citations_count_xpath, 0);

		// Construct hoverover text array
		$hoveroverArray = array(
								'producerProfile' => $this->producer_profile_facet_title . PHP_EOL . sprintf($this->producer_profile_text_template, $organisationName),
								'producerComments' => $this->producer_comments_facet_title . PHP_EOL . sprintf($this->producer_comments_text_template, $supplementalInformation, $knownProblems),
								'lineage' => $this->lineage_facet_title . PHP_EOL . sprintf($this->lineage_text_template, $processStepCount),
								'standardsComplaince' => $this->standards_complaince_facet_title . PHP_EOL . sprintf($this->standards_complaince_text_template, $standardName, $standardVersion),
								'qualityInformation' => $this->quality_facet_title . PHP_EOL . sprintf($this->quality_information_text_template, $scopeLevel),
								'userFeedback' => $this->user_feedback_facet_title . PHP_EOL . sprintf($this->user_feedback_text_template, $feedbacksCount, $feedbacksAverageRating, $ratingsCount),
								'expertReview' => $this->expert_review_facet_title . PHP_EOL . sprintf($this->expert_review_text_template, $expertReviewsCount, $expertAverageRating, $expertRatingsCount),
								'citations' => $this->citations_facet_title . PHP_EOL . sprintf($this->citations_text_template, $citationsCount),
								);
								
		return $hoveroverArray;
	}
	
	/* Function getSummary
	 * Generates an array populated with summary of the information available for a given dataset
	 * 
	 * @param $xml DomDocument an XML document to process
	 * @return array an array populated with hover-over text for each GEO label facet,
	 * or null if $xml is empty
	 */
	public function getDatasetSummary($xml){
		if(empty($xml)){
			return null;
		}

		$summaryArray = array(  
						'producerProfile' => array(
							'availability' => $this->evaluateAvailability($xml, $this->producer_profile_xpath),
							'organisationName' => $this->evaluateXPath($xml, $this->organisation_name_xpath, "undefined"),
						),
						'producerComments' => array(
							'availability' => $this->evaluateAvailability($xml, $this->producer_comments_xpath),
							'supplementalInformation' => $supplementalInformation = $this->evaluateXPath($xml, $this->supplemental_information_xpath, "undefined"),
							'knownProblems' => $this->evaluateXPath($xml, $this->known_problems_xpath, "undefined"),
						),
						'lineage' => array(
							'availability' => $this->evaluateAvailability($xml, $this->lineage_xpath),
							'processStepCount' => $this->evaluateXPath($xml, $this->process_step_count_xpath, 0),
						),
						'standardsComplaince' => array(
							'availability' => $this->evaluateAvailability($xml, $this->standards_xpath),
							'standardName' => $this->evaluateXPath($xml, $this->standard_name_xpath, "undefined"),
							'standardVersion' => $this->evaluateXPath($xml, $this->standard_version_xpath, "undefined"),
						),
						'qualityInformation' => array(
							'availability' => $this->evaluateAvailability($xml, $this->quality_xpath),
							'scopeLevel' => $this->evaluateXPath($xml, $this->scope_level_xpath, "undefined"),
						),
						'userFeedback' => array(
							'availability' => $this->evaluateAvailability($xml, $this->feedback_xpath),
							'feedbacksCount' => $this->evaluateXPath($xml, $this->feedbacks_count_xpath, 0),
							'ratingsCount' => $this->evaluateXPath($xml, $this->ratings_count_xpath, 0),
							'feedbacksAverageRating' => round($this->evaluateXPath($xml, $this->average_rating_xpath, 0), 1),
						),
						'expertReview' => array(
							'availability' => $this->evaluateAvailability($xml, $this->review_xpath),
							'expertReviewsCount' => $this->evaluateXPath($xml, $this->reviews_count_xpath, 0),
							'expertRatingsCount' => $this->evaluateXPath($xml, $this->reviews_ratings_count_xpath, 0),
							'expertAverageRating' => round($this->evaluateXPath($xml, $this->reviews_average_rating_xpath, 0), 1),
						),
						'citations' => array(
							'availability' => $this->evaluateAvailability($xml, $this->citations_xpath),
							'citationsCount' => $this->evaluateXPath($xml, $this->citations_count_xpath, 0),
						)
					);

				return $summaryArray;
	}
	
	/* 
	 * Evaluates two XPath expressions and returns an availability integer
	 * @param $xml DomDocument an XML document to process
	 * @return integer 1 if at least one XPath expression returns true, or 0 if all XPaths expressions return false
	 */
	public function evaluateAvailability($xml, $path){
		$availability = 0;
		if(empty($xml)){
			return $availability;
		}
		
		$xpath = new DOMXpath($xml);
		$available = $xpath->evaluate($path);
		
		if(!empty($available)){
			$availability = 1;
		}
		
		return $availability;
	}
	
	/* Returns the result of XPaths evaluation.
	 *
	 * @param DOMDocument $xml XML document to iterate through
	 * @param string $path XPath expression
	 * @return integer or string result of XPath evaluation
	 */
	public function evaluateXPath($xml, $path, $returnDefault){
		if(empty($xml)){
			return null;
		}
		$xpath = new DOMXpath($xml);
		$eval = $xpath->evaluate($path);

		// If result is not empty, then return its value
		if(!empty($eval)){
			return $eval;
		}
		
		return $returnDefault;
	}
	
	/* Function getDrilldownURLs
	 * Generates an array populated with drilldown URLs for each GEO label facet
	 * 
	 * @param $producerURL String producer URL
	 * @param $feedbackURL String feedback URL
	 * @return array an array populated with drilldown URLs for each GEO label facet,
	 * or null if $xml is empty
	 */
	public function getDrilldownURLs($producerURL, $feedbackURL){
		$producerURL = urlencode($producerURL);
		$feedbackURL = urlencode($feedbackURL);
		

		// Get server protocol
		$server_protocol = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$server_protocol .= "://";
		
		/*
		// Get current URL and set base drilldown URL
		$request_uri = str_replace("geolabel?", "drilldown?", $_SERVER['REQUEST_URI']);
		// quick fix for the inspire demo
		$request_uri = str_replace("geolabel/demo", "drilldown?", $request_uri);
		
		$base_url = explode("?", $request_uri);
		$drilldown_base_url = $server_protocol . $_SERVER["SERVER_NAME"] . $base_url[0] . "?";
		
		// This code fixes geolabel.net redirection problem
		if (strpos($drilldown_base_url, '/?') !== false) {
			$drilldown_base_url = str_replace("/?", "/api/v1/drilldown?", $drilldown_base_url);
		}
		*/
		
		// temporary fix for the geolabel.net service
		$drilldown_base_url = $server_protocol . $_SERVER["SERVER_NAME"] . "/api/v1/drilldown";
		
		// Construct drilldown URLs
		$drilldownURLsArray = array(
								'producerProfile' => sprintf($this->producer_profile_drilldown_url, $drilldown_base_url, $producerURL),
								'producerComments' => sprintf($this->producer_comments_drilldown_url, $drilldown_base_url, $producerURL),
								'lineage' => sprintf($this->lineage_drilldown_url, $drilldown_base_url, $producerURL),
								'standardsComplaince' => sprintf($this->standards_drilldown_url, $drilldown_base_url, $producerURL),
								'qualityInformation' => sprintf($this->quality_information_drilldown_url, $drilldown_base_url, $producerURL),
								'userFeedback' => sprintf($this->user_feedback_drilldown_url, $drilldown_base_url, $feedbackURL),
								'expertReview' => sprintf($this->expert_review_drilldown_url, $drilldown_base_url, $feedbackURL),
								'citations' => sprintf($this->citations_drilldown_url, $drilldown_base_url, $producerURL, $feedbackURL),
								);
								
		return $drilldownURLsArray;
	}
	
	/* Function getStaticURLs
	 * Generates an array populated with drilldown URLs for each GEO label facet
	 * 
	 * @param $producerURL String producer URL
	 * @param $feedbackURL String feedback URL
	 * @return array an array populated with drilldown URLs for each GEO label facet,
	 * or null if $xml is empty
	 */
	public function getStaticURLs($producerURL, $feedbackURL){
		$staticURLsArray = array(
								'producerProfile' => $producerURL,
								'lineage' => $producerURL,
								'producerComments' => $producerURL,
								'standardsComplaince' => $producerURL,
								'qualityInformation' => $producerURL,
								'userFeedback' => $feedbackURL,
								'expertReview' => $feedbackURL,
								'citations' => $producerURL,
								);
								
		return $staticURLsArray;
	}

	/* Function getJsonAvailabilityEncodings
	 * Returns availability encoding for all 8 facets
	 * 
	 * @param $producerXML DomDocument  producer document
	 * @param $feedbackXML DomDocument feedback document
	 * @return String JSON representation of the GEO label facets
	 */
	public function getJsonAvailabilityEncodings($producerXML, $feedbackXML){
		// Join two documents
		$gvqXML = null;
		if(!empty($producerXML) && !empty($feedbackXML)){
			$gvqXML = $this->joinXMLDoms($producerXML, $feedbackXML);
		}
		elseif(!empty($producerXML)){
			$gvqXML = $producerXML;
		}
		elseif(!empty($feedbackXML)){
			$gvqXML = $feedbackXML;
		}		
		// Get availability data from the XML document into an array
		$availabilityArray = $this->getAvailabilityEncodings($gvqXML);
		$datasetID = $this->getXMLFileIdentifier($gvqXML);
		$json = json_encode(array('datasetIdentifier' => $datasetID, 'facets' => $availabilityArray));
		
		return $json;
	}
	
	/* Function getJsonDatasetSummary
	 * Returns dataset summary for 8 GEO label facets in a JSON format
	 * 
	 * @param $producerXML DomDocument  producer document
	 * @param $feedbackXML DomDocument feedback document
	 * @return String JSON representation of the GEO label facets
	 */
	public function getJsonDatasetSummary($producerXML, $feedbackXML){
		// Join two documents
		$gvqXML = null;
		if(!empty($producerXML) && !empty($feedbackXML)){
			$gvqXML = $this->joinXMLDoms($producerXML, $feedbackXML);
		}
		elseif(!empty($producerXML)){
			$gvqXML = $producerXML;
		}
		elseif(!empty($feedbackXML)){
			$gvqXML = $feedbackXML;
		}
		
		// Get summary of the XML documents
		$datasetID = $this->getXMLFileIdentifier($gvqXML);
		$summaryArray = $this->getDatasetSummary($gvqXML);		

		$json = json_encode(array('datasetIdentifier' => $datasetID, 'facets' => $summaryArray));
		return $json;
	}

	
	/* Function getXMLFileIdentifier
	 * Returns file identifier stored in the <fileIdentifier> tag of a given XML document
	 * 
	 * @param $xml DomDocument an XML document to process
	 * @return String file identifier of a given XML document
	 */
	public function getXMLFileIdentifier($xml){
		$id = null;
		if(empty($xml)){
			return $id;
		}
		$xpath = new DOMXpath($xml);
		$nodes = $xpath->query($this->fileIdentifierXPath);
		if ($nodes->length > 0){		
			if(preg_replace('/\s+/', '', $nodes->item(0)->nodeValue) != ""){
				$id = $nodes->item(0)->nodeValue;
			}
		}
		return trim($id);
	}

	 /* Function getXmlFromURL
	  * Checks whether a file exists in the $_FILES array and whether it is of allowed format.
	  * Stores XML in corresponding variable based on XML root node
	  *
	  * @param string $url URL location of the file to load
	  * @return DOMDocument (XML file retrieved) OR null (no file supplied or incorrect extension) 
	  */
	function getXmlFromURL($url){
		$dom = null;
		if(empty($url)){
			return null;
		}
		else{
			$xmlString = file_get_contents($url);
			if(!empty($xmlString)){
				$dom = new DOMDocument('1.0');
				$dom->formatOutput = true;
				if($dom->loadXML($xmlString)){
					return $dom;
				}
				return null;
			}
		}
		return null;
	}

	/* Function joinXMLDoms
	 * Generates a joined DomDocument from two XML strings.
	 * 
	 * @param DomDocument $xml_1 DomDocument XML 1
	 * @param DomDocument $xml_2 DomDocument XML 2
	 * @return DomDocument joined XML document
	 */
	public function joinXMLDoms($xml_1, $xml_2){
		$joinedXML = null;
		if(!empty($xml_1) && !empty($xml_2)){
			$joinedXML = new DOMDocument('1.0', 'UTF-8');
			$joinedXML->formatOutput = true;
			
			// Create and append root element
			$xmlRoot = $joinedXML->createElementNS($this->gvqNameSpace, 'gvq:GVQ_Metadata');
			$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xlink', 'http://www.w3.org/1999/xlink');
			$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
			$xmlRoot = $joinedXML->appendChild($xmlRoot);
			
			// Get root elements of the temporary XMLs
			$tmpRoot1 = $joinedXML->importNode($xml_1->documentElement, true);
			$tmpRoot2 = $joinedXML->importNode($xml_2->documentElement, true);
			// Append XMLs to root of the joined XML DomDocument
			$xmlRoot->appendChild($tmpRoot1);
			$xmlRoot->appendChild($tmpRoot2);
		}
		return $joinedXML;
	}

		
	/* Function joinXMLStrings
	 * Generates a joined DomDocument from two XML strings.
	 * 
	 * @param String $xml_1 XMl string 1
	 * @param String $xml_2 XML string 2
	 * @return DomDocument joined XML document
	 */
	public function joinXMLStrings($xml_1, $xml_2){
		$joinedXML = null;
		if(!empty($xml_1) && !empty($xml_2)){
			$joinedXML = new DOMDocument('1.0', 'UTF-8');
			$joinedXML->formatOutput = true;
			
			// Create and append root element
			$xmlRoot = $joinedXML->createElementNS($this->gvqNameSpace, 'gvq:GVQ_Metadata');
			$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xlink', 'http://www.w3.org/1999/xlink');
			$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
			$xmlRoot = $joinedXML->appendChild($xmlRoot);
			// Create temporary DomDocuments to store XML strings
			$tmpXML_1 = new DOMDocument('1.0', 'UTF-8');
			$tmpXML_2 = new DOMDocument('1.0', 'UTF-8');
			if($tmpXML_1->loadXML($xml_1) && $tmpXML_2->loadXML($xml_2)){
				// Get root elements of the temporary XMLs
				$tmpXML_1Root = $joinedXML->importNode($tmpXML_1->documentElement, true);
				$tmpXML_2Root = $joinedXML->importNode($tmpXML_2->documentElement, true);
				// Append XMLs to root of the joined XML DomDocument
				$xmlRoot->appendChild($tmpXML_1Root);
				$xmlRoot->appendChild($tmpXML_2Root);
			}
			return $joinedXML;
		}
		return $joinedXML;
	}
}
?>