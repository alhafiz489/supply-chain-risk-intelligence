<?php

namespace Tests\Feature;

use App\Services\AutomaticTranslationService;
use Mockery\MockInterface;
use Tests\TestCase;

class UserWebTranslationTest extends TestCase
{
    public function test_user_page_is_automatically_translated_to_selected_language(): void
    {
        config(['services.translation.enabled' => true]);

        $this->mock(
            AutomaticTranslationService::class,
            function (MockInterface $mock): void {
                $mock->shouldReceive('translateMany')
                    ->andReturnUsing(
                        function (array $texts): array {
                            $translations = array_combine($texts, $texts) ?: [];
                            $translations['Country Comparison'] = '国の比較';

                            return $translations;
                        }
                    );
            }
        );

        $response = $this->withSession([
            'locale' => 'en',
            'translation_target_locale' => 'ja',
            'selected_text_direction' => 'ltr',
        ])->get('/');

        $response->assertOk()
            ->assertSee('lang="ja"', false)
            ->assertSee('dir="ltr"', false)
            ->assertSee('&#22269;&#12398;&#27604;&#36611;', false)
            ->assertSee("document.addEventListener('DOMContentLoaded'", false);
    }

    public function test_manual_indonesian_locale_does_not_call_provider(): void
    {
        config(['services.translation.enabled' => true]);

        $this->mock(
            AutomaticTranslationService::class,
            fn (MockInterface $mock) => $mock->shouldNotReceive('translateMany')
        );

        $this->withSession([
            'locale' => 'id',
            'translation_target_locale' => 'id',
        ])->get('/')->assertOk();
    }

    public function test_dynamic_user_text_is_translated_in_a_batch(): void
    {
        $this->mock(
            AutomaticTranslationService::class,
            function (MockInterface $mock): void {
                $mock->shouldReceive('translateMany')
                    ->once()
                    ->with(['Loading data'], 'ja', 'en')
                    ->andReturn(['Loading data' => 'データを読み込んでいます']);
            }
        );

        $this->withSession([
            'translation_target_locale' => 'ja',
            'translation_mode' => 'automatic',
        ])->postJson('/user/translate', [
            'texts' => ['Loading data'],
        ])->assertOk()
            ->assertJsonPath('data.Loading data', 'データを読み込んでいます');
    }
}
