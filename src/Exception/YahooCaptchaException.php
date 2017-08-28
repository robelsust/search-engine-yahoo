<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo\Exception;

use Serps\Exception\RequestError\CaptchaException;
use Serps\SearchEngine\Yahoo\Page\YahooCaptcha;

/**
 * @method GoogleCaptcha getCaptcha
 */
class YahooCaptchaException extends CaptchaException
{

    public function __construct(YahooCaptcha $captchaResponse)
    {
        parent::__construct($captchaResponse);
    }
}
