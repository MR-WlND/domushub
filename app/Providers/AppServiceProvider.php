<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Gemini;
use Gemini\Client;
use Gemini\Contracts\ClientContract;
use Gemini\Laravel\Exceptions\MissingApiKey;
use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ClientContract::class, static function (): Client {
            $apiKey = config('gemini.api_key');

            if (! is_string($apiKey)) {
                throw MissingApiKey::create();
            }

            $baseURL = config('gemini.base_url');
            if (isset($baseURL) && ! is_string($baseURL)) {
                throw new InvalidArgumentException('Invalid Gemini API base URL.');
            }

            /** @var int $timeout */
            $timeout = config('gemini.request_timeout', 30);
            $verify = config('gemini.verify_ssl', true);

            $client = Gemini::factory()
                ->withApiKey(apiKey: $apiKey)
                ->withHttpClient(client: new GuzzleClient([
                    'timeout' => (int) $timeout,
                    'verify' => $verify,
                ]));

            if (! empty($baseURL)) {
                $client->withBaseUrl(baseUrl: $baseURL);
            }

            return $client->make();
        });

        $this->app->alias(ClientContract::class, 'gemini');
        $this->app->alias(ClientContract::class, Client::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
