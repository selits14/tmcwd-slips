<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckLlm7Connection extends Command
{
    protected $signature = 'llm7:check';

    protected $description = 'Check connectivity to the LLM7 chat completions API';

    public function handle(): int
    {
        $apiKey = config('services.llm7.api_key');
        $baseUrl = config('services.llm7.base_url');
        $model = config('services.llm7.model');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            $this->error('LLM7_API_KEY is not configured. Set it in your environment before running this check.');

            return self::FAILURE;
        }

        if (! is_string($baseUrl) || trim($baseUrl) === '' || ! is_string($model) || trim($model) === '') {
            $this->error('LLM7_BASE_URL and LLM7_MODEL must be configured with non-empty values.');

            return self::FAILURE;
        }

        $response = Http::acceptJson()
            ->withToken($apiKey)
            ->timeout(30)
            ->post(rtrim($baseUrl, '/').'/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Say hello in one sentence.',
                    ],
                ],
                'stream' => false,
            ]);

        $response->throw();

        $content = $response->json('choices.0.message.content');

        if (! is_string($content) || trim($content) === '') {
            $this->error('LLM7 returned a successful response without chat message content.');

            return self::FAILURE;
        }

        $this->info(trim($content));

        return self::SUCCESS;
    }
}
