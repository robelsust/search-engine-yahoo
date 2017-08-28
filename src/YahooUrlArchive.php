<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Yahoo;

use Serps\Core\UrlArchive;
use Serps\SearchEngine\Yahoo\YahooUrl;
use Serps\SearchEngine\Yahoo\YahooUrlTrait;

/**
 * A frozen version of a google url
 */
class YahooUrlArchive extends UrlArchive implements YahooUrlInterface
{
    use YahooUrlTrait;

    public function __construct(
        $host = 'search.yahoo.com',
        $path = '/search',
        $scheme = 'https',
        array $query = [],
        $hash = '',
        $port = null,
        $user = null,
        $pass = null
    ) {
        parent::__construct($scheme, $host, $path, $query, $hash, $port, $user, $pass);
    }

    public static function build(
        $scheme = null,
        $host = null,
        $path = null,
        array $query = [],
        $hash = null,
        $port = null,
        $user = null,
        $pass = null
    ) {
        return new static(
            $host,
            $path,
            $scheme,
            $query,
            $hash,
            $port,
            $user,
            $pass
        );
    }
}
