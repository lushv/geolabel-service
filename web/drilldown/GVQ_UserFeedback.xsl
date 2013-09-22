<?xml version="1.0" encoding="UTF-8"?>
<!-- Created with Liquid XML Studio Developer Edition (Education) 9.1.11.3570 (http://www.liquid-technologies.com) -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:gvq="http://www.geoviqua.org/QualityInformationModel/4.0" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml/3.2" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gmi="http://www.isotc211.org/2005/gmi" xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:gmd19157="http://www.geoviqua.org/gmd19157" xmlns:updated19115="http://www.geoviqua.org/19115_updates" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:un="http://www.uncertml.org/2.0" xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xsl:variable name="fileID" select="gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString|gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString|gvq:GVQ_Metadata/gmd:fileIdentifier/gco:CharacterString|//gmd:seriesMetadata/gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString" />
    <xsl:template match="/">
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                <title>User Feedback</title>
                <link href="bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"/>
            </head>
            <body>
				<div class="page-header">
					<h1>User Feedback Summary</h1>
				</div>
				<div class="container">
				<h2>Dataset identifier:
                    <xsl:value-of select="$fileID" />
                </h2>
				<xsl:for-each select="//gvq:item">
					<xsl:call-template name="feedback"/>
				</xsl:for-each>
				</div>
          </body>
        </html>
        </xsl:template>
	
	<xsl:param name="count" select="1"/>
    <xsl:template match="//gvq:item" name="feedback">
		<!-- Test expertise level -->
        <xsl:variable name="expertiseLevel" select="gvq:user/gvq:expertiseLevel"/>
		<xsl:if test="not($expertiseLevel = 4 or $expertiseLevel = 5)">
			<!-- Get user details information -->
			<xsl:variable name="user" select="gvq:user"/>
			<xsl:variable name="userRole" select="gvq:user/gvq:userDetails/gmd:role/gmd:CI_RoleCode"/>
			<xsl:variable name="userOrganisationName" select="gvq:user/gvq:userDetails/gmd:organisationName/gco:CharacterString"/>
			<xsl:variable name="individualName" select="gvq:user/gvq:userDetails/gmd:individualName/gco:CharacterString"/>
			<xsl:variable name="applicationDomain" select="gvq:user/gvq:applicationDomain"/>
			<!-- User comment -->
			<xsl:variable name="userComment" select="gvq:userComment/gvq:comment"/>
			<!-- User ratings -->
			<xsl:variable name="ratingScore" select="gvq:rating/gvq:score"/>
			<xsl:variable name="ratingJustification" select="gvq:rating/gvq:justification"/>
			<!-- Usage reports -->
			<xsl:variable name="reportAspect" select="gvq:usage/gvq:reportAspect/gvq:GVQ_ReportAspectCode/@codeListValue"/>
			<xsl:variable name="usageDescription" select="gvq:usage/gvq:usageDescription"/>
			<xsl:variable name="knownProblem" select="gvq:usage/gvq:discoveredIssue/gvq:GVQ_DiscoveredIssue/gvq:knownProblem/gco:CharacterString"/>

			<table width="95%" border="2" cellpadding="5" cellspacing="2">
				<th colspan="2">
					<h4>User Feedback</h4>
				</th>
				<tr>
				<td>
					<xsl:if test="$user">
						<h4><u>User Details:</u></h4>
						<xsl:if test="$userRole != ''">
							<b>User Role: </b>
							<xsl:value-of select="$userRole"/><br /><br />
						</xsl:if>
						<xsl:if test="$userOrganisationName != ''">
							<b>User Organisation Name: </b>
							<xsl:value-of select="$userOrganisationName"/><br /><br />
						</xsl:if>
						<xsl:if test="$individualName != ''">
							<b>Individual Name: </b>
							<xsl:value-of select="$individualName"/><br /><br />
						</xsl:if>
						<xsl:if test="$expertiseLevel != ''">
							<b>Expertise Level: </b>
							<xsl:value-of select="$expertiseLevel"/><br /><br />
						</xsl:if>
						<xsl:if test="$applicationDomain != ''">
							<b>Application Domain: </b>
							<xsl:value-of select="$applicationDomain"/><br /><br />
						</xsl:if>
						<hr />
					</xsl:if>
					<xsl:if test="$userComment != '' or $ratingScore != ''">				
						<h4><u>Feedback:</u></h4>
						<xsl:if test="$userComment != ''">
							<b>User Comment:</b><br />
							<xsl:value-of select="$userComment"/><br /><br />
						</xsl:if>
						<xsl:if test="$ratingScore != ''">
							<b>User Rating: </b>
							<xsl:value-of select="$ratingScore"/><br />

							<b>Rating Justification:</b><br />
							<xsl:value-of select="$ratingJustification"/><br />
						</xsl:if>
						<hr />
					</xsl:if>			
				<!-- Usage Reports -->
					<xsl:if test="$reportAspect != '' or $usageDescription != '' or $knownProblem != ''">				
						<h4><u>Usage Report:</u></h4>
						<xsl:if test="$reportAspect != ''">
							<b>Report Aspect: </b>
							<xsl:value-of select="$reportAspect"/><br /><br />
						</xsl:if>
						<xsl:if test="$usageDescription != ''">
							<b>Usage Description:</b><br />
							<xsl:value-of select="$usageDescription"/><br /><br />
						</xsl:if>
						<xsl:if test="$knownProblem != ''">
							<b>Known Problem:</b><br />
							<xsl:value-of select="$knownProblem"/><br />
						</xsl:if>
					</xsl:if>
				</td>
				</tr>
			</table>
			<br />
			<br />
		</xsl:if>
    </xsl:template>
</xsl:stylesheet>