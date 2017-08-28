<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Media\MediaFactory;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Yahoo\NaturalResultType;

class ClassicalWithLargeVideo implements ParsingRuleInterface
{

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getAttribute('class') == 'g mnr-c g-blk'
            && $dom->cssQuery('.knowledge-block__video-nav-block', $node)->length == 1
        ) {
            return self::RULE_MATCH_MATCHED;
        } else {
            return self::RULE_MATCH_NOMATCH;
        }
    }

    public function parse(YahooDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $xpath = $dom->getXpath();
        $aTag = $xpath->query("descendant::h3[@class='r'][1]/a", $node)->item(0);

        if (!$aTag) {
            return false;
        }

        $destinationTag = $xpath
            ->query("descendant::div[@class='f kv _SWb']/cite", $node)
            ->item(0);

        $data = [
            'title'   => $aTag->nodeValue,
            'url'     => $dom->getUrl()->resolveAsString($aTag->getAttribute('href')),
            'destination' => $destinationTag ? $destinationTag->nodeValue : null,
            'description' => null,
            'videoLarge'  => true,
            'thumb' => null,
            'videoCover'  => function () use ($dom, $node) {
                $imageTag = $dom
                    ->cssQuery('._ELb img', $node)
                    ->item(0);
                if ($imageTag) {
                    return MediaFactory::createMediaFromSrc($imageTag->getAttribute('src')); // TODO 1p gif ?
                } else {
                    return null;
                }
            }
        ];

        $resultSet->addItem(new BaseResult([NaturalResultType::CLASSICAL_VIDEO, NaturalResultType::CLASSICAL], $data));
    }
}
