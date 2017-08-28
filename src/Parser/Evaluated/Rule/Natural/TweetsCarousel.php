<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural;

use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Yahoo\NaturalResultType;

class TweetsCarousel implements ParsingRuleInterface
{

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {

        if ($dom->cssQuery('.g ._BOf', $node)->length) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(YahooDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $xpath = $dom->getXpath();

        /* @var $aTag \DOMElement */
        $aTag=$xpath
            ->query("descendant::h3[@class='r'][1]//a", $node)
            ->item(0);

        if ($aTag) {
            $title = $aTag->nodeValue;

            preg_match('/@([A-Za-z0-9_]{1,15})/', $title, $match);

            $data = [
                'title'   => $title,
                'url'     => $aTag->getAttribute('href'),
                'user'    => isset($match[0]) ? $match[0] : null
            ];

            $item = new BaseResult(NaturalResultType::TWEETS_CAROUSEL, $data);
            $resultSet->addItem($item);
        }
    }
}
