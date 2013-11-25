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
	private $producer_profile_xpath_rest;
	private $producer_profile_xpath_gvq;

	private $producer_comments_xpath_rest;
	private $producer_comments_xpath_gvq;
	
	private $lineage_xpath_rest;
	private $lineage_xpath_gvq;
	
	private $standards_xpath_rest;
	private $standards_xpath_gvq;

	private $quality_xpath_rest;
	private $quality_xpath_gvq;
	
	private $feedback_xpath_rest;
	private $feedback_xpath_gvq;
	
	private $review_xpath_rest;
	private $review_xpath_gvq;

	private $citations_xpath_rest;
	private $citations_xpath_gvq;
	
	// *************************************************   HOVER-OVER XPATHS   **************************************************
	private $organisation_name_xpath_rest;
	private $organisation_name_xpath_gvq;

	private $supplemental_information_xpath_rest;
	private $supplemental_information_xpath_gvq;
	private $known_problems_xpath_rest;
	private $known_problems_xpath_gvq;
	
	private $process_step_count_xpath_rest;
	private $process_step_count_xpath_gvq;
	
	private $standard_name_xpath_rest;
	private $standard_name_xpath_gvq;
	
	private $standard_version_xpath_rest;
	private $standard_version_xpath_gvq;
	
	private $scope_level_xpath_rest;
	private $scope_level_xpath_gvq;
	
	private $feedbacks_count_xpath_rest;
	private $feedbacks_count_xpath_gvq;
	private $ratings_count_xpath_rest;
	private $ratings_count_xpath_gvq;
	private $average_rating_xpath_rest;
	private $average_rating_xpath_gvq;
	
	private $reviews_count_xpath_rest;
	private $reviews_count_xpath_gvq;
	private $reviews_ratings_count_xpath_rest;
	private $reviews_ratings_count_xpath_gvq;
	private $reviews_average_rating_xpath_rest;
	private $reviews_average_rating_xpath_gvq;

	private $citations_count_xpath_rest;
	private $citations_count_xpath_gvq;

	// ***********************************************   HOVER-OVER TEMPLATES   **************************************************

	
	// Drilldown URLs for each GEO label facet
	private $baseDrilldownURL = "";
	
	/* Constructor
	*/
	public function __construct($app){
		
		// SET PRODUCER PROFILE XPATHS
		$this->producer_profile_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["producerProfile"]["availabilityPath"];
		
		$this->producer_profile_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["producerProfile"]["availabilityPath"];

		$this->organisation_name_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["producerProfile"]["hoverover"]["text"]["organizationNamePath"];
		$this->organisation_name_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["producerProfile"]["hoverover"]["text"]["organizationNamePath"];
		
		// SET PRODUCER COMMENTS XPATHS
		$this->producer_comments_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["producerComments"]["availabilityPath"];
		$this->producer_comments_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["producerComments"]["availabilityPath"];
		
		$this->supplemental_information_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["producerComments"]["hoverover"]["text"]["supplementalInformation"];
		$this->supplemental_information_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["producerComments"]["hoverover"]["text"]["supplementalInformation"];
		
		$this->known_problems_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["producerComments"]["hoverover"]["text"]["knownProblemsPath"];
		$this->known_problems_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["producerComments"]["hoverover"]["text"]["knownProblemsPath"];

		// SET LINEAGE XPATHS
		$this->lineage_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["lineage"]["availabilityPath"];
		$lineage_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["lineage"]["availabilityPath"];
		
		$this->process_step_count_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["lineage"]["hoverover"]["text"]["processStepCountPath"];
		$this->process_step_count_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["lineage"]["hoverover"]["text"]["processStepCountPath"];
		
		// SET STANDARDS XPATHS
		$this->standards_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["standardsCompliance"]["availabilityPath"];
		$this->standards_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["standardsCompliance"]["availabilityPath"];
		
		$this->standard_name_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["standardsCompliance"]["hoverover"]["text"]["standardNamePath"];
		$this->standard_name_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["standardsCompliance"]["hoverover"]["text"]["standardNamePath"];
		
		$this->standard_version_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["standardsCompliance"]["hoverover"]["text"]["standardVersion"];
		$this->standard_version_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["standardsCompliance"]["hoverover"]["text"]["standardVersion"];
		
		// SET QUALITY XPATHS
		$this->quality_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["qualityInformation"]["availabilityPath"];
		$this->quality_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["qualityInformation"]["availabilityPath"];
		
		$this->scope_level_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["qualityInformation"]["hoverover"]["text"]["scopeLevelPath"];
		$this->scope_level_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["qualityInformation"]["hoverover"]["text"]["scopeLevelPath"];
		
		// SET FEEDBACK XPATHS
		$this->feedback_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["userFeedback"]["availabilityPath"];
		$this->feedback_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["userFeedback"]["availabilityPath"];
		
		$this->feedbacks_count_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["userFeedback"]["hoverover"]["text"]["feedbacksCountPath"];
		$this->feedbacks_count_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["userFeedback"]["hoverover"]["text"]["feedbacksCountPath"];
		
		$this->ratings_count_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["userFeedback"]["hoverover"]["text"]["ratingsCountPath"];
		$this->ratings_count_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["userFeedback"]["hoverover"]["text"]["ratingsCountPath"];
		
		$this->average_rating_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["userFeedback"]["hoverover"]["text"]["averageRatingPath"];
		$this->average_rating_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["userFeedback"]["hoverover"]["text"]["averageRatingPath"];

		// SET REVIEWS XPATHS
		$this->review_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["expertReview"]["availabilityPath"];
		$this->review_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["expertReview"]["availabilityPath"];
		
		$this->reviews_count_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["expertReview"]["hoverover"]["text"]["reviewsCountPath"];
		$this->reviews_count_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["expertReview"]["hoverover"]["text"]["reviewsCountPath"];
		
		$this->reviews_ratings_count_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["expertReview"]["hoverover"]["text"]["ratingsCountPath"];
		$this->reviews_ratings_count_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["expertReview"]["hoverover"]["text"]["ratingsCountPath"];
		
		$this->reviews_average_rating_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["expertReview"]["hoverover"]["text"]["averageRatingPath"];
		$this->reviews_average_rating_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["expertReview"]["hoverover"]["text"]["averageRatingPath"];

		// SET CITATIONS XPATHS
		$this->citations_xpath_rest = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["citations"]["availabilityPath"];
		$this->citations_xpath_gvq = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["citations"]["availabilityPath"];
		
		$this->citations_count_xpath_rest = $app["transformerGVQ"]["transformationDescription"]["facetDescriptions"]["citations"]["hoverover"]["text"]["citationsCountPath"];
		$this->citations_count_xpath_gvq = $app["transformerRest"]["transformationDescription"]["facetDescriptions"]["citations"]["hoverover"]["text"]["citationsCountPath"];
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
							'producerProfile' => $this->evaluateAvailability($xml, $this->producer_profile_xpath_rest, $this->producer_profile_xpath_gvq),
							'producerComments' => $this->evaluateAvailability($xml, $this->producer_comments_xpath_rest, $this->producer_comments_xpath_gvq),
							'lineage' => $this->evaluateAvailability($xml, $this->lineage_xpath_rest, $this->lineage_xpath_gvq),
							'standardsComplaince' => $this->evaluateAvailability($xml, $this->standards_xpath_rest, $this->standards_xpath_gvq),
							'qualityInformation' => $this->evaluateAvailability($xml, $this->quality_xpath_rest, $this->quality_xpath_gvq),
							'userFeedback' => $this->evaluateAvailability($xml, $this->feedback_xpath_rest, $this->feedback_xpath_gvq),
							'expertReview' => $this->evaluateAvailability($xml, $this->review_xpath_rest, $this->review_xpath_gvq),
							'citations' => $this->evaluateAvailability($xml, $this->citations_xpath_rest, $this->citations_xpath_gvq),
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

		$producerProfileText = 'Producer Profile' . PHP_EOL;
		$producerCommentsText = 'Producer Comments' . PHP_EOL;
		$lineageText = 'Lineage Information' . PHP_EOL;
		$standardsComplainceText = 'Standards Compliance' . PHP_EOL;
		$qualityInformationText = 'Quality Information' . PHP_EOL;
		$userFeedbackText = 'User Feedback' . PHP_EOL;
		$expertReviewText = 'Expert Review' . PHP_EOL;
		$citationsText = 'Citations Information' . PHP_EOL;

		$organisationName = $this->evaluateXPaths($xml, $this->organisation_name_xpath_rest, $this->organisation_name_xpath_gvq, "undefined");
		$producerProfileText .= "Organisation name: $organisationName.";
		
		$supplementalInformation = $this->evaluateXPaths($xml, $this->supplemental_information_xpath_rest, $this->supplemental_information_xpath_gvq, "undefined");
		$producerCommentsText .= "Supplemental Information: $supplementalInformation" . PHP_EOL;
		
		$knownProblems = $this->evaluateXPaths($xml, $this->known_problems_xpath_rest, $this->known_problems_xpath_gvq, "undefined");
		$producerCommentsText .= "Known Problems: $knownProblems";

		$processStepCount = $this->evaluateXPaths($xml, $this->process_step_count_xpath_rest, $this->process_step_count_xpath_gvq, 0);
		$lineageText .= "Number of process steps: $processStepCount.";
		
		$standardName = $this->evaluateXPaths($xml, $this->standard_name_xpath_rest, $this->standard_name_xpath_gvq, "undefined");
		$standardVersion = $this->evaluateXPaths($xml, $this->standard_version_xpath_rest, $this->standard_version_xpath_gvq, "undefined");
		$standardsComplainceText .= "Standard name: $standardName, version $standardVersion.";
		
		$scopeLevel = $this->evaluateXPaths($xml, $this->scope_level_xpath_gvq, $this->scope_level_xpath_rest, "undefined");
		$qualityInformationText .= "Quality information scope: $scopeLevel.";
		
		$feedbacksCount = $this->evaluateXPaths($xml, $this->feedbacks_count_xpath_rest, $this->feedbacks_count_xpath_gvq, 0);
		$ratingsCount = $this->evaluateXPaths($xml, $this->ratings_count_xpath_rest, $this->ratings_count_xpath_gvq, 0);
		$feedbacksAverageRating = round($this->evaluateXPaths($xml, $this->average_rating_xpath_rest, $this->average_rating_xpath_gvq, 0), 1);
		$userFeedbackText .= "Number of feedbacks: $feedbacksCount. Average rating: $feedbacksAverageRating ($ratingsCount rating(s)).";
		
		$expertReviewsCount = $this->evaluateXPaths($xml, $this->reviews_count_xpath_rest, $this->reviews_count_xpath_gvq, 0);
		$expertRatingsCount = $this->evaluateXPaths($xml, $this->reviews_ratings_count_xpath_rest, $this->reviews_ratings_count_xpath_gvq, 0);
		$expertAverageRating = round($this->evaluateXPaths($xml, $this->reviews_average_rating_xpath_rest, $this->reviews_average_rating_xpath_gvq, 0), 1);
		$expertReviewText .= "Number of reviews: $expertReviewsCount. Average rating: $expertAverageRating ($expertRatingsCount ratings).";
		
		$citationsCount = $this->evaluateXPaths($xml, $this->citations_count_xpath_rest, $this->citations_count_xpath_gvq, 0);
		$citationsText .= "Number of citations: $citationsCount.";
		
		$hoveroverArray = array(
								'producerProfile' => $producerProfileText,
								'lineage' => $lineageText,
								'producerComments' => $producerCommentsText,
								'standardsComplaince' => $standardsComplainceText,
								'qualityInformation' => $qualityInformationText,
								'userFeedback' => $userFeedbackText,
								'expertReview' => $expertReviewText,
								'citations' => $citationsText,
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
							'availability' => $this->evaluateAvailability($xml, $this->producer_profile_xpath_rest, $this->producer_profile_xpath_gvq),
							'organisationName' => $this->evaluateXPaths($xml, $this->organisation_name_xpath_rest, $this->organisation_name_xpath_gvq, "undefined"),
						),
						'producerComments' => array(
							'availability' => $this->evaluateAvailability($xml, $this->producer_comments_xpath_rest, $this->producer_comments_xpath_gvq),
							'supplementalInformation' => $supplementalInformation = $this->evaluateXPaths($xml, $this->supplemental_information_xpath_rest, $this->supplemental_information_xpath_gvq, "undefined"),
							'knownProblems' => $this->evaluateXPaths($xml, $this->known_problems_xpath_rest, $this->known_problems_xpath_gvq, "undefined"),
						),
						'lineage' => array(
							'availability' => $this->evaluateAvailability($xml, $this->lineage_xpath_rest, $this->lineage_xpath_gvq),
							'processStepCount' => $this->evaluateXPaths($xml, $this->process_step_count_xpath_rest, $this->process_step_count_xpath_gvq, 0),
						),
						'standardsComplaince' => array(
							'availability' => $this->evaluateAvailability($xml, $this->standards_xpath_rest, $this->standards_xpath_gvq),
							'standardName' => $this->evaluateXPaths($xml, $this->standard_name_xpath_rest, $this->standard_name_xpath_gvq, "undefined"),
							'standardVersion' => $this->evaluateXPaths($xml, $this->standard_version_xpath_rest, $this->standard_version_xpath_gvq, "undefined"),
						),
						'qualityInformation' => array(
							'availability' => $this->evaluateAvailability($xml, $this->quality_xpath_rest, $this->quality_xpath_gvq),
							'scopeLevel' => $this->evaluateXPaths($xml, $this->scope_level_xpath_gvq, $this->scope_level_xpath_rest, "undefined"),
						),
						'userFeedback' => array(
							'availability' => $this->evaluateAvailability($xml, $this->feedback_xpath_rest, $this->feedback_xpath_gvq),
							'feedbacksCount' => $this->evaluateXPaths($xml, $this->feedbacks_count_xpath_rest, $this->feedbacks_count_xpath_gvq, 0),
							'ratingsCount' => $this->evaluateXPaths($xml, $this->ratings_count_xpath_rest, $this->ratings_count_xpath_gvq, 0),
							'feedbacksAverageRating' => round($this->evaluateXPaths($xml, $this->reviews_average_rating_xpath_rest, $this->reviews_average_rating_xpath_gvq, 0), 1),
						),
						'expertReview' => array(
							'availability' => $this->evaluateAvailability($xml, $this->review_xpath_rest, $this->review_xpath_gvq),
							'expertReviewsCount' => $this->evaluateXPaths($xml, $this->reviews_count_xpath_rest, $this->reviews_count_xpath_gvq, 0),
							'expertRatingsCount' => $this->evaluateXPaths($xml, $this->reviews_ratings_count_xpath_rest, $this->reviews_ratings_count_xpath_gvq, 0),
							'expertAverageRating' => round($this->evaluateXPaths($xml, $this->reviews_ratings_count_xpath_rest, $this->reviews_ratings_count_xpath_gvq, 0), 1),
						),
						'citations' => array(
							'availability' => $this->evaluateAvailability($xml, $this->citations_xpath_rest, $this->citations_xpath_gvq),
							'citationsCount' => $this->evaluateXPaths($xml, $this->citations_count_xpath_rest, $this->citations_count_xpath_gvq, 0),
						)
					);
								
		return $summaryArray;
	}
	
	/* 
	 * Evaluates two XPath expressions and returns an availability integer
	 * @param $xml DomDocument an XML document to process
	 * @return integer 1 if at least one XPath expression returns true, or 0 if all XPaths expressions return false
	 */
	public function evaluateAvailability($xml, $path_1, $path_2){
		$availability = 0;
		if(empty($xml)){
			return $availability;
		}
		
		$xpath = new DOMXpath($xml);
		$available_1 = $xpath->evaluate($path_1);
		$available_2 = $xpath->evaluate($path_2);
		
		if(!empty($available_1) || !empty($available_2)){
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
	public function evaluateXPaths($xml, $path_1, $path_2, $returnDefault){
		if(empty($xml)){
			return null;
		}
		$xpath = new DOMXpath($xml);
		$eval_1 = $xpath->evaluate($path_1);
		$eval_2 = $xpath->evaluate($path_2);

		// Check if both expressions return a number, if so add them together and return the value
		if(preg_match('/^\d+$/', $eval_1) && preg_match('/^\d+$/', $eval_2)){
			return ($eval_1 + $eval_2);
		}
		// If first result is not empty, then return its value
		if(!empty($eval_1)){
			return $eval_1;
		}
		// If second result is not empty, then return its value
		if(!empty($eval_2)){
			return $eval_2;
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
		$drilldown_base_url = $server_protocol . $_SERVER["SERVER_NAME"] . "/api/v1/drilldown?";
		
		// Construct drilldown URLs
		$producerProfileURL = $drilldown_base_url . 'metadata=' . $producerURL . '&facet=' . 'producer_profile';
		$producerCommentsURL = $drilldown_base_url . 'metadata=' . $producerURL . '&facet=' . 'producer_comments';
		$lineageURL = $drilldown_base_url . 'metadata=' . $producerURL . '&facet='. 'lineage';
		$standardsComplainceURL = $drilldown_base_url . 'metadata=' . $producerURL . '&facet=' . 'standards_complaince';
		$qualityInformationURL = $drilldown_base_url . 'metadata=' . $producerURL . '&facet=' . 'quality';
		
		$userFeedbackURL = $drilldown_base_url . 'metadata=' . $producerURL . '&feedback=' .$feedbackURL . '&facet=' . 'user_feedback';
		$expertReviewURL = $drilldown_base_url . 'metadata=' . $producerURL . '&feedback=' .$feedbackURL . '&facet=' . 'expert_review';
		$citationsURL = $drilldown_base_url . 'metadata=' . $producerURL . '&feedback=' .$feedbackURL . '&facet=' . 'citations';
		
		$drilldownURLsArray = array(
								'producerProfile' => $producerProfileURL,
								'lineage' => $lineageURL,
								'producerComments' => $producerCommentsURL,
								'standardsComplaince' => $standardsComplainceURL,
								'qualityInformation' => $qualityInformationURL,
								'userFeedback' => $userFeedbackURL,
								'expertReview' => $expertReviewURL,
								'citations' => $citationsURL,
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
	
	/* Function getAvailabilityInteger
	 * Idenifies whether specified nodes exist in an xml document
	 * 
	 * @param $xml DomDocument an XML document to process
	 * @return integer 1 if found nodes, or 0 if no nodes located
	 */
	public function getAvailabilityInteger($xml, $path){
		$availability = 0;
		if(empty($xml)){
			return $availability;
		}
		$xpath = new DOMXpath($xml);
		$nodes = $xpath->query($path);
		
		if ($nodes->length > 0){
			foreach ($nodes as $node){
				// Check if node is not empty
				if(preg_replace('/\s+/', '', $node->nodeValue) != ""){
					$availability = 1;
					break;
				}
			}
		}
		return $availability;
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