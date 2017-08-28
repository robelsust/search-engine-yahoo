<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo;

use Serps\Core\Browser\BrowserInterface;
use Serps\Core\Cookie\ArrayCookieJar;
use Serps\Core\Cookie\CookieJarInterface;
use Serps\Core\Http\HttpClientInterface;
use Serps\Core\Http\Proxy;
use Serps\Core\UrlArchive;
use Serps\Exception;
use Serps\SearchEngine\Yahoo\Exception\YahooCaptchaException;
use Serps\SearchEngine\Yahoo\Page\YahooCaptcha;
use Serps\SearchEngine\Yahoo\Page\YahooError;
use Serps\SearchEngine\Yahoo\Page\YahooSerp;
use Serps\SearchEngine\Yahoo\YahooUrl;
use Serps\Exception\RequestError\PageNotFoundException;
use Serps\Exception\RequestError\RequestErrorException;
use Serps\Exception\RequestError\InvalidResponseException;

/**
 * Google client the handles google url routing, dom object constructions and request errors
 *
 */
class YahooClient
{

    protected $defaultBrowser;

    public function __construct(BrowserInterface $browser = null)
    {
        $this->defaultBrowser = $browser;
    }

    /**
     * @param GoogleUrlInterface $googleUrl
     * @param BrowserInterface|null $browser
     * @return GoogleSerp
     * @throws Exception
     * @throws PageNotFoundException
     * @throws InvalidResponseException
     * @throws PageNotFoundException
     * @throws GoogleCaptchaException
     */
    public function query(YahooUrlInterface $yahooUrl, BrowserInterface $browser = null)
    {

        if ($yahooUrl->getResultType() !== YahooUrl::RESULT_TYPE_ALL) {
            throw new Exception(
                'The requested url is not valid for the yahoo client.'
                . 'Yahoo client only supports general searches. See YahooUrl::setResultType() for more infos.'
            );
        }

        if (null === $browser) {
            $browser = $this->defaultBrowser;
        }

        if (!$browser) {
            throw new Exception('No browser given for query and no default browser was found');
        }

        $response = $browser->navigateToUrl($yahooUrl);

        $statusCode = $response->getHttpResponseStatus();

        $effectiveUrl = YahooUrlArchive::fromString($response->getEffectiveUrl()->__toString());

        if (200 == $statusCode) {
			
            return new YahooSerp($response->getPageContent(), $effectiveUrl);
        } else {
            if (404 == $statusCode) {
                throw new PageNotFoundException($response);
            } else {
                $errorDom = new YahooError($response->getPageContent(), $effectiveUrl);

                if ($errorDom->isCaptcha()) {
                    throw new YahooCaptchaException(new YahooCaptcha($errorDom));
                } else {
                    $failedUrl = $response->getInitialUrl();
                    throw new InvalidResponseException(
                        $response,
                        "The http response from $failedUrl has an invalid status code: '$statusCode'"
                    );
                }
            }
        }
    }
}
