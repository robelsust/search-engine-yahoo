<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\Core\Media\MediaFactory;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Yahoo\NaturalResultType;

class ClassicalResult implements ParsingRuleInterface
{

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {
      
       
      //  if ($node->getAttribute('class') == 'g') {
          //  if ($dom->cssQuery('.rc', $node)->length == 1) {
                return self::RULE_MATCH_MATCHED;
         //   }
       // }
       // return self::RULE_MATCH_NOMATCH;
    }

    protected function parseNode(YahooDom $dom, \DomElement $node)
    {
         
        // find the title/url
        /* @var $aTag \DOMElement */
        $aTag=$dom
            ->xpathQuery("descendant::h3[@class='title'][1]/a", $node)
            ->item(0);
        if (!$aTag) {
            return;
        }
        
        $r_url = $aTag->getAttribute('href');
        $pos_to_start = strpos($r_url, 'RU=');
        $pos_to_start = $pos_to_start+3;
        $rs_url = substr($r_url, $pos_to_start);
        $end_position = strpos($rs_url, 'RK=');
        $rs_url = rawurldecode(substr($rs_url, 0,$end_position));
        /*
        echo $rs_url;
        echo '<br>';
        $aLink=$dom
            ->xpathQuery("descendant::h3[@class='title'][1]//a/@href", $node)
            ->item(0);
        if (!$aLink) {
            return;
        }
*/

 /*
        $destinationTag = $dom
            ->cssQuery('div.f.kv>cite', $node)
            ->item(0);
        */
        $destinationTag = $dom
            ->xpathQuery("descendant::span[@class='fz-ms']", $node)
            ->item(0);
       

        $descriptionTag = $dom
            ->xpathQuery("descendant::div[@class='compText']", $node)
            ->item(0);

        $test_arry = array();
        $test_arry[] =array(
            'title'   => $aTag->nodeValue,
            'url'     => $rs_url,
            'destination' => $destinationTag ? $destinationTag->nodeValue : null,
            // trim needed for mobile results coming with an initial space
            'description' => $descriptionTag ? trim($descriptionTag->nodeValue) : null
        );
        
       // print_r($test_arry);

        return [
            'title'   => $aTag->nodeValue,
            'url'     => $rs_url,//$dom->getUrl()->resolveAsString($rs_url),
            'destination' => $destinationTag ? $destinationTag->nodeValue : null,
            // trim needed for mobile results coming with an initial space
            'description' => $descriptionTag ? trim($descriptionTag->nodeValue) : null
        ];
    }

    /**
     * If isLarge() matched, this will parse the content of site links
     * @param YahooDom $dom
     * @param \DomElement $node
     * @return \Closure
     */
    protected function parseSiteLink(YahooDom $dom, \DomElement $node)
    {
        
        return function () use ($dom, $node) {
            $items = $dom->cssQuery('.mslg .sld', $node);
            $siteLinksData = [];
            foreach ($items as $item) {
                $siteLinksData[] = new BaseResult(NaturalResultType::CLASSICAL_SITELINK, [
                    'title' => function () use ($dom, $item) {
                        return $dom->cssQuery('h3.title a', $item)
                            ->getNodeAt(0)
                            ->getNodeValue();
                    },
                    'description' => function () use ($dom, $item) {
                        return $dom->cssQuery('.compText ', $item)
                            ->getNodeAt(0)
                            ->getNodeValue();
                    },
                    'url' => function () use ($dom, $item) {
                        return $dom->cssQuery('h3.title a', $item)
                            ->getNodeAt(0)
                            ->getAttribute('href');
                    },
                ]);
            }
            return $siteLinksData;
        };
    }

    /**
     * Check if has site links. Might be overriden by subparser like ClassicalCard
     * @param GoogleDom $dom
     * @param \DomElement $node
     * @return bool
     */
    protected function isLarge(YahooDom $dom, \DomElement $node)
    {
        return $dom->cssQuery('.nrgt', $node)->length == 1;
    }

    public function parse(YahooDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $data = $this->parseNode($dom, $node);

        $resultTypes = [NaturalResultType::CLASSICAL];


        // CLASSICAL RESULT MIGHT BE ENLARGED WITH SITELINKS
        if ($this->isLarge($dom, $node)) {
            $data['sitelinks'] = $this->parseSiteLink($dom, $node);
            $resultTypes[] = NaturalResultType::CLASSICAL_LARGE;
        }


        // classical result can have a video thumbnail
        $thumb = $dom->getXpath()
            ->query("descendant::g-img[@class='_ygd']/img", $node)
            ->item(0);

        if ($thumb) {
            $resultTypes[] = NaturalResultType::CLASSICAL_ILLUSTRATED;

            $data['thumb'] = function () use ($thumb) {
                if ($thumb) {
                    return MediaFactory::createMediaFromSrc($thumb->getAttribute('src'));
                } else {
                    return null;
                }
            };
        }

        $videoDuration = $dom->cssQuery('.vdur', $node);
        if ($videoDuration->length == 1) {
            $resultTypes[] = NaturalResultType::CLASSICAL_VIDEO;
            $data['videoLarge'] = false;
        }


        $item = new BaseResult($resultTypes, $data);
        $resultSet->addItem($item);
    }
}
