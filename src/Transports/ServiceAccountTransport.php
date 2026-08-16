<?php

namespace NotificationChannels\GoogleChat\Transports;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification;
use NotificationChannels\GoogleChat\GoogleChatMessage;

class ServiceAccountTransport implements TransportInterface
{
    public function __construct(
        protected mixed $credentials = null,
        protected ?string $token = null
    ) {}

    public function send(string $endpoint, GoogleChatMessage $message): mixed
    {
        $token = $this->resolveAccessToken();

        $headers = [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Authorization' => 'Bearer '.$token,
        ];

        $pendingRequest = Http::withHeaders($headers)->withOptions([
            'curl' => [CURLOPT_FORBID_REUSE => true],
        ]);

        if ($message->isUpdate()) {
            $messageName = ltrim($message->getUpdateMessageName(), '/');
            $url = 'https://chat.googleapis.com/v1/'.$messageName;
            $updateMask = implode(',', $message->getUpdateMask());
            $url .= '?updateMask='.$updateMask;

            $response = $pendingRequest->patch($url, $message->toArray());
        } else {
            $url = str_starts_with($endpoint, 'http')
                ? $endpoint
                : 'https://chat.googleapis.com/v1/'.ltrim($endpoint, '/').'/messages';

            $replyOption = $message->getReplyOption();
            if (! $replyOption && $message->isThreaded()) {
                $replyOption = 'REPLY_MESSAGE_FALLBACK_TO_NEW_THREAD';
            }

            if ($replyOption) {
                $separator = str_contains($url, '?') ? '&' : '?';
                $url .= $separator.'messageReplyOption='.$replyOption;
            }

            $response = $pendingRequest->post($url, $message->toArray());
        }

        if ($response->failed()) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }

        return $response->json();
    }

    /**
     * Resolve the OAuth 2.0 access token generated from service account credentials.
     */
    public function resolveAccessToken(): string
    {
        if ($this->token) {
            return $this->token;
        }

        $credentialsSource = $this->credentials
            ?? config('google-chat.service_account.credentials');

        if (! $credentialsSource) {
            throw CouldNotSendNotification::missingCredentials();
        }

        $credentials = $this->parseCredentials($credentialsSource);
        $clientEmail = $credentials['client_email'] ?? null;
        $privateKey = $credentials['private_key'] ?? null;

        if (! $clientEmail || ! $privateKey) {
            throw CouldNotSendNotification::invalidCredentials();
        }

        $cacheKey = 'laravel-google-chat.access_token.'.md5($clientEmail);

        return Cache::remember($cacheKey, 3000, function () use ($clientEmail, $privateKey, $credentials) {
            return $this->fetchAccessTokenFromGoogle($clientEmail, $privateKey, $credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token');
        });
    }

    /**
     * Parse credentials from array, JSON string, or file path.
     */
    protected function parseCredentials(mixed $credentialsSource): array
    {
        if (is_array($credentialsSource)) {
            return $credentialsSource;
        }

        if (is_string($credentialsSource)) {
            if (file_exists($credentialsSource)) {
                $content = file_get_contents($credentialsSource);
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }

            $decoded = json_decode($credentialsSource, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        throw CouldNotSendNotification::invalidCredentials();
    }

    /**
     * Generate signed JWT and request OAuth 2.0 Bearer access token from Google.
     */
    protected function fetchAccessTokenFromGoogle(string $clientEmail, string $privateKey, string $tokenUri): string
    {
        $now = time();
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claimSet = [
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/chat.bot',
            'aud' => $tokenUri,
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        $base64UrlHeader = $this->base64UrlEncode((string) json_encode($header));
        $base64UrlClaimSet = $this->base64UrlEncode((string) json_encode($claimSet));
        $signatureInput = $base64UrlHeader.'.'.$base64UrlClaimSet;

        $signature = '';
        $success = openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (! $success) {
            throw CouldNotSendNotification::jwtSigningFailed();
        }

        $jwt = $signatureInput.'.'.$this->base64UrlEncode($signature);

        /** @var Response $response */
        $response = Http::asForm()->post($tokenUri, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->failed() || empty($response['access_token'])) {
            throw CouldNotSendNotification::tokenFetchFailed($response);
        }

        return (string) $response['access_token'];
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
