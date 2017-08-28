<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated;

use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\SearchEngine\Yahoo\Parser\AbstractParser;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\AnswerBox;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Classical\ClassicalResult;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Divider;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Flight;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\ImageGroup;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\ImageGroupCarousel;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\InTheNews;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Classical\LargeClassicalResult;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\KnowledgeCard;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Map;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResult;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\SearchResultGroup;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\TweetsCarousel;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Classical\ClassicalWithLargeVideo;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\VideoGroup;

/**
 * Parses natural results from a mobile google SERP
 */
class MobileNaturalParser extends AbstractParser
{

    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
			
        return [
            new Divider(),
            new SearchResultGroup(),
            new ClassicalCardsResult(),
            new ImageGroupCarousel(),
            new VideoGroup(),
            new ImageGroup(),
            new KnowledgeCard()
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(YahooDom $yahooDom)
    {
	
        $xpathObject = $yahooDom->getXpath();
        $xpathElementGroups = "//div[@id = 'web']";
		//return $yahooDom->xpathQuery("//div[@id = 'web']/*[@id = 'main']/*");

		return $xpathObject->query($xpathElementGroups);
    }
}
