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
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\InTheNews;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Map;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResult;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\SearchResultGroup;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\TopStoriesCarousel;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\TopStoriesVertical;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\TweetsCarousel;
use Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Classical\ClassicalWithLargeVideo;

/**
 * Parses natural results from a google SERP
 */
class NaturalParser extends AbstractParser
{

    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new Divider(),
            new SearchResultGroup(),
            new ClassicalResult(),
            new ClassicalCardsResult(),
            new ImageGroup(),
            new TopStoriesCarousel(),
            new TopStoriesVertical(),
            new TweetsCarousel(),
            new ClassicalWithLargeVideo(),
            new InTheNews(),
            new Map(),
            new AnswerBox(),
            new Flight(),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(YahooDom $yahooDom)
    {
      
		// return $yahooDom->xpathQuery("//div[@id = 'main']/*[@id = 'web']/*");
        return $yahooDom->xpathQuery("//ol[contains(@class, 'searchCenterMiddle')]/li");
       // print_r($yahooDom->xpathQuery("//ol//li"));exit;
		// return $yahooDom->xpathQuery("//ol[@class = 'searchCenterMiddle']");
    }
}
