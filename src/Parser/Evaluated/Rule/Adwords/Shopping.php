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

class Shopping implements ParsingRuleInterface
{

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        $class = $node->getAttribute('class');

        if (strpos(' ' . $class . ' ', ' _oc ')) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }
    public function parse(YahooDom $yahooDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $item = [
            'products' => function () use ($yahooDOM, $node) {
                $items = [];
                $xpathCards = Css::toXPath('.pla-unit');
                $productNodes = $yahooDOM->getXpath()->query($xpathCards, $node);
                foreach ($productNodes as $productNode) {
                    $items[] = $this->parseItem($yahooDOM, $productNode);
                }
                return $items;
            }
        ];


        $resultSet->addItem(new BaseResult(AdwordsResultType::SHOPPING_GROUP, $item));
    }

    public function parseItem(YahooDom $yahooDOM, \DOMNode $node)
    {

        return new BaseResult(AdwordsResultType::SHOPPING_GROUP_PRODUCT, [
            'title' => function () use ($yahooDOM, $node) {
                $aTag = $yahooDOM->getXpath()->query(Css::toXPath('.pla-unit-title-link'), $node)->item(0);
                if (!$aTag) {
                    return null;
                }
                return $aTag->nodeValue;
            },
            'url' => function () use ($node, $yahooDOM) {
                $aTag = $yahooDOM->getXpath()->query(Css::toXPath('.pla-unit-title-link'), $node)->item(0);
                if (!$aTag) {
                    return $yahooDOM->getUrl()->resolve('/');
                }
                return $yahooDOM->getUrl()->resolveAsString($aTag->getAttribute('href'));
            },
            'image' => function () use ($node, $yahooDOM) {
                $imgTag = $yahooDOM->getXpath()->query(
                    Css::toXPath('.pla-unit-img-container-link img'),
                    $node
                )->item(0);

                if (!$imgTag) {
                    return null;
                }
                return $imgTag->getAttribute('src');
            },
            'target' => function () use ($node, $yahooDOM) {
                $aTag = $yahooDOM->getXpath()->query(
                    Css::toXPath('div._mC span.a'),
                    $node
                )->item(0);

                if (!$aTag) {
                    return null;
                }
                return $aTag->nodeValue;
            },
            'price' => function () use ($node, $yahooDOM) {
                $priceTag = $yahooDOM->getXpath()->query(
                    Css::toXPath('._QD._pvi'),
                    $node
                )->item(0);

                if (!$priceTag) {
                    return null;
                }
                return $priceTag->nodeValue;
            }
        ]);
    }
}
