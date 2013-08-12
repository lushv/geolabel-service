<?php
/**
* Lml class provides functionality for constructing an LML representation of the GEO label.
*
* PHP version 5
*
* @author		Original Author Victoria Lush
* @version		1.0
*/
namespace GeoViQua\GeoLabel\LML;
use \DOMDocument;
use \DOMXpath;

class LML{
	// Private class variables:
	private $lmlDom;
	private $lmlNS = 'http://geolabel.info';
	// key - the qualified name of the element, as prefix:tagname; value - the value of the element
	private $lmlRootAttributes = array(
									'glb:sourceDocuments'  => null,
									'glb:labelParameters' => null,
									'glb:facets' => null,);
	
	/* Constructor
	 * Initialises the lml DomDocument. Generates the following XML structure:
	 *<glb:geoLabel xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:glb="http://geolabel.info" >
	 *	<glb:sourceDocuments></glb:sourceDocuments>
	 *	<glb:labelParameters></glb:labelParameters>
	 *	<glb:facets></glb:facets>
	 *</glb:geoLabel>
	*/
	public function __construct(){
		$this->lmlDom = new DOMDocument('1.0', 'UTF-8');
		$this->lmlDom->formatOutput = true;

		$tmpRoot = $this->createElement($this->lmlNS, 'glb:geoLabel', $this->lmlRootAttributes);
		$lmlRoot = $this->lmlDom->importNode($tmpRoot, true);
		$lmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xlink', 'http://www.w3.org/1999/xlink');
		$lmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$lmlRoot = $this->lmlDom->appendChild($lmlRoot);	
	}
	
	/* Function createElement
	 * Generates a DOMElement based on supplied information.
	 * 
	 * @param $elementNamespaceURI the namespace URI of the element and its child nodes
	 * @param $elementQualifiedName the qualified name of the element, as prefix:tagname
	 * @param $childNodes array an array of child nodes of the element where key is the qualified name of the element, as prefix:tagname,
	 * and value is the value of the element.
	 * @return DOMElement based on supplied information or null if no element created.
	 */
	public function createElement($elementNamespaceURI, $elementQualifiedName, $childNodes){
		$dom = new DOMDocument();
		$dom->formatOutput = true;
		
		if(empty($elementNamespaceURI) || empty($elementQualifiedName)){
			return null;
		}
		$lmlElement = $dom->createElementNS($elementNamespaceURI, $elementQualifiedName);
		
		if(!empty($childNodes)){
			foreach ($childNodes as $key => $value) {
				$childElement = $dom->createElementNS($elementNamespaceURI, $key, $value);
				$lmlElement->appendChild($childElement);
			}
		}
		return $lmlElement;
	}
	
	/* Function createNode
	 * Generates a DomDocument node element.
	 * 
	 * @param $elementNamespaceURI String the namespace URI of the element
	 * @param $elementQualifiedName String the qualified name of the element, as prefix:tagname
	 * @param $value String node value
	 * @return DOMElement based on supplied information or null if no element created.
	 */
	public function createNode($elementNamespaceURI, $elementQualifiedName, $value){
		$dom = new DOMDocument();
		$dom->formatOutput = true;
		
		if(empty($elementNamespaceURI) || empty($elementQualifiedName)){
			return null;
		}
		$lmlElement = $dom->createElementNS($elementNamespaceURI, $elementQualifiedName, $value);
		return $lmlElement;
	}
	
	/* Function appendElement 
	 * Appends DomElement to a specified parent LML element.
	 * 
	 * @parentElementNamespaceURI String namespace URL of the element to append to
	 * @param $parentElementName String name of the element to append to (with no namespace)
	 * @param $element DomElement GEO label facet element to be appended
	 * @return boolean true if the element was appended or false otherwise
	 */
	public function appendElement($parentElementNamespaceURI, $parentElementName, $element){
		if(empty($parentElementNamespaceURI) || empty($parentElementName) || empty($element)){
			return false;
		}
		$facetsElement = $this->lmlDom->getElementsByTagNameNS($parentElementNamespaceURI, $parentElementName)->item(0);
		if(!empty($facetsElement)){
			$tmpNode = $this->lmlDom->importNode($element, true);
			$facetsElement->appendChild($tmpNode);
			return true;
		}
		return false;
	}

	/* Function getLmlDom
	 * @return DomDocument representation of the GEO label LML.
	 */
	public function getLmlDom(){
		return $this->lmlDom;
	}
	
	/* Function getLmlString
	 * @return String representation of the GEO label LML.
	 */
	public function getLmlString(){
		// Remove any extra spaces and return XML string
		return preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~','$1', $this->lmlDom->saveXML());
	}
}
?>