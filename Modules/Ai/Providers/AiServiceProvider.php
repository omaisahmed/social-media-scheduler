<?php

declare(strict_types=1);

namespace Modules\AI\Providers;

use Modules\AI\Contracts\ContentGenerator;
use Modules\AI\Services\OpenAIContentGenerator;
use Modules\Core\Providers\ModuleServiceProvider;

final class AiServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'ai';

    protected string $moduleNamespace = 'ai';

    public function register(): void
    {
        parent::register();

        $this->app->singleton(ContentGenerator::class, function ($app) {
            $config = $app['config']->get('ai.openai');

            return new OpenAIContentGenerator(
                $config['key'] ?? '',
                $config['model'] ?? 'gpt-4o-mini',
                $config['base_url'] ?? 'https://api.openai.com/v1',
            );
        });
    }
}
