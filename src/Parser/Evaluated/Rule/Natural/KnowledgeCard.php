<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural;

use Serps\Core\Dom\DomElement;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Yahoo\NaturalResultType;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface;

class KnowledgeCard implements ParsingRuleInterface
{

    public function match(YahooDom $dom, DomElement $node)
    {
        if ($node->hasClass('mnr-c') && $node->hasClass('kno-kp')) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(YahooDom $yahooDOM, \DomElement $node, IndexedResultSet $resultSet)
    {

        $data = [
            'title' => function () use ($yahooDOM, $node) {
                $item = $yahooDOM->cssQuery('._OKe ._Q1n ._sdf');
                return $item->getNodeAt(0)->getNodeValue();
            },
            'shortDescription' => function () use ($yahooDOM, $node) {
                $item = $yahooDOM->cssQuery('._OKe ._Q1n ._gdf');
                return $item->getNodeAt(0)->getNodeValue();
            }
        ];

        $resultSet->addItem($a = new BaseResult(NaturalResultType::KNOWLEDGE, $data));
    }
}
