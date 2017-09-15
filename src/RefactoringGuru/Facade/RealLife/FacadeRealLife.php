<?php

namespace RefactoringGuru\Facade\RealLife;

/**
 * Facade Design Pattern
 *
 * Intent: Provide a unified interface to a set of interfaces in a subsystem.
 * Facade defines a higher-level interface that makes the subsystem easier
 * to use.
 *
 * Example: Facades are simplicity adapters over complex code. They isolate
 * complexity within a single class and allow other application code to
 * use straightforward interface. In this example, facade hides from a client
 * code the complexity of Youtube API and ffmpeg library. Instead working
 * with dozen of classes, client uses one simple method of the facade.
 */

/**
 * Facade. Provides a simple interface for a single operations that
 * uses complex Youtube and FFMpeg subsystems.
 */
class YoutubeDownloader
{
    protected $youtube;
    protected $ffmpeg;

    /**
     * Facade can manage lifecycle of subsystems it uses.
     */
    public function __construct(string $youtubeApiKey)
    {
        $this->youtube = new Youtube($youtubeApiKey);
        $this->ffmpeg = new FFMpeg();
    }

    /**
     * Facade provides simple method for downloading video and encoding it to
     * a target format (for the sake of simplicity, the real-world code is
     * commented).
     */
    public function downloadVideo(string $url)
    {
        echo "Fetching video metadata from youtube...\n";
        //$title = $this->youtube->fetchVideo($url)->getTitle();
        echo "Saving video file to a temporary file...\n";
        //$this->youtube->saveAs($url, "video.mpg");

        echo "Processing source video...\n";
        //$video = $this->ffmpeg->open('video.mpg');
        echo "Normalizing and resizing the video to smaller dimensions...\n";
        //$video
        //    ->filters()
        //    ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
        //    ->synchronize();
        echo "Capturing preview image...\n";
        //$video
        //    ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
        //    ->save($title . 'frame.jpg');
        echo "Saving video in target formats...\n";
        //$video
        //    ->save(new FFMpeg\Format\Video\X264(), $title . '.mp4')
        //    ->save(new FFMpeg\Format\Video\WMV(), $title . '.wmv')
        //    ->save(new FFMpeg\Format\Video\WebM(), $title . '.webm');
        echo "Done!\n";
    }
}

/**
 * Youtube API subsystem.
 */
class Youtube
{
    function fetchVideo() { /* ... */ }

    function saveAs($path) { /* ... */ }

    // ... more methods and classes ...
}

/**
 * FFMpeg subsystem. Complex video/audio conversion library.
 */
class FFMpeg
{
    static public function create() { /* ... */ }

    public function open(string $video) { /* ... */ }

    // ... more methods and classes ...
}

class FFMpegVideo
{
    public function filters() { /* ... */ }

    public function resize() { /* ... */ }

    public function synchronize() { /* ... */ }

    public function frame() { /* ... */ }

    public function save(string $path) { /* ... */ }

    // ... more methods and classes ...
}


/**
 * Client code does not depend on any subsystem classes. Any changes inside
 * subsystem code won't affect the client code. You will only need to update
 * the facade.
 */
function clientCode(YoutubeDownloader $facade)
{
    // ...

    $facade->downloadVideo("https://www.youtube.com/watch?v=QH2-TGUlwu4");

    // ...
}

$facade = new YoutubeDownloader("APIKEY-XXXXXXXXX");
clientCode($facade);
