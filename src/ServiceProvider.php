<?php

declare(strict_types=1);

namespace EsfahanAhan\Money;

use Illuminate\Support\ServiceProvider as SupportServiceProvider;

class ServiceProvider extends SupportServiceProvider
{
    public function boot(): void
    {
        $this->bootingLanguages();
        $this->bootingPublishing();
    }

    protected function bootingLanguages(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'esfahanahan');
    }

    protected function bootingPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], [
                'esfahanahan',
                'esfahanahan-money',
                'esfahanahan-money-migrations',
            ]);
        }
    }
}
