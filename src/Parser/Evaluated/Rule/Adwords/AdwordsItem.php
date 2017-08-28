<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Adwords;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Yahoo\AdwordsResultType;
use Serps\Core\Dom\Css;
use Serps\SearchEngine\Yahoo\NaturalResultType;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface;

class AdwordsItem implements ParsingRuleInterface
{

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getAttribute('class') == 'ads-ad') {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }
    public function parse(YahooDom $yahooDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $item = [
            'title' => function () use ($yahooDOM, $node) {
                $aTag = $yahooDOM->getXpath()->query('descendant::h3/a[2]', $node)->item(0);
                if (!$aTag) {
                    return null;
                }
                return $aTag->nodeValue;
            },
            'url' => function () use ($node, $yahooDOM) {
                $aTag = $yahooDOM->getXpath()->query('descendant::h3/a[2]', $node)->item(0);
                if (!$aTag) {
                    return $yahooDOM->getUrl()->resolve('/');
                }

                return $yahooDOM->getUrl()->resolveAsString($aTag->getAttribute('href'));
            },
            'visurl' => function () use ($node, $yahooDOM) {
                $aTag = $yahooDOM->getXpath()->query(
                    Css::toXPath('div.ads-visurl>cite'),
                    $node
                )->item(0);

                if (!$aTag) {
                    return null;
                }
                return $aTag->nodeValue;
            },
            'description' => function () use ($node, $yahooDOM) {
                $aTag = $yahooDOM->getXpath()->query(
                    Css::toXPath('div.ads-creative'),
                    $node
                )->item(0);

                if (!$aTag) {
                    return null;
                }
                return $aTag->nodeValue;
            },
        ];

        $resultSet->addItem(new BaseResult(AdwordsResultType::AD, $item));
    }
}
