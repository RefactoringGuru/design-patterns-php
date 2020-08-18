<?php

namespace RefactoringGuru\Proxy\RealWorld;

/**
 * EN: Proxy Design Pattern
 *
 * Intent: Provide a surrogate or placeholder for another object to control
 * access to the original object or to add other responsibilities.
 *
 * Example: There are countless ways proxies can be used: caching, logging,
 * access control, delayed initialization, etc. This example demonstrates how
 * the Proxy pattern can improve the performance of a downloader object by
 * caching its results.
 *
 * RU: Паттерн Заместитель
 *
 * Назначение: Позволяет подставлять вместо реальных объектов специальные
 * объекты-заменители. Эти объекты перехватывают вызовы к оригинальному объекту,
 * позволяя сделать что-то до или после передачи вызова оригиналу.
 *
 * Пример: Существует бесчисленное множество направлений, где могут быть
 * использованы заместители: кэширование, логирование, контроль доступа,
 * отложенная инициализация и т.д. В этом примере показано, как паттерн
 * Заместитель может повысить производительность объекта-загрузчика путём
 * кэширования его результатов.
 */

/**
 * EN: The Subject interface describes the interface of a real object.
 *
 * The truth is that many real apps may not have this interface clearly defined.
 * If you're in that boat, your best bet would be to extend the Proxy from one
 * of your existing application classes. If that's awkward, then extracting a
 * proper interface should be your first step.
 *
 * RU: Интерфейс Субъекта описывает интерфейс реального объекта.
 *
 * Дело в том, что у большинства приложений нет чётко определённого интерфейса.
 * В этом случае лучше было бы расширить Заместителя за счёт существующего
 * класса приложения. Если это неудобно, тогда первым шагом должно быть
 * извлечение правильного интерфейса.
 */
interface Downloader
{
    public function download(string $url): string;
}

/**
 * EN: The Real Subject does the real job, albeit not in the most efficient way.
 * When a client tries to download the same file for the second time, our
 * downloader does just that, instead of fetching the result from cache.
 *
 * RU: Реальный Субъект делает реальную работу, хотя и не самым эффективным
 * способом. Когда клиент пытается загрузить тот же самый файл во второй раз,
 * наш загрузчик именно это и делает, вместо того, чтобы извлечь результат из
 * кэша.
 */
class SimpleDownloader implements Downloader
{
    public function download(string $url): string
    {
        echo "Downloading a file from the Internet.\n";
        $result = file_get_contents($url);
        echo "Downloaded bytes: " . strlen($result) . "\n";

        return $result;
    }
}

/**
 * EN: The Proxy class is our attempt to make the download more efficient. It
 * wraps the real downloader object and delegates it the first download calls.
 * The result is then cached, making subsequent calls return an existing file
 * instead of downloading it again.
 *
 * Note that the Proxy MUST implement the same interface as the Real Subject.
 *
 * RU: Класс Заместителя – это попытка сделать загрузку более эффективной. Он
 * обёртывает реальный объект загрузчика и делегирует ему первые запросы на
 * скачивание. Затем результат кэшируется, что позволяет последующим вызовам
 * возвращать уже имеющийся файл вместо его повторной загрузки.
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
            echo "CacheProxy MISS. ";
            $result = $this->downloader->download($url);
            $this->cache[$url] = $result;
        } else {
            echo "CacheProxy HIT. Retrieving result from cache.\n";
        }
        return $this->cache[$url];
    }
}

/**
 * EN: The client code may issue several similar download requests. In this
 * case, the caching proxy saves time and traffic by serving results from cache.
 *
 * The client is unaware that it works with a proxy because it works with
 * downloaders via the abstract interface.
 *
 * RU: Клиентский код может выдать несколько похожих запросов на загрузку. В
 * этом случае кэширующий заместитель экономит время и трафик, подавая
 * результаты из кэша.
 *
 * Клиент не знает, что он работает с заместителем, потому что он работает с
 * загрузчиками через абстрактный интерфейс.
 */
function clientCode(Downloader $subject)
{
    // ...

    $result = $subject->download("http://example.com/");

    // EN: Duplicate download requests could be cached for a speed gain.
    //
    // RU: Повторяющиеся запросы на загрузку могут кэшироваться для увеличения
    // скорости.

    $result = $subject->download("http://example.com/");

    // ...
}

echo "Executing client code with real subject:\n";
$realSubject = new SimpleDownloader();
clientCode($realSubject);

echo "\n";

echo "Executing the same client code with a proxy:\n";
$proxy = new CachingDownloader($realSubject);
clientCode($proxy);
