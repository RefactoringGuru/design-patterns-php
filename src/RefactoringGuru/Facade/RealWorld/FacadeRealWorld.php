<?php

namespace RefactoringGuru\Facade\RealWorld;

/**
 * Facade Design Pattern
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
 */

/**
 * The Facade provides a single method for downloading videos from YouTube. This
 * method hides all the complexity of the PHP network layer, YouTube API and the
 * video conversion library (FFmpeg).
 */
class YouTubeDownloader
{
    protected $youtube;
    protected $ffmpeg;

    /**
     * It is handy when the Facade can manage the lifecycle of the subsystem it
     * uses.
     */
    public function __construct(string $youtubeApiKey)
    {
        $this->youtube = new YouTube($youtubeApiKey);
        $this->ffmpeg = new FFMpeg();
    }

    /**
     * The Facade provides a simple method for downloading video and encoding it
     * to a target format (for the sake of simplicity, the real-world code is
     * commented).
     */
    public function downloadVideo(string $url)
    {
        print("Fetching video metadata from youtube...\n");
        // $title = $this->youtube->fetchVideo($url)->getTitle();
        print("Saving video file to a temporary file...\n");
        // $this->youtube->saveAs($url, "video.mpg");

        print("Processing source video...\n");
        // $video = $this->ffmpeg->open('video.mpg');
        print("Normalizing and resizing the video to smaller dimensions...\n");
        // $video
        //     ->filters()
        //     ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
        //     ->synchronize();
        print("Capturing preview image...\n");
        // $video
        //     ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
        //     ->save($title . 'frame.jpg');
        print("Saving video in target formats...\n");
        // $video
        //     ->save(new FFMpeg\Format\Video\X264(), $title . '.mp4')
        //     ->save(new FFMpeg\Format\Video\WMV(), $title . '.wmv')
        //     ->save(new FFMpeg\Format\Video\WebM(), $title . '.webm');
        print("Done!\n");
    }
}

/**
 * The YouTube API subsystem.
 */
class YouTube
{
    function fetchVideo() { /* ... */ }

    function saveAs($path) { /* ... */ }

    // ... more methods and classes ...
}

/**
 * The FFmpeg subsystem (a complex video/audio conversion library).
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
 * The client code does not depend on any subsystem's classes. Any changes
 * inside the subsystem's code won't affect the client code. You will only need
 * to update the Facade.
 */
function clientCode(YouTubeDownloader $facade)
{
    // ...

    $facade->downloadVideo("https://www.youtube.com/watch?v=QH2-TGUlwu4");

    // ...
}

$facade = new YouTubeDownloader("APIKEY-XXXXXXXXX");
clientCode($facade);
