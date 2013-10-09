<?php
/**
* LMLParser class provides functionality to generate an LML GEO label representation based on XML file.
*
* PHP version 5
*
* @author		Original Author Victoria Lush
* @version		1.0
*/
namespace GeoViQua\GeoLabel\SVG;
use GeoViQua\GeoLabel\SVG\SVG as SVG;
use GeoViQua\GeoLabel\XML\XMLProcessor as XMLProcessor;
use \DOMDocument;
use \DOMXpath;

class SVGParser{
	private $svg;
	private $xmlProcessor;
	
	//private $availabilityArray;
	//private $hoveroverTextArray;
	//private $drilldownURLsArray;
	
	/* Constructor
	*/
	public function __construct(){
		$this->svg = new SVG();
		$this->xmlProcessor = new XMLProcessor();
	}

	/* Function constructFromURLFiles
	 * Constructs a full SVG representation of the GEO label from two URLs
	 * 
	 * @param $producerURL String URL of the producer document
	 * @param $feedbackURL String URL of the feedback document
	 * @param $size String size
	 * @return String svg representation of the GEO label if generated successfully
	 */
	public function constructFromURLFiles($producerXML, $producerURL, $feedbackXML, $feedbackURL, $size){
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
		$hoveroverTextArray = $this->xmlProcessor->getHoveroverText($gvqXML);
		$drilldownURLsArray = $this->xmlProcessor->getDrilldownURLs($producerURL, $feedbackURL);
		
		$labelSVG = $this->constructSVG($availabilityArray, $hoveroverTextArray, $drilldownURLsArray, $size);
		
		return $labelSVG;
	}
	
	
	
	/* Function constructFromAggregatedXML
	 * Constructs a full LML representation of the GEO label from an aggregated XML file
	 * This function takes the URL of the XML file as it is provided. It assumes that the 
	 * URL is not the URL of the actual XML document but a link to some related documentation.
	 * 
	 * @param $aggregatedXML DomDocument an XML document to process
	 * @param $aggregatedURL String URL of the document
	 * @param $size String size
	 * @return String svg GEO label representation
	 */
	public function constructFromAggregatedXML($aggregatedXML, $aggregatedURL, $size){
		if(empty($aggregatedXML)){
			return null;
		}
		// Get all data from the XML document into 3 arrays
		$availabilityArray = $this->xmlProcessor->getAvailabilityEncodings($aggregatedXML);
		$hoveroverTextArray = $this->xmlProcessor->getHoveroverText($aggregatedXML);
		$drilldownURLsArray = $this->xmlProcessor->getStaticURLs($aggregatedURL, $aggregatedURL);
				
		$labelSVG = $this->constructSVG($availabilityArray, $hoveroverTextArray, $drilldownURLsArray, $size);
		
		return $labelSVG;
	}

	/* Function constructFromAggregatedURL
	 * Constructs a full LML representation of the GEO label from an aggregated XML file
	 * This function will convert the URL of the aggregated file into a drill-down link
	 * 
	 * @param $aggregatedXML DomDocument an XML document to process
	 * @param $aggregatedURL String URL of the document
	 * @param $size String size
	 * @return String svg GEO label representation
	 */
	public function constructFromAggregatedURL($aggregatedURL, $size){
		if(empty($aggregatedURL)){
			return null;
		}
		
		$aggregatedXML = $this->xmlProcessor->getXmlFromURL($aggregatedURL);
		
		// Get all data from the XML document into 3 arrays
		$availabilityArray = $this->xmlProcessor->getAvailabilityEncodings($aggregatedXML);
		$hoveroverTextArray = $this->xmlProcessor->getHoveroverText($aggregatedXML);
		$drilldownURLsArray = $this->xmlProcessor->getStaticURLs($aggregatedURL, $aggregatedURL);
				
		$labelSVG = $this->constructSVG($availabilityArray, $hoveroverTextArray, $drilldownURLsArray, $size);
		
		return $labelSVG;
	}
	
	/* Function constructFromXMLs
	 * Constructs a full LML representation of the GEO label from an aggregated XML file
	 * 
	 * @param $aggregatedXML DomDocument an XML document to process
	 * @param $aggregatedURL String URL of the document
	 * @param $size String size
	 * @return String svg GEO label representation
	 */
	public function constructFromXMLs($producerXML, $producerURL, $feedbackXML, $feedbackURL, $size){
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
		$hoveroverTextArray = $this->xmlProcessor->getHoveroverText($gvqXML);
		$drilldownURLsArray = $this->xmlProcessor->getDrilldownURLs($producerURL, $feedbackURL);
			
		$labelSVG = $this->constructSVG($availabilityArray, $hoveroverTextArray, $drilldownURLsArray, $size);
		
		return $labelSVG;
	}

	/* Function constructFromURLs
	 * Constructs a full LML representation of the GEO label from two URLs
	 * 
	 * @param $producerURL String URL of the producer document
	 * @param $feedbackURL String URL of the feedback document
	 * @param $size String size
	 * @return DomDocument lml document if generated successfully
	 */
	public function constructFromURLs($producerURL, $feedbackURL, $size){
		if(empty($producerURL) && empty($feedbackURL)){
			return null;
		}
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
		$hoveroverTextArray = $this->xmlProcessor->getHoveroverText($gvqXML);
		$drilldownURLsArray = $this->xmlProcessor->getDrilldownURLs($producerURL, $feedbackURL);
		
		$labelSVG = $this->constructSVG($availabilityArray, $hoveroverTextArray, $drilldownURLsArray, $size);
		
		return $labelSVG;
	}
	
	/* Function constructSVG
	 * Constructs a full LML representation of the GEO label from an aggregated XML file
	 * This function takes the URL of the XML file as it is provided. It assumes that the 
	 * URL is not the URL of the actual XML document but a link to some related documentation.
	 * 
	 * @param $availabilityArray array of availability encodings
	 * @param $hoveroverTextArray array of 
	 * @param $drilldownURLsArray String URL of the document
	 * @param $size String size
	 * @return String svg representation of the GEO label
	 */
	public function constructSVG($availabilityArray, $hoveroverTextArray, $drilldownURLsArray, $size){
		if(empty($availabilityArray) || empty($hoveroverTextArray) || empty($drilldownURLsArray)){
			return null;
		}
		if(empty($size)){
			$size = 250;
		}
		$labelSVG = $this->svg->getHeader($size);
		$labelSVG .= $this->svg->getFacetsGroupOpeningTag();
			
			$labelSVG .= $this->svg->getFacet('producer_profile', $availabilityArray['producerProfile'], $hoveroverTextArray['producerProfile'], $drilldownURLsArray['producerProfile']);
			$labelSVG .= $this->svg->getFacet('lineage', $availabilityArray['lineage'], $hoveroverTextArray['lineage'], $drilldownURLsArray['lineage']);
			$labelSVG .= $this->svg->getFacet('producer_comments', $availabilityArray['producerComments'], $hoveroverTextArray['producerComments'], $drilldownURLsArray['producerComments']);
			$labelSVG .= $this->svg->getFacet('standards_compliance', $availabilityArray['standardsComplaince'], $hoveroverTextArray['standardsComplaince'], $drilldownURLsArray['standardsComplaince']);
			$labelSVG .= $this->svg->getFacet('quality_information', $availabilityArray['qualityInformation'], $hoveroverTextArray['qualityInformation'], $drilldownURLsArray['qualityInformation']);
			$labelSVG .= $this->svg->getFacet('user_feedback', $availabilityArray['userFeedback'], $hoveroverTextArray['userFeedback'], $drilldownURLsArray['userFeedback']);
			$labelSVG .= $this->svg->getFacet('expert_review', $availabilityArray['expertReview'], $hoveroverTextArray['expertReview'], $drilldownURLsArray['expertReview']);
			$labelSVG .= $this->svg->getFacet('citations_information', $availabilityArray['citations'], $hoveroverTextArray['citations'], $drilldownURLsArray['citations']);

		$labelSVG .= $this->svg->getGroupClosingTag();
		$labelSVG .= $this->svg->getBrandingGroupOpeningTag();
			$labelSVG .= $this->svg->getBranding();
		$labelSVG .= $this->svg->getGroupClosingTag();
		$labelSVG .= $this->svg->getFooter();
		
		return $labelSVG;
	}
}
?>