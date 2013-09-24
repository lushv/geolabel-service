<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:gvq="http://www.geoviqua.org/QualityInformationModel/4.0" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml/3.2" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gmi="http://www.isotc211.org/2005/gmi" xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:gmd19157="http://www.geoviqua.org/gmd19157" xmlns:updated19115="http://www.geoviqua.org/19115_updates" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:un="http://www.uncertml.org/2.0" xmlns:xs="http://www.w3.org/2001/XMLSchema">

    <xsl:variable name="transformName" select="'GVQ_Quality.xsl'"/>
    <xsl:variable name="transformVersion" select="'2.0'"/>
    <xsl:variable name="contact" select="gmi:MI_Metadata/gmd:contact | gvq:GVQ_Metadata/gmd:contact | gmd:MD_Metadata/gmd:contact" />
    <xsl:variable name="fileID" select="gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString|gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString|gvq:GVQ_Metadata/gmd:fileIdentifier/gco:CharacterString|//gmd:seriesMetadata/gmi:MI_Metadata/gmd:fileIdentifier/gco:CharacterString" />
    <xsl:key name="scopeCodes" match="//gmd:hierarchyLevel/gmd:MD_ScopeCode" use="." />
    <xsl:key name="uniqueIndividualsOrganisationsRole" match="gmd:CI_ResponsibleParty" use="concat(normalize-space(gmd:individualName/gco:CharacterString),normalize-space(gmd:organisationName/gco:CharacterString),gmd:role/gmd:CI_RoleCode)" />
    <xsl:key name="uniqueAbstracts" match="gmd:abstract" use="normalize-space(gco:CharacterString)" />
    <xsl:key name="uniqueReferencs" match="//*[@xlink:href]" use="concat(@xlink:href,@xlink:title)" />
    <xsl:key name="uniqueBoundingBoxes" match="gmd:EX_GeographicBoundingBox" use="concat(gmd:westBoundLongitude/gco:Decimal,gmd:eastBoundLongitude/gco:Decimal,gmd:southBoundLatitude/gco:Decimal,gmd:northBoundLatitude/gco:Decimal)" />
    <xsl:key name="uniqueExtents" match="gmd:EX_Extent" use="concat(gmd:geographicElement/@xlink:href,gmd:temporalElement/gmd:EX_TemporalExtent/gmd:extent/@xlink:href)" />
    <xsl:key name="referencedScope" match="gmd19157:DQ_Scope" use="@id" />
    <xsl:key name="referencedMetaQuality" match="gmd19157:relatedElement" use="@xlink:href" />
    <!-- Display Results Fields -->
    <xsl:template match="/">
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
                <style type="text/css">
                    table {
                    empty-cells : show;
                    }></style>
                <title>ISO Table View</title>
                <link href="/stylesheets/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"></link>
            </head>
            <body>
				<div class="page-header">
					<h1>Quality Report Summary</h1>
				</div>
				<div class="container">
				<h2>Dataset identifier:
                    <xsl:value-of select="$fileID" />
                </h2>
                <xsl:if test="count(//gvq:dataQualityInfo/*/gmd19157:report)">
                    <xsl:for-each select="//gvq:dataQualityInfo">
						<xsl:if test="gmd19157:report">
							<p>
								<h2>Scope:
									<xsl:value-of select="//gmd19157:scope/gmd19157:DQ_Scope/gmd19157:level/gmd:MD_ScopeCode/@codeListValue" />
									<xsl:variable name="scopeDesc" select="//gmd19157:scope/gmd19157:DQ_Scope/gmd19157:level/gmd:MD_ScopeDescription/gmd:attributes" />
									<xsl:if test="$scopeDesc">
										- <xsl:value-of select="$scopeDesc" />
									</xsl:if>
								</h2>
							</p>
						</xsl:if>
                        <xsl:for-each select=".//gmd19157:report/*">
                            <xsl:variable name="thisName" select="name(.)" />
                            <!-- TODO - test whether this is a metaquality report or a quality report -->
                            <xsl:choose>
                                <xsl:when test="$thisName = 'gmd19157:DQ_Confidence' or $thisName = 'gmd19157:DQ_Representativity' or $thisName = 'gmd19157:DQ_Homogeneity' or $thisName = 'gvq:GVQ_Traceability' or $thisName = 'gmd19157:DQ_MetaQuality'">
                                    <!-- for now, do nothing -->
                                </xsl:when>
                                <xsl:otherwise>
                                    <!-- Each will have up to one evaluation, up to one measure, and potentially several results -->
                                    <table width="95%" border="1" cellpadding="2" cellspacing="2">
                                        <tr>
                                            <th colspan="4">
                                                <h3>  Report Type:
                                                    <!-- TODO - put these in a lookup list somewhere -->
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_ThematicClassificationCorrectness'">
                                                        Thematic Classification Correctness
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_Completeness'">
                                                        Completeness
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_CompletenessCommission'">
                                                        Completeness - Commission
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_CompletenessOmmission'">
                                                        Completeness - Ommission
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_LogicalConsistency'">
                                                        Logical Consistency
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_ConceptualConsistency'">
                                                        Conceptual Consistency
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_DomainConsistency'">
                                                        Domain Consistency
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_FormatConsistency'">
                                                        Format Consistency
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_TopologicalConsistency'">
                                                        Topological Consistency
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_PositionalAccuracy'">
                                                        Positional Accuracy
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_AbsoluteExternalPositionalAccuracy'">
                                                        Absolute External Positional Accuracy
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_GriddedDataPositionalAccuracy'">
                                                        Gridded Data Positional Accuracy
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_TemporalAccuracy'">
                                                        Temporal Accuracy
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_AccuracyOfATimeMeasurement'">
                                                        Accuracy of a Time Measurement
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_TemporalConsistency'">
                                                        Temporal Consistency
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_TemporalValidity'">
                                                        Temporal Validity
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_TemporalAccuracy'">
                                                        Temporal Accuracy
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_NonQuantitativeAttributeAccuracy'">
                                                        Non-quantitative Attribute Accuracy
                                                    </xsl:if>
                                                    <xsl:if test="$thisName = 'gmd19157:DQ_QuantitativeAttributeAccuracy'">
                                                        Quantitative Attribute Accuracy
                                                    </xsl:if>
                                                </h3>
                                            </th>
                                        </tr>
                                        <xsl:variable name="thisReportID" select="concat('#', ./@id)" />
                                        <!-- Later we'll search for xlinks to this report which will give us related metaquality records -->
                                        <xsl:for-each select="gmd19157:evaluation/*">
                                            <tr>
                                                <td valign="top" colspan="1">
                                                    <b>Evaluation Method</b>
                                                </td>
                                                <td valign="top" colspan="3">
                                                    <xsl:variable name="Method" select="gmd19157:evaluationMethodType/gmd19157:DQ_EvaluationMethodTypeCode/@codeListValue" />
                                                    <xsl:variable name="thisName2" select="name(.)" />
                                                    <xsl:if test = "$thisName2 = 'gvq:GVQ_FullInspection' or $thisName2 = 'gmd:MD_FullInspection'">
                                                        <xsl:value-of select="'Full Inspection'" /> :
                                                    </xsl:if>
                                                    <xsl:if test="$thisName2 = 'gvq:GVQ_IndirectEvaluation' or $thisName2 = 'gmd:MD_IndirectEvaluation'">
                                                        <xsl:value-of select="'Indirect Evaluation'" />
                                                    </xsl:if>
                                                    <xsl:if test="$thisName2 = 'gvq:GVQ_SampleBasedInspection' or $thisName2 = 'gmd:MD_SampleBasedInspection'">
                                                        <xsl:value-of select="'Sample-based Inspection'" />
                                                    </xsl:if>
                                                    :
                                                    <xsl:value-of select="$Method" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top" colspan="1">
                                                    <b>Evaluation Description</b>
                                                </td>
                                                <td valign="top" colspan="3">
                                                    <xsl:variable name="Description" select="gmd19157:evaluationMethodDescription/gco:CharacterString" />
                                                    <xsl:value-of select="$Description" />
                                                </td>
                                            </tr>
                                            <!-- test for any publications -->
                                            <xsl:for-each select="gmd19157:referenceDoc/gvq:GVQ_Publication">
                                                <tr>
                                                    <td valign="top" colspan="1">
                                                        <b>Reference document</b>
                                                    </td>
                                                    <td valign="top" colspan="3">
                                                        <xsl:apply-templates select="." />
                                                    </td>
                                                </tr>
                                            </xsl:for-each>
                                            <!-- Selecting publications -->
                                            <!-- Reference datasets -->
                                            <xsl:for-each select="gvq:referenceDataset/updated19115:MD_AssociatedResource">
                                                <tr>
                                                    <td colspan="1">
                                                        <b>Reference Dataset </b>
                                                    </td>
                                                    <td colspan="3">
                                                        <b>Name: </b>
                                                        <xsl:value-of select="updated19115:name/gmd:CI_Citation/gmd:title/gco:CharacterString" />
                                                        : <b>Identifier: </b>
                                                        <xsl:value-of select="updated19115:name/gmd:CI_Citation/gmd:identifier/updated19115:MD_Identifier/updated19115:codeSpace/gco:CharacterString" />:
                                                        <xsl:value-of select="updated19115:name/gmd:CI_Citation/gmd:identifier/updated19115:MD_Identifier/gmd:code/gco:CharacterString" />
                                                        <br />
                                                        <b>Application: </b>
                                                        <xsl:value-of select="updated19115:associationType/gmd:DS_AssociationTypeCode/@codeListValue" />:
                                                        <xsl:value-of select="updated19115:initiativeType/gmd:DS_InitiativeTypeCode/@codeListValue" />
                                                        <br />
                                                    </td>
                                                </tr>
                                            </xsl:for-each>
                                            <!-- Selecting reference datasets -->
                                        </xsl:for-each>
                                        <!-- Selecting evaluations (0 or 1)-->
                                        <xsl:for-each select="gmd19157:measure">
                                            <xsl:variable name="measure" select="gmd:nameOfMeasure/gco:CharacterString" />
                                            <xsl:variable name="measuredesc" select="/gmd:measureDescription/gco:CharacterString" />
                                            <xsl:if test="$measure or $measuredesc">
                                                <tr>
                                                    <td valign="top" colspan="1" class=".th">
                                                        <b>Measure</b>
                                                    </td>
                                                    <td colspan="3">
                                                        <xsl:value-of select="../../gmd19157:measure/gmd:nameOfMeasure/gco:CharacterString" /> :
                                                        <xsl:value-of select="../../gmd19157:measure/gmd:measureDescription/gco:CharacterString" />
                                                    </td>
                                                </tr>
                                            </xsl:if>
                                        </xsl:for-each>
                                        <!-- Selecting measures (0 or 1) -->
                                        <xsl:for-each select="gmd19157:result/*">
                                            <tr>
                                                <th colspan="4">Result number
                                                    <xsl:value-of select="position()" />
                                                </th>
                                            </tr>
                                            <tr>
                                                <td valign="top" colspan="1">
                                                    <b>Result Scope</b>
                                                </td>
                                                <td colspan="3">
                                                    <xsl:choose>
                                                        <xsl:when test="count(gmd19157:resultScope/gmd19157:DQ_Scope)">
                                                            <xsl:apply-templates select="gmd19157:resultScope/gmd19157:DQ_Scope" />
                                                        </xsl:when>
                                                        <xsl:otherwise>
                                                            <xsl:variable name="scopeID" select="gmd19157:resultScope/@xlink:href" />
                                                            <xsl:if test="$scopeID != ''">
                                                                <xsl:apply-templates select="key('referencedScope',substring($scopeID,2))" />
                                                            </xsl:if>
                                                        </xsl:otherwise>
                                                    </xsl:choose>
                                                </td>
                                            </tr>
                                            <xsl:variable name="thisName5" select="name(.)" />
                                            <xsl:if test="$thisName5 = 'gmd19157:DQ_QuantitativeResult'">
                                                <!-- the result will have one or more values, a valueUnit and an optional value type -->
                                                <xsl:variable name="units" select="gmd:valueUnit" />
                                                <xsl:variable name="unitsURL" select="gmd:valueUnit/@xlink:href" />
                                                <xsl:for-each select="gmd19157:value/gco:Record">
                                                    <tr>
                                                        <td valign="top" colspan="1">
                                                            <b>Result</b>
                                                        </td>
                                                        <td colspan="3">
                                                            <xsl:choose>
                                                                <xsl:when test="count(child::*)=0">
                                                                    <xsl:value-of select="." />
                                                                </xsl:when>
                                                                <xsl:otherwise>
                                                                    <xsl:variable name="thisName7" select="name(*[1])" />
                                                                    <xsl:choose>
                                                                        <!--<xsl:when test="$thisName7 = 'un:ConfusionMatrix'">-->
                                                                        <xsl:when test="$thisName7 = 'nothing'">
                                                                            <!-- tabular matrix display requires 'tokenize' from XSLT 2.0 -->
                                                                            <xsl:apply-templates select ="." />
                                                                            <!-- when there are templates for all possible result types, don't need to test name -->
                                                                        </xsl:when>
                                                                        <xsl:otherwise>
                                                                            <xsl:for-each select="./*">
                                                                                <xsl:for-each select="child::*">
                                                                                    <p>
                                                                                        <b>
                                                                                            <xsl:value-of select="name(.)" />
                                                                                        </b>:
                                                                                        <xsl:value-of select="." />
                                                                                    </p>
                                                                                </xsl:for-each>
                                                                            </xsl:for-each>
                                                                        </xsl:otherwise>
                                                                    </xsl:choose>
                                                                    <!-- Selecting sub-elements of result - messy but temporary -->
                                                                </xsl:otherwise>
                                                            </xsl:choose>
                                                            <xsl:if test="$units or $unitsURL">
                                                                (
                                                                <xsl:if test="$units">
                                                                    <xsl:value-of select="$units" />
                                                                </xsl:if>
                                                                <xsl:if test="$units and $unitsURL"> : </xsl:if>
                                                                <xsl:if test="unitsURL">
                                                                    <xsl:value-of select="$unitsURL" />
                                                                </xsl:if>
                                                                )
                                                            </xsl:if>
                                                        </td>
                                                    </tr>
                                                </xsl:for-each>
                                                <!-- Selecting results -->
                                                <xsl:for-each select="gmd19157:valueType/gco:RecordType">
                                                    <!-- should be just one -->
                                                    <tr>
                                                        <td valign="top" colspan="1">
                                                            <b>Result Type</b>
                                                        </td>
                                                        <td colspan="3">
                                                            <xsl:value-of select="." /> (
                                                            <a>
                                                                <xsl:attribute name="href">
                                                                    <xsl:value-of select="./@xlink:href" />
                                                                </xsl:attribute>
                                                                <xsl:value-of select="./@xlink:href" />
                                                            </a>)
                                                        </td>
                                                    </tr>
                                                </xsl:for-each>
                                                <!-- Selecting result types -->
                                            </xsl:if>
                                            <!-- what to do if it's a quantitative result -->
                                            <xsl:if test="$thisName5 = 'gmd19157:DQ_ConformanceResult'">
                                            </xsl:if>
                                            <xsl:if test="$thisName5 = 'gmd19157:DQ_DescriptiveResult'">
                                            </xsl:if>
                                        </xsl:for-each>
                                        <!-- Selecting results -->
                                        <!-- see if there is any metaquality relating to this record -->
                                        <xsl:for-each select="key('referencedMetaQuality',$thisReportID)">
                                            <xsl:for-each select="../..">
                                                <!-- only one grandparent!! -->
                                                <xsl:for-each select="./gmd19157:DQ_Confidence">
                                                    <!--TODO -->
                                                </xsl:for-each>
                                                <xsl:for-each select="./gmd19157:DQ_Representativity">
                                                    <!--TODO -->
                                                </xsl:for-each>
                                                <xsl:for-each select="./gmd19157:DQ_Homogeneity">
                                                    <!--TODO -->
                                                </xsl:for-each>
                                                <xsl:for-each select="./gvq:GVQ_Traceability">
                                                    <tr>
                                                        <th colspan="4">
                                                            <b>Traceability of this quality report</b>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4">
                                                            <xsl:call-template name="styleTraceability" />
                                                        </td>
                                                    </tr>
                                                </xsl:for-each>
                                            </xsl:for-each>
                                            <!-- Selecting types of MQ_element -->
                                        </xsl:for-each>
                                        <!-- Selecting metaquality elements that reference this report -->
                                    </table>
									<br />
									<br />
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:for-each>
                        <!-- Selecting reports -->
                    </xsl:for-each>
                    <!-- Selecting dataQualityInfo elements - usually just one report for each... -->
                </xsl:if>
			</div>
            </body>
        </html>
    </xsl:template>
    <!-- Separate template for content info -->
    <xsl:template name="displayContentInformation">
        <h3>
            <xsl:value-of select=".//gmd:contentType/gmd:MD_CoverageContentTypeCode" />
        </h3>
        <table border="1" cellpadding="2" cellspacing="2">
            <tr>
                <th valign="top">Name</th>
                <th valign="top">Description</th>
                <th valign="top">Type</th>
            </tr>
            <xsl:for-each select=".//gmd:dimension">
                <xsl:sort select=".//gmd:sequenceIdentifier/gco:MemberName/gco:aName/gco:CharacterString" />
                <tr>
                    <td>
                        <xsl:value-of select=".//gmd:sequenceIdentifier/gco:MemberName/gco:aName/gco:CharacterString" />
                    </td>
                    <td>
                        <xsl:value-of select=".//gmd:descriptor/gco:CharacterString|./gmi:MI_Band/gmd:description/gco:CharacterString" />
                    </td>
                    <td>
                        <xsl:value-of select=".//gmd:sequenceIdentifier/gco:MemberName/gco:attributeType/gco:TypeName/gco:aName/gco:CharacterString" />
                    </td>
                </tr>
            </xsl:for-each>
        </table>
    </xsl:template>
    <!-- Template for cutting down a scope object and putting it into a table cell -->
    <xsl:template match="gmd19157:DQ_Scope">
        <xsl:variable name="scopeCode" select="gmd19157:level/gmd:MD_ScopeCode/@codeListValue" />
        <xsl:variable name="scopeDesc" select="gmd19157:level/gmd:MD_ScopeDescription/gmd:attributes" />
        <b>Level : </b>
        <xsl:value-of select="$scopeCode" />  -
        <xsl:value-of select="$scopeDesc" />
        <xsl:apply-templates select="gmd19157:extent/gmd:EX_Extent" />
    </xsl:template>
    <!-- Template for compressing extent into a few paras suitable for entry into a table field -->
    <xsl:template match="gmd:EX_Extent">
        <xsl:variable name="Link" select="./@xlink:href" />
        <xsl:variable name="LinkTitle" select="./@xlink:title" />
        <xsl:variable name="Description" select="./gmd:description" />
        <xsl:variable name="West" select="./gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal" />
        <xsl:variable name="East" select="./gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal" />
        <xsl:variable name="South" select="./gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal" />
        <xsl:variable name="North" select="./gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal" />
        <xsl:variable name="Start" select="./gmd:temporalElement/gmd:EX_TemporalExtent/gmd:extent/gml:TimePeriod/gml:beginPosition" />
        <xsl:variable name="End" select="./gmd:temporalElement/gmd:EX_TemporalExtent/gmd:extent/gml:TimePeriod/gml:endPosition" />
        <xsl:if test="$Description">
            <br />
            <b>Description: </b>
            <xsl:value-of select="$Description" />
        </xsl:if>
        <br />
        <b>Spatial extent --- West: </b>
        <xsl:value-of select="$West" /> -
        <b>East: </b>
        <xsl:value-of select="$East" /> -
        <b>South: </b>
        <xsl:value-of select="$South" /> -
        <b>North: </b>
        <xsl:value-of select="$North" />
        <xsl:if test="$Start">
            <br />
            <h5> --- Temporal extent</h5>
            <b>Start: </b>
            <xsl:value-of select="$Start" /> -
            <b>End: </b>
            <xsl:value-of select="$End" />
        </xsl:if>
        <xsl:if test="$Link">
            <br />
            <a>
                <xsl:attribute name="href">
                    <xsl:value-of select="$Link" />
                </xsl:attribute>
                <xsl:value-of select="$LinkTitle" />
            </a>
        </xsl:if>
    </xsl:template>
    <!-- Template for rendering an UncertML confusion matrix - requires XSLT 2.0 -->
    <xsl:template match="un:ConfusionMatrix">
        <xsl:variable name="tokenizedCounts" select="tokenize(normalize-space(un:counts),' ')" />
        <xsl:variable name="tokenizedTargets" select="tokenize(normalize-space(un:targetCategories),' ')" />
        <xsl:variable name="tokenizedSources" select="tokenize(normalize-space(un:sourceCategories),' ')" />
        <div style="font-size:9px">
            <table bgcolor="white">
                <tr>
                    <th></th>
                    <xsl:for-each select="$tokenizedSources">
                        <th>
                            <xsl:value-of select="." />
                        </th>
                    </xsl:for-each>
                </tr>
                <xsl:variable name="currentValueIndex" select="0" />
                <!-- find out how many rows are required -->
                <xsl:variable name="rowNum" select="count($tokenizedTargets)" />
                <xsl:variable name="colNum" select="count($tokenizedSources)" />
                <xsl:for-each select="$tokenizedCounts">
                    <xsl:variable name="currentTokenIndex" select="position()" />
                    <xsl:choose>
                        <xsl:when test="$currentTokenIndex = 1">
                            <xsl:element name="tr" />
                            <td class="leftcolumn">
                                <b>
                                    <xsl:value-of select="$tokenizedTargets[1]" />
                                </b>
                            </td>
                            <td class="matrix">
                                <xsl:value-of select="." />
                            </td>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:variable name="currentTokenIndex_zeroed" select="position() -1" />
                            <xsl:choose>
                                <xsl:when test="$currentTokenIndex_zeroed mod $colNum = 0">
                                    <xsl:variable name="currentRowIndex" select="($currentTokenIndex_zeroed div $colNum) + 1" />
                                    <xsl:element name="tr" />
                                    <td class="leftcolumn">
                                        <b>
                                            <xsl:value-of select="$tokenizedTargets[$currentRowIndex]" />
                                        </b>
                                    </td>
                                    <td class="matrix">
                                        <xsl:value-of select="." />
                                    </td>
                                </xsl:when>
                                <xsl:otherwise>
                                    <td class="matrix">
                                        <xsl:value-of select="." />
                                    </td>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </table>
			<br />
        </div>
    </xsl:template>
    <!-- Template for styling process steps from a lineage item -->
    <xsl:template name="styleTraceability">
        <p>
            <b>Evaluation method: </b>
            <xsl:value-of select="gmd19157:evaluation//gmd19157:evaluationMethodDescription/gco:CharacterString" />
        </p>
        <p>
            <p>
                <b>Statement: </b>
                <xsl:value-of select="gmd19157:result//gmd19157:statement/gco:CharacterString" />
            </p>
            <xsl:for-each select="gvq:trace//gmd19157:processStep/gmd19157:LI_ProcessStep">
                <p>
                    <b>Process step
                        <xsl:value-of select="position()" />
                    </b>
                    <br />
                    <b>Description: </b>
                    <xsl:value-of select="gmd19157:description/gco:CharacterString" />
                    <br />
                    <b>Rationale: </b>
                    <xsl:value-of select="gmd19157:rationale/gco:CharacterString" />
                </p>
            </xsl:for-each>
        </p>
    </xsl:template>
</xsl:stylesheet>