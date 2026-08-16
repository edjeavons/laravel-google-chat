<?php

namespace NotificationChannels\GoogleChat\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification;
use NotificationChannels\GoogleChat\GoogleChatMessage;
use NotificationChannels\GoogleChat\Transports\ServiceAccountTransport;

class ServiceAccountTransportTest extends TestCase
{
    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    public function test_it_uses_explicit_token_if_provided()
    {
        $transport = new ServiceAccountTransport(token: 'test-access-token');

        $this->assertEquals('test-access-token', $transport->resolveAccessToken());
    }

    public function test_it_throws_exception_when_no_credentials_provided()
    {
        config(['google-chat.service_account.credentials' => null]);

        $transport = new ServiceAccountTransport;

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Google Chat Service Account driver requires `credentials`');

        $transport->resolveAccessToken();
    }

    public function test_it_throws_exception_on_invalid_credentials_array()
    {
        $transport = new ServiceAccountTransport(credentials: ['invalid' => 'keys']);

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Invalid Google Chat Service Account credentials');

        $transport->resolveAccessToken();
    }

    public function test_it_fetches_and_caches_access_token_using_credentials_array()
    {
        $keyPair = openssl_pkey_new([
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($keyPair, $privateKey);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mocked-oauth-bearer-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ], 200),
            'https://chat.googleapis.com/*' => Http::response(['name' => 'spaces/AAA/messages/123'], 200),
        ]);

        $credentials = [
            'client_email' => 'bot@project.iam.gserviceaccount.com',
            'private_key' => $privateKey,
        ];

        $transport = new ServiceAccountTransport(credentials: $credentials);

        $token = $transport->resolveAccessToken();
        $this->assertEquals('mocked-oauth-bearer-token', $token);

        // Verify cache hit (Http should only be called once for token)
        $cachedToken = $transport->resolveAccessToken();
        $this->assertEquals('mocked-oauth-bearer-token', $cachedToken);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://oauth2.googleapis.com/token';
        });

        // Send a message using the transport
        $message = GoogleChatMessage::create()->text('Hello Service Account');
        $transport->send('spaces/AAA', $message);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'https://chat.googleapis.com/v1/spaces/AAA/messages')
                && $request->hasHeader('Authorization', 'Bearer mocked-oauth-bearer-token');
        });
    }
}
