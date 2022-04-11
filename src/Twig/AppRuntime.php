<?php

namespace BW\StandWithUkraineBundle\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function censor(string $text): string
    {
        $censoredText = preg_replace_callback('@\<span data="censorship"\>(.*?)\</span\>@', static function ($matches) {
            $censored = '';

            $length = mb_strlen($matches[1]);
            if (!$length) {
                return '';
            }

            for ($i = 0; $i < $length; $i++) {
                $censored .= self::generateRandomCensoredChar();
            }

            return $censored;
        }, $text);

        return $censoredText;
    }

    private static function generateRandomCensoredChar(): string
    {
        $chars = [
            '@',
            '#',
            '$',
            '%',
            '&',
            '*',
        ];

        $randomIndex = array_rand($chars);

        return $chars[$randomIndex];
    }
}
