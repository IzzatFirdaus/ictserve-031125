<?php

declare(strict_types=1);

namespace Tests\Feature\Translations;

use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FaqAiChatTranslationTest extends TestCase
{
    #[Test]
    public function bahasa_melayu_faq_ai_chat_translations_exist(): void
    {
        App::setLocale('ms');

        $keys = [
            'faq.ai_chat.title',
            'faq.ai_chat.description',
            'faq.ai_chat.chat_button',
            'faq.ai_chat.powered_by',
        ];

        foreach ($keys as $key) {
            $translation = __($key);

            $this->assertNotSame(
                $key,
                $translation,
                \sprintf('Missing Bahasa Melayu translation for %s', $key)
            );
            $this->assertNotEmpty($translation, \sprintf('Empty translation for %s', $key));
        }
    }

    #[Test]
    public function faq_ai_chat_translations_return_expected_bahasa_melayu_values(): void
    {
        App::setLocale('ms');

        $translations = [
            'faq.ai_chat.title' => 'Tanya AI Bedrock',
            'faq.ai_chat.description' => 'Tidak jumpa jawapan yang anda cari? Berbual dengan AI Bedrock untuk mendapat bantuan peribadi dan jawapan yang lebih terperinci.',
            'faq.ai_chat.chat_button' => 'Sembang dengan AI',
            'faq.ai_chat.powered_by' => 'Dikuasakan oleh AWS Bedrock',
        ];

        foreach ($translations as $key => $expectedValue) {
            $this->assertSame($expectedValue, __($key));
        }
    }
}
