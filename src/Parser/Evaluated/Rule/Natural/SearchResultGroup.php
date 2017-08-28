<?php
/**
 * @license see LICENSE
 */
namespace Serps\SearchEngine\Yahoo\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Yahoo\Page\YahooDom;
use Serps\SearchEngine\Yahoo\Parser\ParsingRuleInterface;

/**
 * This is a group of results that need to be sub-parsed
 */
class SearchResultGroup implements ParsingRuleInterface
{
    public function match(YahooDom $dom, \Serps\Core\Dom\DomElement $node)
    {	
      
        $class = $node->getAttribute('class');
       
       // if ('web' == $class) {
           // return $node->childNodes;
       // } else {
           return self::RULE_MATCH_NOMATCH;
       // }
    }

    public function parse(YahooDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        
    }
}
