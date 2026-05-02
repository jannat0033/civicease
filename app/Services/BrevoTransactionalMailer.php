<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use RuntimeException;

class BrevoTransactionalMailer
{
    public function sendEmailVerification(string $email, string $name, string $verificationUrl): void
    {
        $htmlContent = View::make('emails.verify-email', [
            'name' => $name,
            'verificationUrl' => $verificationUrl,
        ])->render();

        $this->sendMessage(
            toEmail: $email,
            toName: $name,
            subject: 'Verify your CivicEase email address',
            htmlContent: $htmlContent,
            tags: ['civicease-email-verification'],
        );
    }

    public function sendPasswordReset(string $email, string $name, string $resetUrl, int $expiresInMinutes): void
    {
        $htmlContent = View::make('emails.reset-password', [
            'name' => $name,
            'resetUrl' => $resetUrl,
            'expiresInMinutes' => $expiresInMinutes,
        ])->render();

        $this->sendMessage(
            toEmail: $email,
            toName: $name,
            subject: 'Reset your CivicEase password',
            htmlContent: $htmlContent,
            tags: ['civicease-password-reset'],
        );
    }

    protected function sendMessage(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlContent,
        array $tags = [],
    ): void {
        $apiKey = (string) config('services.brevo.api_key');

        if ($apiKey === '') {
            throw new RuntimeException('Brevo API key is missing. Set BREVO_API_KEY before sending email.');
        }

        $response = Http::acceptJson()
            ->asJson()
            ->timeout(20)
            ->withHeaders([
                'api-key' => $apiKey,
            ])
            ->post($this->endpointUrl(), [
                'sender' => [
                    'name' => config('mail.from.name', config('app.name', 'CivicEase')),
                    'email' => config('mail.from.address'),
                ],
                'to' => [[
                    'email' => $toEmail,
                    'name' => $toName,
                ]],
                'subject' => $subject,
                'htmlContent' => $htmlContent,
                'tags' => $tags,
            ]);

        try {
            $response->throw();
        } catch (RequestException $exception) {
            $status = $exception->response?->status() ?? 'unknown';
            $body = $exception->response?->body() ?? 'No response body returned.';

            throw new RuntimeException(
                sprintf('Brevo API email request failed with status %s. Response: %s', $status, $body),
                previous: $exception,
            );
        }
    }

    protected function endpointUrl(): string
    {
        return rtrim((string) config('services.brevo.base_url', 'https://api.brevo.com/v3'), '/').'/smtp/email';
    }
}
