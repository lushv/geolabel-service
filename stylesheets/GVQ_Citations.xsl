<?xml version="1.0" encoding="UTF-8"?>
<!-- Created with Liquid XML Studio Developer Edition (Education) 9.1.11.3570 (http://www.liquid-technologies.com) -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:gvq="http://www.geoviqua.org/QualityInformationModel/4.0" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml/3.2" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gmi="http://www.isotc211.org/2005/gmi" xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:gmd19157="http://www.geoviqua.org/gmd19157" xmlns:updated19115="http://www.geoviqua.org/19115_updates" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:un="http://www.uncertml.org/2.0" xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xsl:variable name="fileID" select="gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString|gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString|gvq:GVQ_Metadata/gmd:fileIdentifier/gco:CharacterString|//gmd:seriesMetadata/gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString" />
    <xsl:template match="/">
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                <title>Citations</title>
                <link href="bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"/>
            </head>
            <body>
				<div class="page-header">
					<h1>Citations Summary</h1>
				</div>
				<div class="container">
				<h2>Dataset identifier:
                    <xsl:value-of select="$fileID" />
                </h2>
				
				<xsl:for-each select="//gvq:GVQ_Publication">
					<table width="95%" border="2" cellpadding="5" cellspacing="2">
						<xsl:call-template name="pub"/>
					</table>
					<br />
				</xsl:for-each>
				</div>
          </body>
        </html>
        </xsl:template>              
    <!-- Template for collating publication info into a few paras -->
    <xsl:template match="gvq:GVQ_Publication" name="pub">
        <xsl:variable name="diReferenceTitle" select="gmd:title/gco:CharacterString"/>
        <xsl:variable name="diReferenceDOI" select="gvq:doi/gco:CharacterString"/>
		<tr>
			<th>
				<h4>Dataset Citation</h4>
			</th>
		</tr>
        <tr>
			<td>
				<b>Title:</b><br />
				<xsl:value-of select="$diReferenceTitle"/><br /><br />
				<b>DOI:</b><br />
				<xsl:value-of select="$diReferenceDOI"/><br /><br />

			
				<xsl:for-each select="gvq:onlineResource">
					<xsl:variable name="diUUID" select="gmd:CI_OnlineResource/@uuid"/>
					<xsl:variable name="diname" select="gmd:CI_OnlineResource/gmd:name/gco:CharacterString"/>
					<xsl:variable name="didesc" select="gmd:CI_OnlineResource/gmd:description/gco:CharacterString"/>
					<xsl:variable name="diprotocol" select="gmd:CI_OnlineResource/gmd:protocol/gco:CharacterString"/>
					<xsl:variable name="dilinkage" select="gmd:CI_OnlineResource/gmd:linkage/gmd:URL"/>
					<xsl:if test="$diUUID">
						<a>
						<xsl:attribute name="href">
							<xsl:value-of select="concat('http://www.ngdc.noaa.gov/docucomp/iso/',$diUUID)"/><br />
						</xsl:attribute>
						<xsl:value-of select="$diUUID"/><br /><br />
						</a>
					</xsl:if>
					<xsl:if test="$diname or $didesc">
						<xsl:value-of select="$diname"/>:<xsl:value-of select="$didesc"/><br /><br />
					</xsl:if>
					<xsl:if test="$dilinkage">
						<a>
							<xsl:attribute name="href">
								<xsl:value-of select="$dilinkage"/><br />
							</xsl:attribute>
							<xsl:value-of select="$dilinkage"/><br /><br />
						</a>
					</xsl:if>
				</xsl:for-each>
				<!-- Selecting online resources -->
				<xsl:variable name="pType" select="gvq:category/gvq:GVQ_PublicationCategoryCode/@codeListValue"/>
				<xsl:variable name="pISSN" select="gvq:ISSN/gco:CharacterString"/>
				<xsl:variable name="pISBN" select="gvq:ISBN/gco:CharacterString"/>
				<xsl:if test="$pType">
					<b>Category:</b><br />
					<xsl:value-of select="$pType"/><br /><br />
				</xsl:if>
				<xsl:if test="$pISSN">
					<b>ISSN: </b><br />
					<xsl:value-of select="$pISSN"/><br /><br />
				</xsl:if>
				<xsl:if test="$pISBN">
					<b>ISBN:</b><br />
					<xsl:value-of select="$pISBN"/><br /><br />
				</xsl:if>
			</td>
	   </tr>
    </xsl:template>
</xsl:stylesheet>