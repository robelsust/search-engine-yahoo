<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural;

use Serps\Core\Media\MediaFactory;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Yahoo\NaturalResultType;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface;

/**
 * This rule extracts video groups as present on mobile results
 */
class VideoGroup implements ParsingRuleInterface
{

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($dom->cssQuery('._Fzo', $node)->length == 1) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(YahooDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {

        $item = [

            'videos'    => function () use ($node, $dom) {
                $items = [];
                $nodes = $dom->cssQuery('._ERj', $node);

                foreach ($nodes as $node) {
                    $items[] = new BaseResult(NaturalResultType::VIDEO_GROUP_VIDEO, [
                        'image' => function () use ($node, $dom) {
                            $data = $dom->cssQuery('g-img img', $node)->getNodeAt(0)->getAttribute('src');
                            return MediaFactory::createMediaFromSrc($data);
                        },
                        'title' => function () use ($node, $dom) {
                            return $dom->cssQuery('._IRj', $node)->getNodeAt(0)->getNodeValue();
                        },
                        'url' => function () use ($node, $dom) {
                            return $dom->cssQuery('g-inner-card a', $node)->getNodeAt(0)->getAttribute('href');
                        }
                    ]);
                }

                return $items;
            }

        ];

        $resultSet->addItem(new BaseResult(NaturalResultType::VIDEO_GROUP, $item));
    }
}
