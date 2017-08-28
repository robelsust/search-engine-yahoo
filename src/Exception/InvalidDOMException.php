<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Exception;

use Serps\Exception;

class InvalidDOMException extends Exception
{

    public function __construct($message)
    {
        parent::__construct($message . ' Yahoo DOM has possibly changed and an update may be required.');
    }
}
