<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Service\WordReverser;
use App\Service\WordReverserInterface;

final class WordReverserTest extends TestCase
{
    private WordReverserInterface $svc;

    protected function setUp(): void
    {
        $this->svc = new WordReverser();
    }

    public function testEmpty(): void
    {
        $this->assertSame('', $this->svc->transform(''));
    }

    public function testBasicLatinAndCyrillicCasePreservation(): void
    {
        $this->assertSame('Tac',      $this->svc->transform('Cat'));
        $this->assertSame('Ьшым',     $this->svc->transform('Мышь'));
        $this->assertSame('esuOh',    $this->svc->transform('houSe'));
        $this->assertSame('кимОД',    $this->svc->transform('домИК'));
        $this->assertSame('tnAhPele', $this->svc->transform('elEpHant'));
    }

    public function testPunctuationStays(): void
    {
        $this->assertSame('tac,',                  $this->svc->transform('cat,'));
        $this->assertSame('Амиз:',                 $this->svc->transform('Зима:'));
        $this->assertSame("si 'dloc' won",         $this->svc->transform("is 'cold' now"));
        $this->assertSame('отэ «Кат» "отсорп"',    $this->svc->transform('это «Так» "просто"'));
    }

    public function testHyphenAndApostrophesAreSeparators(): void
    {
        // Дефис, апостроф и бэктик являются разделителями слов
        // Каждая часть слова разворачивается отдельно, разделитель остается на месте
        $this->assertSame('driht-trap', $this->svc->transform('third-part'));
        $this->assertSame('nac`t',      $this->svc->transform('can`t')); // бэктик как разделитель
        $this->assertSame("nac't",      $this->svc->transform("can't")); // классический апостроф
        
        // Проверяем, что разделители действительно разделяют слова:
        // "won't've" разбивается на ['won', "'", 't', "'", 've']
        // Каждая буквенная часть разворачивается отдельно: "won" -> "now", "t" -> "t", "ve" -> "ev"
        // Результат: "now't'ev"
        $this->assertSame("now't'ev", $this->svc->transform("won't've"));
    }

    public function testMixedScriptsAndNoCaseLetters(): void
    {
        $this->assertSame('山川', $this->svc->transform('川山')); // иероглифы — без изменения регистра
        $this->assertSame('ßa',   $this->svc->transform('aß'));  // немецкая ß
    }

    public function testMultipleWordsWithSpaces(): void
    {
        $this->assertSame('Olleh  Dlrow', $this->svc->transform('Hello  World'));
    }

    public function testNumbersAndSymbolsRemain(): void
    {
        $this->assertSame('cba-123-ZYX', $this->svc->transform('abc-123-XYZ'));
        $this->assertSame('Ab!@#',       $this->svc->transform('Ba!@#'));
    }

    public function testSingleLetter(): void
    {
        $this->assertSame('A', $this->svc->transform('A'));
        $this->assertSame('а', $this->svc->transform('а'));
        $this->assertSame('Я', $this->svc->transform('Я'));
    }

    public function testOnlyPunctuation(): void
    {
        $this->assertSame('!@#$%', $this->svc->transform('!@#$%'));
        $this->assertSame('.,;:', $this->svc->transform('.,;:'));
    }

    public function testOnlySpaces(): void
    {
        $this->assertSame('   ', $this->svc->transform('   '));
        $this->assertSame("\t\n", $this->svc->transform("\t\n"));
    }

    public function testComplexHyphenatedWords(): void
    {
        $this->assertSame('eno-owt-eerht', $this->svc->transform('one-two-three'));
        $this->assertSame('TAC-ESUOH', $this->svc->transform('CAT-HOUSE'));
        $this->assertSame('кимОД-Ьшым', $this->svc->transform('домИК-Мышь'));
    }

    public function testMultipleApostrophes(): void
    {
        $this->assertSame("now't'ev", $this->svc->transform("won't've"));
        $this->assertSame("nac`t'd", $this->svc->transform("can`t'd"));
    }

    public function testMixedPunctuation(): void
    {
        $this->assertSame('tac,', $this->svc->transform('cat,'));
        $this->assertSame('tac.', $this->svc->transform('cat.'));
        $this->assertSame('tac!', $this->svc->transform('cat!'));
        $this->assertSame('tac?', $this->svc->transform('cat?'));
        $this->assertSame('tac;', $this->svc->transform('cat;'));
        $this->assertSame('tac:', $this->svc->transform('cat:'));
    }

    public function testQuotesAndBrackets(): void
    {
        $this->assertSame('"tac"', $this->svc->transform('"cat"'));
        $this->assertSame("'tac'", $this->svc->transform("'cat'"));
        $this->assertSame('(tac)', $this->svc->transform('(cat)'));
        $this->assertSame('[tac]', $this->svc->transform('[cat]'));
        $this->assertSame('{tac}', $this->svc->transform('{cat}'));
    }

    public function testMixedLanguagesInOneWord(): void
    {
        $this->assertSame('КотTac', $this->svc->transform('CatТок'));
        $this->assertSame('домEsUoh', $this->svc->transform('houSeМод'));
    }

    public function testSpecialUnicodeCharacters(): void
    {
        // Греческие буквы
        $this->assertSame('ωΑ', $this->svc->transform('αΩ'));
        $this->assertSame('ΒΑ', $this->svc->transform('ΑΒ'));
        
        // Арабские буквы (без регистра, но должны разворачиваться)
        $this->assertSame('باج', $this->svc->transform('جاب'));
    }

    public function testCasePreservationEdgeCases(): void
    {
        // Все заглавные
        $this->assertSame('TAC', $this->svc->transform('CAT'));
        // Все строчные
        $this->assertSame('tac', $this->svc->transform('cat'));
        // Чередование
        $this->assertSame('TaC', $this->svc->transform('CaT'));
        $this->assertSame('tAc', $this->svc->transform('cAt'));
    }

    public function testCyrillicCasePreservation(): void
    {
        $this->assertSame('ТАК', $this->svc->transform('КАТ'));
        $this->assertSame('так', $this->svc->transform('кат'));
        $this->assertSame('ТаК', $this->svc->transform('КаТ'));
        $this->assertSame('тАк', $this->svc->transform('кАт'));
    }

    public function testLongWords(): void
    {
        $this->assertSame('tnemngissa', $this->svc->transform('assignment'));
        $this->assertSame('TnemngissA', $this->svc->transform('AssignmenT'));
    }

    public function testWordsWithNumbers(): void
    {
        $this->assertSame('cba123', $this->svc->transform('abc123'));
        $this->assertSame('123cba', $this->svc->transform('123abc'));
        // Буквы, разделенные числами, обрабатываются отдельно
        $this->assertSame('c3b2a1', $this->svc->transform('c3b2a1'));
    }

    public function testMultipleSeparators(): void
    {
        $this->assertSame('driht-trap`nac', $this->svc->transform('third-part`can'));
        $this->assertSame("driht-trap'nac", $this->svc->transform("third-part'can"));
    }

    public function testSentenceWithMultiplePunctuation(): void
    {
        $this->assertSame(
            'Olleh, Dlrow!',
            $this->svc->transform('Hello, World!')
        );
        $this->assertSame(
            'Тевирп, Рим!',
            $this->svc->transform('Привет, Мир!')
        );
    }

    public function testWhitespaceVariations(): void
    {
        $this->assertSame("tac\ngod", $this->svc->transform("cat\ndog"));
        $this->assertSame("tac\tgod", $this->svc->transform("cat\tdog"));
        $this->assertSame("tac  god", $this->svc->transform("cat  dog"));
    }

    public function testEmptyWordsBetweenPunctuation(): void
    {
        $this->assertSame('---', $this->svc->transform('---'));
        $this->assertSame('...', $this->svc->transform('...'));
    }

    public function testComplexRealWorldExamples(): void
    {
        $this->assertSame(
            "si 'dloc' won, now't ti?",
            $this->svc->transform("is 'cold' now, won't it?")
        );
        $this->assertSame(
            'отэ «Кат» "отсорп" — да!',
            $this->svc->transform('это «Так» "просто" — ад!')
        );
    }

    public function testDiacriticsAndAccents(): void
    {
        // Французские буквы с диакритикой
        $this->assertSame('éàç', $this->svc->transform('çàé'));
        $this->assertSame('ÉÀÇ', $this->svc->transform('ÇÀÉ'));
        
        // Немецкие умлауты
        $this->assertSame('öäü', $this->svc->transform('üäö'));
        $this->assertSame('ÖÄÜ', $this->svc->transform('ÜÄÖ'));
    }

    public function testInterfaceImplementation(): void
    {
        $this->assertInstanceOf(WordReverserInterface::class, $this->svc);
    }

    public function testLeadingAndTrailingWhitespace(): void
    {
        $this->assertSame('  tac  ', $this->svc->transform('  cat  '));
        $this->assertSame("\ttac\n", $this->svc->transform("\tcat\n"));
        $this->assertSame('   ', $this->svc->transform('   '));
    }

    public function testVeryLongWord(): void
    {
        // Стресс-тест: очень длинное слово
        $longWord = str_repeat('abcdefghijklmnopqrstuvwxyz', 10); // 260 символов
        $reversed = $this->svc->transform($longWord);
        $this->assertNotEquals($longWord, $reversed);
        $this->assertSame(strlen($longWord), strlen($reversed));
        
        // Проверяем, что первая буква стала последней (используем mb функции для безопасности)
        $this->assertSame('z', mb_strtolower(mb_substr($reversed, 0, 1, 'UTF-8'), 'UTF-8'));
        $this->assertSame('a', mb_strtolower(mb_substr($reversed, -1, 1, 'UTF-8'), 'UTF-8'));
    }

    public function testVeryLongWordWithCase(): void
    {
        // Длинное слово с чередующимся регистром
        $longWord = 'AbCdEfGhIjKlMnOpQrStUvWxYz';
        $reversed = $this->svc->transform($longWord);
        $this->assertSame('ZyXwVuTsRqPoNmLkJiHgFeDcBa', $reversed);
    }

    public function testMixedLanguagesInDifferentWords(): void
    {
        $this->assertSame('доМ cat', $this->svc->transform('моД tac'));
        $this->assertSame('hellO миР', $this->svc->transform('olleH риМ'));
        $this->assertSame('Cat 123 Кот', $this->svc->transform('Tac 123 Ток'));
    }

    public function testChineseCharactersNoCase(): void
    {
        // Китайские иероглифы не имеют регистра, должны просто разворачиваться
        $this->assertSame('世界你好', $this->svc->transform('好你界世'));
        $this->assertSame('山川河流', $this->svc->transform('流河川山'));
        
        // Смешанный случай: китайские иероглифы + кириллица
        // Иероглифы не имеют регистра, кириллица разворачивается
        $this->assertSame('好你 рим', $this->svc->transform('你好 мир'));
    }

    public function testJapaneseCharacters(): void
    {
        // Японские символы (хирагана, катакана, кандзи)
        $this->assertSame('こんにちは', $this->svc->transform('はちにんこ'));
        $this->assertSame('カタカナ', $this->svc->transform('ナカタカ'));
    }

    public function testSpecialCaseGermanEszett(): void
    {
        // Немецкая ß (эсцет) - особый случай
        $this->assertSame('ßa', $this->svc->transform('aß'));
        $this->assertSame('straßE', $this->svc->transform('eßartS'));
    }

    public function testTurkishSpecialCharacters(): void
    {
        // Турецкие символы İ и ı
        $this->assertSame('i̇stanbuL', $this->svc->transform('lubnatsİ'));
        // Примечание: mb_strtoupper('i', 'tr_TR') даст 'İ', но мы используем 'UTF-8'
        // Это может быть edge-case, но для общего случая UTF-8 работает корректно
    }

    public function testEmptyStringEdgeCases(): void
    {
        $this->assertSame('', $this->svc->transform(''));
        // Пробелы остаются пробелами, но если нет букв, строка остается без изменений
        $this->assertSame('   ', $this->svc->transform('   '));
        $this->assertSame("\t\n\r", $this->svc->transform("\t\n\r"));
    }

    public function testSingleCharacterWords(): void
    {
        $this->assertSame('A', $this->svc->transform('A'));
        $this->assertSame('a', $this->svc->transform('a'));
        $this->assertSame('Я', $this->svc->transform('Я'));
        $this->assertSame('я', $this->svc->transform('я'));
    }

    public function testComplexSeparatorsCombination(): void
    {
        $this->assertSame('driht-trap`nac\'won', $this->svc->transform('third-part`can\'now'));
        $this->assertSame('eno-owt-eerht`ruof', $this->svc->transform('one-two-three`four'));
    }

    public function testNumbersAsSeparators(): void
    {
        // Числа должны оставаться на месте, буквы разворачиваются отдельно
        $this->assertSame('cba123def', $this->svc->transform('abc123fed'));
        $this->assertSame('123cba456def', $this->svc->transform('123abc456fed'));
    }

    public function testMultipleSpacesBetweenWords(): void
    {
        $this->assertSame('tac    dog', $this->svc->transform('cat    god'));
        $this->assertSame("tac\tdog\ntnahpele", $this->svc->transform("cat\tgod\nelephant"));
    }

    /**
     * Тест на стабильность: строка без букв должна возвращаться без изменений.
     * Это важно для корректной обработки строк, содержащих только пунктуацию, числа, пробелы.
     */
    public function testStabilityNoLetters(): void
    {
        // Только пунктуация
        $this->assertSame('!@#$%^&*()', $this->svc->transform('!@#$%^&*()'));
        
        // Только числа
        $this->assertSame('1234567890', $this->svc->transform('1234567890'));
        
        // Только пробелы
        $this->assertSame('   ', $this->svc->transform('   '));
        
        // Только разделители
        $this->assertSame('---', $this->svc->transform('---'));
        $this->assertSame("'''", $this->svc->transform("'''"));
        $this->assertSame('```', $this->svc->transform('```'));
        
        // Смешанные не-буквенные символы
        $this->assertSame('123!@#456', $this->svc->transform('123!@#456'));
        $this->assertSame('---...!!!', $this->svc->transform('---...!!!'));
    }

    /**
     * Позитивные кейсы из требований ТЗ.
     * Проверяем все примеры из технического задания.
     */
    public function testPositiveCasesFromRequirements(): void
    {
        // Базовые примеры с сохранением регистра
        $this->assertSame('Tac', $this->svc->transform('Cat'));
        $this->assertSame('кимОД', $this->svc->transform('домИК'));
        $this->assertSame('esuOh', $this->svc->transform('houSe'));
        
        // С пунктуацией
        $this->assertSame('si "dloc" won', $this->svc->transform('is "cold" now'));
        $this->assertSame('отэ «Кат» "отсорп"', $this->svc->transform('это «Так» "просто"'));
        
        // Составные слова с разделителями
        $this->assertSame('driht-trap', $this->svc->transform('third-part'));
        $this->assertSame("nac't", $this->svc->transform("can't"));
        
        // Мультиалфавитные
        $this->assertSame('Мод tac', $this->svc->transform('Дом cat'));
    }
}

