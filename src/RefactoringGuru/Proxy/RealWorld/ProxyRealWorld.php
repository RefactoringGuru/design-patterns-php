<?php

namespace RefactoringGuru\Proxy\RealWorld;

/**
 * Proxy Design Pattern
 *
 * Intent: Provide a surrogate or placeholder for another object to control
 * access to the original object or to add other responsibilities.
 *
 * Example: There are countless ways proxies can be used: caching, logging,
 * access control, delayed initialization, etc. This example demonstrates how
 * the Proxy pattern can improve the performance of a downloader object by
 * caching its results.
 */

/**
 * The Subject interface describes the interface of a real object.
 *
 * The truth is that many real apps may not have this interface clearly defined.
 * If you're in that boat, your first bet would be to extend the Proxy from one
 * of your existing application classes. If that's awkward, then extracting a
 * proper interface should be your first step.
 */
interface Downloader
{
    public function download(string $url): string;
}

/**
 * The Real Subject does the real job, albeit not in the most efficient way.
 * When a client tries to download the same file for the second time, our
 * downloader does just that, instead of fetching the result from cache.
 */
class SimpleDownloader implements Downloader
{
    public function download(string $url): string
    {
        print("Downloading a file from the Internet.\n");
        $result = file_get_contents($url);
        print("Downloaded bytes: " . strlen($result) . "\n");
        return $result;
    }
}

/**
 * The Proxy class is our attempt to make the download more efficient. It wraps
 * the real downloader object and delegates it the first download calls. The
 * result is then cached, making subsequent calls return an existing file
 * instead of downloading it again.
 *
 * Note that the Proxy MUST implement the same interface as the Real Subject.
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
            print("CacheProxy HIT. Retrieving result from cache.\n");
        }
        return $this->cache[$url];
    }
}

/**
 * The client code may issue several similar download requests. In this case,
 * caching proxy saves time and traffic by serving results from cache.
 *
 * The client is unaware that it works with a proxy because it works with
 * downloaders via the abstract interface.
 */
function clientCode(Downloader $subject)
{
    // ...

    $result = $subject->download("http://example.com/");

    // Duplicate download requests could be cached for a speed gain.

    $result = $subject->download("http://example.com/");

    // ...
}

print("Executing client code with real subject:\n");
$realSubject = new SimpleDownloader();
clientCode($realSubject);

print("\n");

print("Executing the same client code with a proxy:\n");
$proxy = new CachingDownloader($realSubject);
clientCode($proxy);