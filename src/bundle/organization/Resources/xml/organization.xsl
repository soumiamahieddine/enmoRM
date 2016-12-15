<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0"
    xmlns="http://www.w3.org/1999/xhtml"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:fr="maarch.org:laabs:organization"
    exclude-result-prefixes="xsl xsd fr">

    <xsl:template match="fr:organization">
        <dl class="dl-horizontal" data-translate-catalog="organization/messages">
            <dt>Organization name</dt>
            <dd translate="no"><xsl:value-of select="fr:orgName"/></dd>

            <xsl:if test="fr:legalClassification">
                <dt>Legal classification</dt>
                <dd translate="no"><xsl:value-of select="fr:legalClassification"/></dd>
            </xsl:if>

            <xsl:if test="fr:taxIdentifier">
                <dt>Tax identifier</dt>
                <dd translate="no"><xsl:value-of select="fr:taxIdentifier"/></dd>
            </xsl:if>

            <xsl:if test="fr:registrationNumber">
                <dt>Registration number</dt>
                <dd translate="no"><xsl:value-of select="fr:registrationNumber"/></dd>
            </xsl:if>

            <xsl:if test="fr:companyName">
                <dt>Company name</dt>
                <dd translate="no"><xsl:value-of select="fr:companyName"/></dd>
            </xsl:if>

            <xsl:if test="fr:year">
                <dt>Year</dt>
                <dd translate="no"><xsl:value-of select="fr:year"/></dd>
            </xsl:if>

            <xsl:if test="fr:companyCode">
                <dt>Company code</dt>
                <dd translate="no"><xsl:value-of select="fr:companyCode"/></dd>
            </xsl:if>

            <xsl:if test="fr:address">
                <dt>Address</dt>
                <xsl:if test="fr:address/fr:BuildingNumber">
                    <dd translate="no"><xsl:value-of select="fr:address/fr:BuildingNumber"/></dd>
                </xsl:if>
                <xsl:if test="fr:address/fr:StreetName">
                    <dd translate="no"><xsl:value-of select="fr:address/fr:StreetName"/></dd>
                </xsl:if>
                <xsl:if test="fr:address/fr:BlockName">
                    <dd translate="no"><xsl:value-of select="fr:address/fr:BlockName"/></dd>
                </xsl:if>
                <xsl:if test="fr:address/fr:CityName">
                    <dd translate="no"><xsl:value-of select="fr:address/fr:CityName"/></dd>
                </xsl:if>
                <xsl:if test="fr:address/fr:Postcode">
                    <dd translate="no"><xsl:value-of select="fr:address/fr:Postcode"/></dd>
                </xsl:if>
                <xsl:if test="fr:address/fr:Country">
                    <dd translate="no"><xsl:value-of select="fr:address/fr:Country"/></dd>
                </xsl:if>
            </xsl:if>
        </dl>
    </xsl:template>
    
</xsl:stylesheet>