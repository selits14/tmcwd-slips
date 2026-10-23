<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Command\Command;
use Tests\TestCase;

class Llm7ConnectionCommandTest extends TestCase
{
    public function test_it_sends_a_server_side_chat_completion_request(): void
    {
        config([
            'services.llm7.api_key' => 'test-api-key',
            'services.llm7.base_url' => 'https://api.llm7.io/v1/',
            'services.llm7.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.llm7.io/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Hello there.']],
                ],
            ]),
        ]);

        $this->artisan('llm7:check')
            ->expectsOutput('Hello there.')
            ->assertExitCode(Command::SUCCESS);

        Http::assertSent(fn (HttpRequest $request) => $request->url() === 'https://api.llm7.io/v1/chat/completions'
            && $request->hasHeader('Authorization', 'Bearer test-api-key')
            && $request->data() === [
                'model' => 'test-model',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Say hello in one sentence.',
                    ],
                ],
                'stream' => false,
            ]);
    }

    public function test_it_fails_without_sending_a_request_when_the_api_key_is_missing(): void
    {
        config(['services.llm7.api_key' => null]);
        Http::fake();

        $this->artisan('llm7:check')
            ->expectsOutput('LLM7_API_KEY is not configured. Set it in your environment before running this check.')
            ->assertExitCode(Command::FAILURE);

        Http::assertNothingSent();
    }
}
