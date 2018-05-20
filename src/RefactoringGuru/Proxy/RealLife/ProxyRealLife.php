<?php

namespace RefactoringGuru\Proxy\RealLife;

/**
 * Proxy Design Pattern
 *
 * Intent: Provide a surrogate or placeholder for another object to control
 * access to it or add other responsibilities.
 *
 * Example: There are dozens of ways proxies can be used: caching, logging,
 * access control, delayed initialization, etc. This example shows how proxy can
 * improve a some real object by caching its results.
 */

/**
 * Subject. Defines the downloading interface.
 */
interface Downloader
{
    public function download(string $url): string;
}

/**
 * Real Subject. Downloads a web page.
 */
class SimpleDownloader implements Downloader
{
    public function download(string $url): string
    {
        print("Downloading file from Internet...\n");
        return file_get_contents($url);
    }
}

/**
 * Proxy. Caches the download result and serves it on subsequent requests
 * instead of downloading it again.
 */
class CachingDownloader implements Downloader
{
    /**
     * @var SimpleDownloader
     */
    private $downloader;

    /**
     * @var string[]
     */
    private $cache = [];

    public function __construct(SimpleDownloader $downloader)
    {
        $this->downloader = $downloader;
    }

    public function download(string $url): string
    {
        if (!isset($this->cache[$url])) {
            print("CacheProxy MISS. ");
            $result = $this->downloader->download($url);
            $this->cache[$url] = $result;
        } else {
            print("CacheProxy HIT. Retrieving result from cache...\n");
        }
        return $this->cache[$url];
    }
}

/**
 * Client code may issue several equal download requests. In this case caching
 * proxy can save time and traffic by serving results from cache.
 */
function clientCode(Downloader $subject)
{
    // ...

    $result = $subject->download("http://example.com/");
    print("Downloaded chars: " . strlen($result) . "\n");

    // Duplicate download requests could be cached for a speed gain.

    $result = $subject->download("http://example.com/");
    print("Downloaded chars: " . strlen($result) . "\n");

    // ...
}

print("Executing client code with real subject:\n");
$realSubject = new SimpleDownloader();
clientCode($realSubject);

print("\n");

print("Executing the same client code with a proxy:\n");
$proxy = new CachingDownloader($realSubject);
clientCode($proxy);