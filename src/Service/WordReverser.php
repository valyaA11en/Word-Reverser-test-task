<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Интерфейс для разворота букв в словах с сохранением регистра и пунктуации.
 */
interface WordReverserInterface
{
    /**
     * Разворачивает буквы в каждом слове, сохраняя регистр и пунктуацию.
     *
     * @param string $input Входная строка
     * @return string Строка с развёрнутыми буквами
     */
    public function transform(string $input): string;
}

final class WordReverser implements WordReverserInterface
{
    /** Кодировка для работы с Unicode-строками. */
    private const ENCODING = 'UTF-8';

    /**
     * Разворачивает буквы в словах, сохраняя регистр и пунктуацию.
     * Дефис, апостроф и бэктик — разделители слов.
     *
     * @param string $input Входная строка
     * @return string Строка с развёрнутыми буквами
     */
    public function transform(string $input): string
    {
        if ($input === '') {
            return '';
        }

        // Разбиваем на токены: буквы (\p{L}+) и остальное (пунктуация, разделители)
        // Разделители (-, ', `) не являются буквами, остаются на местах
        $tokens = preg_split('/(\p{L}+)/u', $input, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($tokens === false) {
            return $input;
        }

        // Нечётные индексы — буквенные группы, разворачиваем их
        foreach ($tokens as $i => $token) {
            if ($i % 2 === 1 && $token !== '') {
                $tokens[$i] = $this->reverseLettersPreservingCase($token);
            }
        }

        return implode('', $tokens);
    }

    /**
     * Разворачивает буквы в слове, сохраняя паттерн регистра по позициям.
     *
     * @param string $word Слово из букв
     * @return string Слово с развёрнутыми буквами
     */
    private function reverseLettersPreservingCase(string $word): string
    {
        $chars = self::mbStrToArray($word);
        $caseMask = array_map(fn(string $ch) => self::caseOf($ch), $chars);
        $reversed = array_reverse($chars);

        // Применяем регистр исходных позиций к развёрнутым символам
        for ($i = 0; $i < count($chars); $i++) {
            if ($caseMask[$i] === 'upper') {
                $reversed[$i] = mb_strtoupper($reversed[$i], self::ENCODING);
            } elseif ($caseMask[$i] === 'lower') {
                $reversed[$i] = mb_strtolower($reversed[$i], self::ENCODING);
            }
        }

        return implode('', $reversed);
    }

    /**
     * Определяет регистр символа: 'upper' | 'lower' | 'none'.
     *
     * @param string $ch Символ Unicode
     * @return string Тип регистра
     */
    private static function caseOf(string $ch): string
    {
        $up = mb_strtoupper($ch, self::ENCODING);
        $lo = mb_strtolower($ch, self::ENCODING);

        if ($ch === $up && $ch !== $lo) {
            return 'upper';
        }
        if ($ch === $lo && $ch !== $up) {
            return 'lower';
        }
        return 'none';
    }

    /**
     * Разбивает Unicode-строку на массив символов.
     *
     * @param string $str Unicode-строка
     * @return array<string> Массив символов
     */
    private static function mbStrToArray(string $str): array
    {
        $a = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        return $a === false ? [$str] : $a;
    }
}

