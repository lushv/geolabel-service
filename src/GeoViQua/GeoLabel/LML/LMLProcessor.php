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
use \DOMDocument;
use \DOMXpath;

class LMLProcessor{
	// Private class variables
	private $gvqNameSpace = 'xmlns:gvq="http://www.geoviqua.org/QualityInformationModel/3.1"';
	private $lml;
	private $xmlProcessor;
	private $availabilityArray;
	private $hoveroverTextArray;
	private $drilldownURLsArray;
	
	
	/* Constructor
	*/
	public function __construct(){
		include "LML.php";
		$this->lml = new LML();
		include "XMLProcessor.php";
		$this->xmlProcessor = new XMLProcessor();
	}
}
?>