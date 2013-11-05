<?xml version="1.0" encoding="UTF-8"?>
<!-- Created with Liquid XML Studio Developer Edition (Education) 9.1.11.3570 (http://www.liquid-technologies.com) -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:gvq="http://www.geoviqua.org/QualityInformationModel/4.0" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml/3.2" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gmi="http://www.isotc211.org/2005/gmi" xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:gmd19157="http://www.geoviqua.org/gmd19157" xmlns:updated19115="http://www.geoviqua.org/19115_updates" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:un="http://www.uncertml.org/2.0" xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xsl:variable name="fileID" select="gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString|gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString|gvq:GVQ_Metadata/gmd:fileIdentifier/gco:CharacterString|//gmd:seriesMetadata/gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString" />
    <xsl:template match="/">
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                <title>Producer Profile</title>
                <link href="/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"/>
            </head>
            <body>
				<div class="page-header">
					<h1>Producer Profile Summary</h1>
				</div>
				<div class="container">
				<h2>Dataset identifier:
                    <xsl:value-of select="$fileID" />
                </h2>
				<xsl:for-each select="//gmd:contact/gmd:CI_ResponsibleParty | //gmd:pointOfContact/gmd:CI_ResponsibleParty">
						<xsl:call-template name="producerProf"/>
				</xsl:for-each>
				<br />
				</div>
			</body>
        </html>
        </xsl:template>              
    <!-- Template for collating feedbacks info a few paras -->
    <xsl:template match="//gmd:contact/gmd:CI_ResponsibleParty" name="producerProf">
		<!-- Producer details information -->
        <xsl:variable name="individualName" select="gmd:individualName/gco:CharacterString"/>
        <xsl:variable name="organisationName" select="gmd:organisationName/gco:CharacterString"/>
        <xsl:variable name="positionName" select="gmd:positionName/gco:CharacterString"/>
		
        <xsl:variable name="deliveryPoint" select="gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString"/>
        <xsl:variable name="city" select="gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString"/>
        <xsl:variable name="administrativeArea" select="gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:administrativeArea/gco:CharacterString"/>
        <xsl:variable name="postalCode" select="gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString"/>
        <xsl:variable name="country" select="gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString"/>
        <xsl:variable name="voice" select="gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/>
        <xsl:variable name="fax" select="gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString"/>
        <xsl:variable name="electronicMailAddress" select="gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
		
		<table width="95%" border="2" cellpadding="5" cellspacing="2">
		<th>
			<h4>Producer Details</h4>
		</th>
			<tr>
				<td>
					<b>Individual Name :</b>
					<xsl:value-of select="$individualName"/><br /><br />

					<b>Organisation Name: </b>
					<xsl:value-of select="$organisationName"/><br /><br />
					
					<b>Position: </b>
					<xsl:value-of select="$positionName"/><br /><br />
				</td>
			</tr>
		<th>
			<h4>Contact Details</h4>
		</th>
		<tr>
			<td>
				<b>Delivery Point: </b>
				<xsl:value-of select="$deliveryPoint"/><br /><br />
				
				<b>City: </b>
				<xsl:value-of select="$city"/><br/><br />

				<b>Administrative Area: </b>
				<xsl:value-of select="$administrativeArea"/><br /><br />

				<b>Postal Code: </b>
				<xsl:value-of select="$postalCode"/><br /><br />

				<b>Country: </b>
				<xsl:value-of select="$country"/><br/><br />

				<b>Telephone number: </b>
				<xsl:value-of select="$voice"/><br /><br />

				<b>Fax: </b>
				<xsl:value-of select="$fax"/><br /><br />
				
				<b>Email address: </b>
				<xsl:value-of select="$electronicMailAddress"/>
			</td>
		</tr>
		</table>
		<br />
    </xsl:template>
</xsl:stylesheet>