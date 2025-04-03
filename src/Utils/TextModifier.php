<?php

namespace App\Utils;

class TextModifier {
    public static function convert($text) {
        // Not time yet!
        if(date('m-d') !== '04-01') {
            return $text;
        }
        
        // We do a little trolling >:3
        $replacements = [
            'r' => 'w',
            'l' => 'w',
            'R' => 'W',
            'L' => 'W',
            'no' => 'nyo',
            'na' => 'nya',
            'ne' => 'nye',
            'ni' => 'nyi',
            'nu' => 'nyu',
            'ove' => 'uv',
            'ow' => 'owo',
            'uv' => 'uvu',
            'uw' => 'uwu',
            'with' => 'wiff',
            'small' => 'smol',
            'cute' => 'kawaii~',
            'love' => 'wuv',
            'has' => 'haz',
            'have' => 'haz',
            'you' => 'yuu',
            'You' => 'Yuu',
            'the ' => 'da ',
            'The ' => 'Da ',
            'that' => 'dat',
            'is' => 'ish',
            'this' => 'dis',
            'thing' => 'tingy',
            'my' => 'mai',
            'Oh' => 'OwO',
            'hello' => 'hewwo',
            'Hello' => 'Hewwo',
            'hi' => 'hai',
            'Hi' => 'Hai',
            'good' => 'gud',
            'stop' => 'stawp',
            'what' => 'wat',
            'please' => 'pwease',
            'sorry' => 'sowwy',
            '.' => ' uwu~',
        ];

        $uwuText = strtr($text, $replacements);
        $uwuText = preg_replace('/([a-zA-Z])/u', '$1', $uwuText);
        $kawaiiPhrases = [
            ' uwu', ' >w<', ' ^-^', ' (✿ ♡‿♡)', ' (*≧ω≦)', ' rawr~',
            ' OwO', ' UwU', ' ( •́ .̫ •̀ )', ' nya~', ' :3', ' XD',
            ' ;;w;;', ' TwT', ' ÒwÓ', ' >.<', ' uwu~', ' ~nyaa'
        ];
        $randomPhrase = $kawaiiPhrases[array_rand($kawaiiPhrases)];
        $uwuText .= ' ' . $randomPhrase;

        return $uwuText;
    }
}
