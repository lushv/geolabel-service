<?xml version="1.0" encoding="UTF-8"?>
<!-- Created with Liquid XML Studio Developer Edition (Education) 9.1.11.3570 (http://www.liquid-technologies.com) -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:gvq="http://www.geoviqua.org/QualityInformationModel/4.0" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml/3.2" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gmi="http://www.isotc211.org/2005/gmi" xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:gmd19157="http://www.geoviqua.org/gmd19157" xmlns:updated19115="http://www.geoviqua.org/19115_updates" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:un="http://www.uncertml.org/2.0" xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xsl:variable name="fileID" select="gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString|gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString|gvq:GVQ_Metadata/gmd:fileIdentifier/gco:CharacterString|//gmd:seriesMetadata/gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString" />
    <xsl:template match="/">
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                <title>Producer Comments</title>
                <link href="/stylesheets/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"/>
            </head>
            <body>
				<div class="page-header">
					<h1>Producer Comments Summary</h1>
				</div>
				<div class="container">
				<h2>Dataset identifier:
                    <xsl:value-of select="$fileID" />
                </h2>
				<xsl:for-each select="//gmd:identificationInfo">
						<xsl:call-template name="supplementalInfo"/>
				</xsl:for-each>
				<br />
				<table width="95%" border="2" cellpadding="5" cellspacing="2">				
				<th><h4>Discovered Issues</h4></th>
				<xsl:for-each select="//gvq:dataQualityInfo//gvq:discoveredIssue">
						<xsl:call-template name="discoveredIssue"/>
				</xsl:for-each>
				</table>
				</div>
          </body>
        </html>
        </xsl:template>              
    <!-- Template for collating feedbacks info a few paras -->
    <xsl:template match="//gmd:identificationInfo" name="supplementalInfo">
		<!-- Producer Supplemental Information -->
        <xsl:variable name="suppInfo" select="//gmd:supplementalInformation/gco:CharacterString"/>
		<xsl:if test="$suppInfo">
			<table width="95%" border="2" cellpadding="5" cellspacing="2">
			<th><h4>Supplemental Information</h4></th>
				<tr>
					<td><xsl:value-of select="$suppInfo"/></td>
				</tr>
			</table>
			<br />
		</xsl:if>
    </xsl:template>
	
    <xsl:template match="//gvq:dataQualityInfo//gvq:discoveredIssue" name="discoveredIssue">
		<!-- Discovered Issues -->
		<xsl:for-each select="gvq:GVQ_DiscoveredIssue">
			<xsl:variable name="knownProblem" select="gvq:knownProblem/gco:CharacterString"/>
			<xsl:variable name="workAround" select="gvq:workAround/gco:CharacterString"/>
			<xsl:if test="$knownProblem">
				<tr>
					<td>
						<b>Known Problem:</b><br />
						<xsl:value-of select="$knownProblem"/><br /><br />
						<b>Work Around: </b><br />
						<xsl:value-of select="$workAround"/><br />
					</td>
				</tr>
			</xsl:if>
		</xsl:for-each>
    </xsl:template>
</xsl:stylesheet>