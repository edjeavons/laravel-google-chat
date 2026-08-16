<?php

namespace NotificationChannels\GoogleChat\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Client\Response;
use NotificationChannels\GoogleChat\GoogleChatMessage;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown if a notification instance does not implement a toGoogleChat() method, but is
     * attempting to be delivered via the Google Chat notification channel.
     *
     * @param  mixed  $notification
     * @return static
     */
    public static function undefinedMethod($notification)
    {
        return new static(
            'Notification of class: '.get_class($notification)
            .' must define a `toGoogleChat()` method in order to send via the Google Chat Channel'
        );
    }

    /**
     * Thrown if a notification instance's toGoogleChat() method returns a value other than
     * an instance of \NotificationChannels\GoogleChat\GoogleChatMessage.
     *
     * @param  mixed  $actual
     * @return static
     */
    public static function invalidMessage($actual)
    {
        return new static(
            'Expected a message instance of type '.GoogleChatMessage::class
            .' - Actually received '
            .(
                is_object($actual)
                ? 'instance of: '.get_class($actual)
                : gettype($actual)
            )
        );
    }

    /**
     * Thrown if a message could not be built to an invalid argument being passed.
     *
     * @param  string  $method
     * @param  string  $expected
     * @param  mixed  $actual
     * @return static
     */
    public static function invalidArgument($method, $expected, $actual)
    {
        return new static(
            'Cannot pass '
            .(
                is_object($actual)
                ? 'object of type: '.get_class($actual)
                : gettype($actual)
            )
            ." to $method, expected: $expected"
        );
    }

    /**
     * Thrown if a notification is about to be sent, however no webhook could be found. This
     * exception means that:
     *      - No endpoint was configured in `google-chat.space`
     *      - No endpoint was manually specified whilst the notification was being constructed,
     *        using the `GoogleChatMessage::to()` method
     *      - The notifiable does not implement the `routeNotificationForGoogleChat(...)`
     *        method, or it returned an empty value.
     *
     * @return static
     */
    public static function webhookUnavailable()
    {
        return new static(
            'No webhook URL was available when sending the Google Chat notification.'
        );
    }

    /**
     * Thrown if a 400-level Http error was encountered whilst attempting to deliver the
     * notification.
     *
     * @return static
     */
    public static function clientError(ClientException $exception)
    {
        if (! $exception->hasResponse()) {
            return new static('Google Chat responded with an error but no response body was available');
        }

        $statusCode = $exception->getResponse()->getStatusCode();
        $description = $exception->getMessage();

        return new static(
            "Failed to send Google Chat message, encountered client error: `{$statusCode} - {$description}`"
        );
    }

    /**
     * Thrown if Google Chat API or Webhook returns an error response.
     *
     * @param  Response  $response
     * @return static
     */
    public static function serviceRespondedWithAnError($response)
    {
        return new static(
            "Failed to send Google Chat message, encountered HTTP status {$response->status()}: {$response->body()}"
        );
    }

    /**
     * Thrown if an unexpected exception was encountered whilst attempting to deliver the
     * notification.
     *
     * @return static
     */
    public static function unexpectedException(Exception $exception)
    {
        return new static(
            'Failed to send Google Chat message, unexpected exception encountered: `'.$exception->getMessage().'`',
            0,
            $exception
        );
    }

    /**
     * Thrown if service account credentials are missing.
     *
     * @return static
     */
    public static function missingCredentials()
    {
        return new static(
            'Google Chat Service Account driver requires `credentials` (JSON file path, JSON string, or array) to be configured.'
        );
    }

    /**
     * Thrown if service account credentials could not be parsed or are missing required keys.
     *
     * @return static
     */
    public static function invalidCredentials()
    {
        return new static(
            'Invalid Google Chat Service Account credentials. Expected a valid JSON key file, array, or JSON string containing `client_email` and `private_key`.'
        );
    }

    /**
     * Thrown if JWT signing fails with OpenSSL.
     *
     * @return static
     */
    public static function jwtSigningFailed()
    {
        return new static(
            'Failed to sign OAuth 2.0 JWT assertion using the provided Service Account private key via OpenSSL.'
        );
    }

    /**
     * Thrown if Google OAuth endpoint returns an error when fetching access token.
     *
     * @return static
     */
    public static function tokenFetchFailed(Response $response)
    {
        return new static(
            "Failed to fetch OAuth 2.0 access token from Google, HTTP status {$response->status()}: {$response->body()}"
        );
    }
}
