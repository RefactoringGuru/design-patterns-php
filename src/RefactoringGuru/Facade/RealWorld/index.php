<?php

namespace RefactoringGuru\Facade\RealWorld;

/**
 * EN: Facade Design Pattern
 *
 * Intent: Provide a unified interface to a set of interfaces in a subsystem.
 * Facade defines a higher-level interface that makes the subsystem easier to
 * use.
 *
 * Example: Think of the Facade as a simplicity adapter for some complex
 * subsystem. The Facade isolates complexity within a single class and allows
 * other application code to use the straightforward interface.
 *
 * In this example, the Facade hides the complexity of the YouTube API and
 * FFmpeg library from the client code. Instead of working with dozens of
 * classes, the client uses a simple method on the Facade.
 *
 * RU: Паттерн Фасад
 *
 * Назначение: Предоставляет единый интерфейс к ряду интерфейсов в подсистеме.
 * Фасад определяет интерфейс более высокого уровня, который упрощает
 * использование подсистемы.
 *
 * Пример: Думайте о Фасаде как о «адаптере-упрощателе» для некой сложной
 * подсистемы. Фасад изолирует сложность в рамках одного класса и позволяет
 * остальному коду приложения использовать простой интерфейс.
 *
 * В этом примере Фасад скрывает сложность API YouTube и библиотеки FFmpeg от
 * клиентского кода. Вместо того, чтобы работать с десятками классов, клиент
 * использует простой метод Фасада.
 */

/**
 * EN: The Facade provides a single method for downloading videos from YouTube.
 * This method hides all the complexity of the PHP network layer, YouTube API
 * and the video conversion library (FFmpeg).
 *
 * RU: Фасад предоставляет единый метод загрузки видео с YouTube. Этот метод
 * скрывает всю сложность сетевого уровня PHP, API YouTube и библиотеки
 * преобразования видео (FFmpeg).
 */
class YouTubeDownloader
{
    protected $youtube;
    protected $ffmpeg;

    /**
     * EN: It is handy when the Facade can manage the lifecycle of the subsystem
     * it uses.
     *
     * RU: Бывает удобным сделать Фасад ответственным за управление жизненным
     * циклом используемой подсистемы.
     */
    public function __construct(string $youtubeApiKey)
    {
        $this->youtube = new YouTube($youtubeApiKey);
        $this->ffmpeg = new FFMpeg();
    }

    /**
     * EN: The Facade provides a simple method for downloading video and
     * encoding it to a target format (for the sake of simplicity, the real-
     * world code is commented-out).
     *
     * RU: Фасад предоставляет простой метод загрузки видео и кодирования его в
     * целевой формат (для простоты понимания примера реальный код
     * закомментирован).
     */
    public function downloadVideo(string $url): void
    {
        echo "Fetching video metadata from youtube...\n";
        // $title = $this->youtube->fetchVideo($url)->getTitle();
        echo "Saving video file to a temporary file...\n";
        // $this->youtube->saveAs($url, "video.mpg");

        echo "Processing source video...\n";
        // $video = $this->ffmpeg->open('video.mpg');
        echo "Normalizing and resizing the video to smaller dimensions...\n";
        // $video
        //     ->filters()
        //     ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
        //     ->synchronize();
        echo "Capturing preview image...\n";
        // $video
        //     ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
        //     ->save($title . 'frame.jpg');
        echo "Saving video in target formats...\n";
        // $video
        //     ->save(new FFMpeg\Format\Video\X264(), $title . '.mp4')
        //     ->save(new FFMpeg\Format\Video\WMV(), $title . '.wmv')
        //     ->save(new FFMpeg\Format\Video\WebM(), $title . '.webm');
        echo "Done!\n";
    }
}

/**
 * EN: The YouTube API subsystem.
 *
 * RU: Подсистема API YouTube.
 */
class YouTube
{
    public function fetchVideo(): string { /* ... */ }

    public function saveAs(string $path): void { /* ... */ }

    // EN: ...more methods and classes...
    //
    // RU: ...дополнительные методы и классы...
}

/**
 * EN: The FFmpeg subsystem (a complex video/audio conversion library).
 *
 * RU: Подсистема FFmpeg (сложная библиотека работы с видео/аудио).
 */
class FFMpeg
{
    public static function create(): FFMpeg { /* ... */ }

    public function open(string $video): void { /* ... */ }

    // ...more methods and classes... RU: ...дополнительные методы и классы...
}

class FFMpegVideo
{
    public function filters(): self { /* ... */ }

    public function resize(): self { /* ... */ }

    public function synchronize(): self { /* ... */ }

    public function frame(): self { /* ... */ }

    public function save(string $path): self { /* ... */ }

    // ...more methods and classes... RU: ...дополнительные методы и классы...
}


/**
 * EN: The client code does not depend on any subsystem's classes. Any changes
 * inside the subsystem's code won't affect the client code. You will only need
 * to update the Facade.
 *
 * RU: Клиентский код не зависит от классов подсистем. Любые изменения внутри
 * кода подсистем не будут влиять на клиентский код. Вам нужно будет всего лишь
 * обновить Фасад.
 */
function clientCode(YouTubeDownloader $facade)
{
    // ...

    $facade->downloadVideo("https://www.youtube.com/watch?v=QH2-TGUlwu4");

    // ...
}

$facade = new YouTubeDownloader("APIKEY-XXXXXXXXX");
clientCode($facade);
