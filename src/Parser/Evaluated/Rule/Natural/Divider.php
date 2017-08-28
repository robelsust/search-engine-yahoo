<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface;

class Divider implements \Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface
{

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {
       // print_r($node);exit;
        /**
         * Divider should not be parsed and for performance we just skip the parsing
         */
        if ('hr' == $node->tagName || 'rgsep' == $node->getAttribute('class')) {
            return self::RULE_MATCH_STOP;
        }
    }

    public function parse(YahooDom $yahooDOM, \DomElement $group, IndexedResultSet $resultSet)
    {
    }
}
