<?php

namespace App\Tests\Entity;

use App\Entity\SongSpinPlay;
use PHPUnit\Framework\TestCase;

class SongSpinPlayTest extends TestCase
{
    /**
     * @dataProvider youTubeUrlProvider
     */
    public function testExtractYouTubeId(?string $url, ?string $expectedId)
    {
        $this->assertSame($expectedId, SongSpinPlay::extractYouTubeId($url));
    }

    public function youTubeUrlProvider()
    {
        return [
            'watch' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'short link' => ['https://youtu.be/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'short link with si' => ['https://youtu.be/dQw4w9WgXcQ?si=AbCdEf123_-x', 'dQw4w9WgXcQ'],
            'watch with si' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ&si=AbCdEf123', 'dQw4w9WgXcQ'],
            'watch with timestamp' => ['https://youtube.com/watch?v=dQw4w9WgXcQ&t=42s', 'dQw4w9WgXcQ'],
            'watch with list' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=PL123&index=2', 'dQw4w9WgXcQ'],
            'v not first' => ['https://www.youtube.com/watch?feature=share&v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'mobile' => ['https://m.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'music' => ['https://music.youtube.com/watch?v=dQw4w9WgXcQ&si=xyz', 'dQw4w9WgXcQ'],
            'shorts' => ['https://youtube.com/shorts/dQw4w9WgXcQ?si=xyz', 'dQw4w9WgXcQ'],
            'embed' => ['https://www.youtube.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'nocookie embed' => ['https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'live' => ['https://www.youtube.com/live/dQw4w9WgXcQ?feature=share', 'dQw4w9WgXcQ'],
            'no scheme' => ['youtu.be/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'fragment' => ['https://youtu.be/dQw4w9WgXcQ#t=10', 'dQw4w9WgXcQ'],
            'not youtube' => ['https://vimeo.com/123456789', null],
            'empty' => ['', null],
            'null' => [null, null],
        ];
    }
}
