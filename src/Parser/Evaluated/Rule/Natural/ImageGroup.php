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

class ImageGroup implements \Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface
{

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->hasAttribute('id') && $node->getAttribute('id') == 'imagebox_bigimages') {
            return self::RULE_MATCH_MATCHED;
        } else {
            return self::RULE_MATCH_NOMATCH;
        }
    }
    public function parse(YahooDom $yahooDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $item = [
            'images' => [],
            'isCarousel' => false,
            'moreUrl' => function () use ($node, $yahooDOM) {
                $aTag = $yahooDOM->getXpath()->query('descendant::div[@class="_Icb _kk _wI"]/a', $node)->item(0);
                if (!$aTag) {
                    return $yahooDOM->getUrl()->resolve('/');
                }
                return $yahooDOM->getUrl()->resolveAsString($aTag->getAttribute('href'));
            }
        ];

        // TODO: detect no image (google dom update)
        $imageNodes = $yahooDOM->cssQuery('.rg_ul>div._ZGc a', $node);
        foreach ($imageNodes as $imgNode) {
            $item['images'][] = $this->parseItem($yahooDOM, $imgNode);
        }
        $resultSet->addItem(new BaseResult(NaturalResultType::IMAGE_GROUP, $item));
    }
    /**
     * @param GoogleDOM $googleDOM
     * @param \DOMElement $imgNode
     * @return array
     */
    private function parseItem(YahooDOM $yahooDOM, \DOMElement $imgNode)
    {
        $data =  [
            'sourceUrl' => function () use ($imgNode, $yahooDOM) {
                $img = $yahooDOM->getXpath()->query('descendant::img', $imgNode)->item(0);
                if (!$img) {
                    return $yahooDOM->getUrl()->resolve('/');
                }
                return $yahooDOM->getUrl()->resolveAsString($img->getAttribute('title'));
            },
            'targetUrl' => function () use ($imgNode, $yahooDOM) {
                return $yahooDOM->getUrl()->resolveAsString($imgNode->getAttribute('href'));
            },
            'image' => function () use ($imgNode, $yahooDOM) {
                $img = $yahooDOM->getXpath()->query('descendant::img', $imgNode)->item(0);
                if (!$img) {
                    return '';
                }
                return MediaFactory::createMediaFromSrc($img->getAttribute('src'));
            },
        ];

        return new BaseResult(NaturalResultType::IMAGE_GROUP_IMAGE, $data);
    }
}
