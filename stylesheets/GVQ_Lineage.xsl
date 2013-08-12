<?xml version="1.0" encoding="UTF-8"?>
<!-- Created with Liquid XML Studio Developer Edition (Education) 9.1.11.3570 (http://www.liquid-technologies.com) -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:gvq="http://www.geoviqua.org/QualityInformationModel/4.0" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml/3.2" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gmi="http://www.isotc211.org/2005/gmi" xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:gmd19157="http://www.geoviqua.org/gmd19157" xmlns:updated19115="http://www.geoviqua.org/19115_updates" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:un="http://www.uncertml.org/2.0" xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xsl:variable name="fileID" select="gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString|gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString|gvq:GVQ_Metadata/gmd:fileIdentifier/gco:CharacterString|//gmd:seriesMetadata/gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString" />
    <xsl:template match="/">
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                <title>Lineage Information</title>
                <link href="bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"/>
            </head>
            <body>
				<div class="page-header">
					<h1>Lineage Information Summary</h1>
				</div>
				<div class="container">
				<h2>Dataset identifier:
                    <xsl:value-of select="$fileID" />
                </h2>
				<table width="95%" border="2" cellpadding="5" cellspacing="2">
                    <xsl:for-each select="//gmd19157:LI_Lineage">
                        <xsl:call-template name="lineageInfo"/>
			        </xsl:for-each>          
				</table>
				<br />
				</div>
          </body>
        </html>
        </xsl:template>              
    <!-- Template for collating feedbacks info a few paras -->
    <xsl:template match="gmd19157:LI_Lineage" name="lineageInfo">
        <xsl:variable name="statement" select="gmd19157:statement/gco:CharacterString"/>
        <xsl:variable name="description" select="gmd19157:processStep/gmd19157:LI_ProcessStep/gmd19157:description/gco:CharacterString"/>
        <xsl:variable name="rationale" select="gmd19157:processStep/gmd19157:LI_ProcessStep/gmd19157:rationale/gco:CharacterString"/>
		
		<xsl:if test="$statement or $description or $rationale">
			<th>
				<h4>Lineage Information</h4>
			</th>
			<tr>
				<td>
					<xsl:if test="$statement">
						<b>Lineage Statement:</b><br />
						<xsl:value-of select="$statement"/><br /><br />
					</xsl:if>
					<xsl:if test="$description">
						<b>Process Step Description:</b><br />
						<xsl:value-of select="$description"/><br /><br />
					</xsl:if>
					<xsl:if test="$rationale">
						<b>Process Step Rationale:</b><br />
						<xsl:value-of select="$rationale"/><br /><br />					
					</xsl:if>
				</td>
			</tr>
		</xsl:if>
    </xsl:template>
</xsl:stylesheet>