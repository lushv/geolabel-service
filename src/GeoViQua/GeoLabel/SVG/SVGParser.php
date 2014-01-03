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
use \DOMDocument;
use \DOMXpath;

class SVGParser{
	private $svg;
	private $xmlProcessor;
	
	/* Constructor
	*/
	public function __construct(){
		$this->svg = new SVG();
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
			$size = 200;
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