<?php

namespace BW\StandWithUkraineBundle\Tests\Twig;

use BW\StandWithUkraineBundle\Twig\TwigRuntime;
use PHPUnit\Framework\TestCase;

class TwigRuntimeTest extends TestCase
{
    /**
     * @dataProvider textProvider
     */
    public function testCensor($text, $expected)
    {
        $appRuntime = new TwigRuntime();
        // Set only 1 valid char for censorship so we could test easier
        $appRuntime->setCensoredChars(['#']);
        $actual = $appRuntime->censor($text);

        self::assertEquals($expected, $actual);
    }

    public function textProvider(): array
    {
        return [
            // Test cyrillic with 1 char censored
            [
                'Російський військовий корабель, іди н<span data="censorship">а</span>хуй! © 🇺🇦',
                'Російський військовий корабель, іди н#хуй! © 🇺🇦',
            ],
            // Test more than 1 char censored
            [
                'Russian warship, go f<span data="censorship">uck</span> yourself!',
                'Russian warship, go f### yourself!',
            ],
            // Test empty censored tag
            [
                'Russian warship, go <span data="censorship"></span>fuck yourself!',
                'Russian warship, go fuck yourself!',
            ],
            // Test no censored tag at all
            [
                'Russian warship, go fuck yourself!',
                'Russian warship, go fuck yourself!',
            ],
            // Test a few censored tags
            [
                'Russian warship, g<span data="censorship">o</span> f<span data="censorship">u</span>ck yourself!',
                'Russian warship, g# f#ck yourself!',
            ],
        ];
    }
}
