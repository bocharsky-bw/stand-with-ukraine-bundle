<?php

namespace BW\StandWithUkraineBundle\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class TwigRuntime implements RuntimeExtensionInterface
{
    private $censoredChars = [
        '@',
        '#',
        '$',
        '%',
        '&',
        '*',
    ];

    public function censor(string $text): string
    {
        $censoredText = preg_replace_callback('@\<span data="censorship"\>(.*?)\</span\>@', function ($matches) {
            $censored = '';

            $length = mb_strlen($matches[1]);
            if (!$length) {
                return '';
            }

            for ($i = 0; $i < $length; $i++) {
                $censored .= $this->generateRandomCensoredChar();
            }

            return $censored;
        }, $text);

        return $censoredText;
    }

    private function generateRandomCensoredChar(): string
    {
        $randomIndex = array_rand($this->censoredChars);

        return $this->censoredChars[$randomIndex];
    }

    /**
     * @internal It's supposed to be used in tests only
     */
    public function setCensoredChars(array $censoredChars)
    {
        $this->censoredChars = $censoredChars;
    }
}
