<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural;

use Serps\Core\Media\MediaFactory;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Yahoo\NaturalResultType;

class ImageGroupCarousel implements \Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface
{

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($dom->cssQuery('._ekh image-viewer-group g-scrolling-carousel', $node)->length == 1) {
            return self::RULE_MATCH_MATCHED;
        } else {
            return self::RULE_MATCH_NOMATCH;
        }
    }
    public function parse(YahooDom $yahooDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $item = [
            'images' => function () use ($node, $yahooDOM) {
                $items = [];

                $imageNodes = $yahooDOM->cssQuery('.rg_ul>._sqh g-inner-card', $node);
                foreach ($imageNodes as $imageNode) {
                    $items[] = $this->parseItem($yahooDOM, $imageNode);
                }

                return $items;
            },
            'isCarousel' => true,
            'moreUrl' => function () use ($node, $yahooDOM) {
                $a = $yahooDOM->cssQuery('g-tray-header ._Nbi a');
                $a = $a->item(0);
                if ($a instanceof \DOMElement) {
                    return $yahooDOM->getUrl()->resolveAsString($a->getAttribute('href'));
                }
                return null;
            }
        ];

        $resultSet->addItem(new BaseResult(NaturalResultType::IMAGE_GROUP, $item));
    }
    /**
     * @param GoogleDOM $googleDOM
     * @param \DOMElement $imgNode
     * @return array
     *
     */
    private function parseItem(YahooDOM $yahooDOM, \DOMElement $imgNode)
    {
        $data =  [
            'sourceUrl' => function () use ($imgNode, $yahooDOM) {
                $node = $yahooDOM->cssQuery('.rg_meta', $imgNode)->item(0);
                if (!$node) {
                    return null;
                }
                $url = $yahooDOM->getJsonNodeProperty('ru', $node);
                return $url;
            },
            'targetUrl' => function () use ($imgNode, $yahooDOM) {
                // not available for mobile results
                return null;
            },
            'image' => function () use ($imgNode, $yahooDOM) {
                // TODO: maybe parse from javascript source
                $img = $yahooDOM->cssquery('.iuth>img')->item(0);
                if (!$img) {
                    return null;
                }
                return mediafactory::createmediafromsrc($img->getattribute('src'));
            },
        ];

        return new BaseResult(NaturalResultType::IMAGE_GROUP_IMAGE, $data);
    }
}
