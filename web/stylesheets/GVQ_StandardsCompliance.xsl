<?xml version="1.0" encoding="UTF-8"?>
<!-- Created with Liquid XML Studio Developer Edition (Education) 9.1.11.3570 (http://www.liquid-technologies.com) -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:gvq="http://www.geoviqua.org/QualityInformationModel/4.0" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml/3.2" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gmi="http://www.isotc211.org/2005/gmi" xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:gmd19157="http://www.geoviqua.org/gmd19157" xmlns:updated19115="http://www.geoviqua.org/19115_updates" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:un="http://www.uncertml.org/2.0" xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xsl:variable name="fileID" select="gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString|gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString|gvq:GVQ_Metadata/gmd:fileIdentifier/gco:CharacterString|//gmd:seriesMetadata/gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString" />
    <xsl:template match="/">
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                <title>Standards Compliance</title>
                <link href="/stylesheets/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"/>
            </head>
            <body>
				<div class="page-header">
					<h1>Standards Compliance Summary</h1>
				</div>
				<div class="container">
				<h2>Dataset identifier:
                    <xsl:value-of select="$fileID" />
                </h2>
				<table width="95%" border="2" cellpadding="5" cellspacing="2">
					<th><h4>Metadata Standard</h4></th>
                    <xsl:for-each select="/">
                        <xsl:call-template name="standard"/>
			        </xsl:for-each>
				</table>
				<br />
			</div>
          </body>
        </html>
        </xsl:template>              
    <!-- Template for collating feedbacks info a few paras -->
    <xsl:template match="//gmd:metadataStandardName" name="standard">
		<!-- Producer Supplemental Information -->
        <xsl:variable name="standardName" select="//gmd:metadataStandardName/gco:CharacterString"/>
        <xsl:variable name="standardVersion" select="//gmd:metadataStandardVersion/gco:CharacterString"/>
		<tr>
			<td>
				<b>Metadata Standard Name: </b>
				<xsl:value-of select="$standardName"/><br /><br />

				<b>Metadata Standard Version: </b>
				<xsl:value-of select="$standardVersion"/>
			</td>
		</tr>
    </xsl:template>
</xsl:stylesheet>