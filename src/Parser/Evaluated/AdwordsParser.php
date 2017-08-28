<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated;

use Serps\Core\Serp\CompositeResultSet;
use Serps\SearchEngine\Yahoo\AdwordsResultType;
use Serps\Core\Dom\Css;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use \Serps\SearchEngine\Yahoo\Parser\Evaluated\AdwordsSectionParser;

class AdwordsParser
{

    /**
     * @param GoogleDom $googleDom
     * @return CompositeResultSet
     */
    public function parse(YahooDom $yahooDom)
    {
        $parsers = [
            // Adwords top
            new AdwordsSectionParser(
                Css::toXPath('div#tads li.ads-ad, div#tvcap ._oc'),
                AdwordsResultType::SECTION_TOP
            ),

            // Adwords bottom
            new AdwordsSectionParser(
                "descendant::div[@id = 'bottomads']//li[@class='ads-ad']",
                AdwordsResultType::SECTION_BOTTOM
            )
        ];


        $resultsSets = new CompositeResultSet();

        foreach ($parsers as $parser) {
            /* @var $parser \Serps\SearchEngine\Google\Parser\Evaluated\AdwordsSectionParser */
            $resultsSets->addResultSet(
                $parser->parse($yahooDom)
            );
        }

        return $resultsSets;
    }
}
