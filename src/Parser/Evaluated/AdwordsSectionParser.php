<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated;

use Serps\SearchEngine\Yahoo\AdwordsResultType;
use Serps\SearchEngine\Yahoo\AdwordsSectionResultSet;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\SearchEngine\Yahoo\Parser\AbstractParser;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Adwords\AdwordsItem;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Adwords\Shopping;

/**
 * Parses adwords results from a Yahoo SERP
 */
class AdwordsSectionParser extends AbstractParser
{

    protected $pathToItems;
    protected $location;

    /**
     * @param $pathToItems
     */
    public function __construct($pathToItems, $location)
    {
        $this->pathToItems = $pathToItems;
        $this->location = $location;
    }

    protected function createResultSet(YahooDom $yahooDom)
    {
        return new AdwordsSectionResultSet($this->location);
    }


    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new AdwordsItem(),
            new Shopping()
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(YahooDom $yahooDom)
    {
        $xpathObject = $yahooDom->getXpath();
        $xpathElementGroups = $this->pathToItems;
        return $xpathObject->query($xpathElementGroups);
    }
}
