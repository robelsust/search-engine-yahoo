<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser;

use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\Core\Serp\IndexedResultSet;

interface ParsingRuleInterface
{
    const RULE_MATCH_MATCHED = 1;
    const RULE_MATCH_NOMATCH = 2;
    const RULE_MATCH_STOP = 3;

    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node);
    public function parse(YahooDom $dom, \DomElement $node, IndexedResultSet $resultSet);
}
