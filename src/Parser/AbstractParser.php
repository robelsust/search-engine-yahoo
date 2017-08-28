<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Parser;

use Serps\Core\Dom\DomNodeList;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Yahoo\Page\YahooDom;

abstract class AbstractParser
{

    /**
     * @var ParsingRuleInterface[]
     */
    protected $rules = null;

    /**
     * @return ParsingRuleInterface[]
     */
    abstract protected function generateRules();

    /**
     * @param GoogleDom $googleDom
     * @return DomNodeList
     */
    abstract protected function getParsableItems(YahooDom $yahooDom);


    /**
     * @return ParsingRuleInterface[]
     */
    public function getRules()
    {
       
        if (null == $this->rules) {
            $this->rules = $this->generateRules();
        }
        
        return $this->rules;
    }

    /**
     * Parses the given google dom
     * @param GoogleDom $googleDom
     * @return IndexedResultSet
     */
    public function parse(YahooDom $yahooDom)
    {
      		
        $elementGroups = $this->getParsableItems($yahooDom);
	
        $resultSet = $this->createResultSet($yahooDom);
		
        return $this->parseGroups($elementGroups, $resultSet, $yahooDom);
    }

    protected function createResultSet(YahooDom $yahooDom)
    {
       
        $startingAt = (int) $yahooDom->getUrl()->getParamValue('b', 0);
      
        return new IndexedResultSet($startingAt + 1);
    }

    /**
     * @param $elementGroups
     * @param IndexedResultSet $resultSet
     * @param $googleDom
     * @return IndexedResultSet
     */
    protected function parseGroups(DomNodeList $elementGroups, IndexedResultSet $resultSet, $yahooDom)
    {
        $rules = $this->getRules();
       
        foreach ($elementGroups as $group) {	
            
            if (!($group instanceof \DOMElement)) {
                continue;
            }
            
            foreach ($rules as $rule) {
               
                $match = $rule->match($yahooDom, $group);
                
                if ($match instanceof \DOMNodeList) {	                   
                    $this->parseGroups(new DomNodeList($group->childNodes, $yahooDom), $resultSet, $yahooDom);
                    break;
                } else {
					
                    switch ($match) {
                        case ParsingRuleInterface::RULE_MATCH_MATCHED: 
                            $rule->parse($yahooDom, $group, $resultSet);
                            break 2;
                        case ParsingRuleInterface::RULE_MATCH_STOP:
                            break 2;
                    }
                }
            }
        }
	
        return $resultSet;
    }
}
