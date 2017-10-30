<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0"
    xmlns="http://www.w3.org/1999/xhtml"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:fr="maarch.org:laabs:recordsManagement"
    exclude-result-prefixes="xsl xsd fr">
    
    <xsl:template match="fr:accessRule">
        <hr/>
        <h4 data-translate-catalog="recordsManagement/messages">Access rule</h4>
        <dl class="dl-horizontal" data-translate-catalog="recordsManagement/messages">
            <dt>Code</dt>
            <dd translate="no"><xsl:value-of select="fr:code"/></dd>

            <dt>Duration</dt>
            <dd translate="no"><xsl:apply-templates select="fr:duration"/></dd>

            <dt>Start date</dt>
            <dd translate="no"><xsl:apply-templates select="fr:startDate"/></dd>

            
            <dt>Originator identifier</dt>
            <dd translate="no"><xsl:value-of select="fr:originatingAgency/fr:Identifier"/></dd>
            
            <dt>Originator Name</dt>
            <dd translate="no"><xsl:value-of select="fr:originatingAgency/fr:OrganizationDescriptiveMetadata"/></dd>
        </dl>
    </xsl:template>

</xsl:stylesheet>