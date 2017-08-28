<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Yahoo\NaturalResultType;

class InTheNews implements \Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface
{

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        $child = $node->firstChild;
        if (!$child || !($child instanceof \DOMElement)) {
            return self::RULE_MATCH_NOMATCH;
        }
        if ($child->getAttribute('class') == 'mnr-c _yE') {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(YahooDom $yahooDOM, \DomElement $group, IndexedResultSet $resultSet)
    {
        $item = [
            'news' => []
        ];
        $xpathCards = "div/div[contains(concat(' ',normalize-space(@class),' '),' card-section ')]";
        $cardNodes = $yahooDOM->getXpath()->query($xpathCards, $group);

        foreach ($cardNodes as $cardNode) {
            $item['news'][] = $this->parseItem($yahooDOM, $cardNode);
        }

        $resultSet->addItem(new BaseResult(NaturalResultType::IN_THE_NEWS, $item));
    }
    /**
     * @param GoogleDOM $googleDOM
     * @param \DomElement $node
     * @return array
     */
    protected function parseItem(YahooDOM $yahooDOM, \DomElement $node)
    {
        $card = [];
        $xpathTitle = "descendant::a[@class = '_Dk']";
        $aTag = $yahooDOM->getXpath()->query($xpathTitle, $node)->item(0);
        if ($aTag) {
            $card['title'] = $aTag->nodeValue;
            $card['url'] = $aTag->getAttribute('href');
            $card['description'] = function () use ($yahooDOM, $node) {
                $span = $yahooDOM->getXpath()->query("descendant::span[@class='_dwd st s std']", $node);
                if ($span && $span->length > 0) {
                    return  $span->item(0)->nodeValue;
                }
                return null;
            };
        }
        return new BaseResult('', $card);
    }
}
