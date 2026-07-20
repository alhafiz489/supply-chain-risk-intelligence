<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Services\AutomaticTranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TranslationProviderIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mandarin_is_sent_to_libretranslate_as_zh_hans(): void
    {
        config([
            'services.translation.enabled' => true,
            'services.translation.provider' => 'libretranslate',
            'services.translation.libretranslate.url' => 'http://translator.test',
        ]);

        Http::fake([
            'http://translator.test/translate' => Http::response([
                'translatedText' => ['供应链风险'],
            ]),
        ]);

        $result = app(AutomaticTranslationService::class)
            ->translateMany(['Supply chain risk'], 'zh');

        $this->assertSame('供应链风险', $result['Supply chain risk']);
        Http::assertSent(fn ($request) =>
            $request['target'] === 'zh-Hans'
            && $request['q'] === ['Supply chain risk']
        );
    }

    public function test_language_dropdown_only_returns_provider_targets(): void
    {
        config([
            'services.translation.provider' => 'libretranslate',
            'services.translation.libretranslate.url' => 'http://translator.test',
        ]);
        Cache::forget('libretranslate.supported-targets');

        Http::fake([
            'http://translator.test/languages' => Http::response([
                ['code' => 'en', 'name' => 'English', 'targets' => ['en', 'ja']],
                ['code' => 'ja', 'name' => 'Japanese', 'targets' => ['en', 'ja']],
            ]),
        ]);

        Country::query()->create([
            'name' => 'Japan',
            'iso2' => 'JP',
            'primary_language_code' => 'jpn',
            'primary_language_name' => 'Japanese',
            'primary_language_native_name' => '日本語',
            'text_direction' => 'ltr',
        ]);
        Country::query()->create([
            'name' => 'France',
            'iso2' => 'FR',
            'primary_language_code' => 'fra',
            'primary_language_name' => 'French',
            'primary_language_native_name' => 'Français',
            'text_direction' => 'ltr',
        ]);

        $response = $this->getJson('/user/languages')->assertOk();

        $response->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.language_code', 'ja')
            ->assertJsonPath('data.0.language_native_name', '日本語');
    }

    public function test_translation_status_reports_local_provider_online(): void
    {
        config([
            'services.translation.enabled' => true,
            'services.translation.provider' => 'libretranslate',
            'services.translation.libretranslate.url' => 'http://translator.test',
        ]);
        Http::fake([
            'http://translator.test/languages' => Http::response([
                ['code' => 'en', 'name' => 'English', 'targets' => ['ja']],
                ['code' => 'ja', 'name' => 'Japanese', 'targets' => ['en']],
            ]),
        ]);

        $this->getJson('/user/translation-status')
            ->assertOk()
            ->assertJsonPath('data.enabled', true)
            ->assertJsonPath('data.online', true)
            ->assertJsonPath('data.provider', 'libretranslate')
            ->assertJsonPath('data.languages.1', 'ja');
    }
}
