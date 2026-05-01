<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HelpChatController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:1000'],
            'messages' => ['nullable', 'array', 'max:20'],
            'messages.*.role' => ['required_with:messages', 'string', 'in:user,assistant'],
            'messages.*.text' => ['required_with:messages', 'string', 'max:2000'],
        ]);

        $baseUrl = rtrim((string) env('LM_STUDIO_BASE_URL', 'http://127.0.0.1:1234/v1'), '/');
        $model = $this->resolveModel($baseUrl);

        if ($model === null) {
            throw ValidationException::withMessages([
                'message' => 'The local LM Studio server is not ready. Start LM Studio, load a model, and enable the local server first.',
            ]);
        }

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are the CivicEase website assistant. Help users with reporting local issues, postcode lookup, maps, image upload, account access, report statuses, duplicate warnings, and admin workflow. Keep every answer short, practical, and under 120 words. Do not invent features that the website does not have.',
            ],
        ];

        foreach ($data['messages'] ?? [] as $message) {
            $messages[] = [
                'role' => $message['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => $message['text'],
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $data['message'],
        ];

        $requestBuilder = Http::timeout(60)->acceptJson();
        $apiKey = env('LM_STUDIO_API_KEY');

        if (is_string($apiKey) && $apiKey !== '') {
            $requestBuilder = $requestBuilder->withToken($apiKey);
        }

        try {
            $response = $requestBuilder->post($baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.2,
                'max_tokens' => 180,
            ]);
        } catch (ConnectionException) {
            throw ValidationException::withMessages([
                'message' => 'Unable to reach LM Studio. Make sure the local server is running on port 1234.',
            ]);
        }

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                'message' => 'LM Studio returned an error. Check that a model is loaded and the local API server is enabled.',
            ]);
        }

        $reply = (string) data_get($response->json(), 'choices.0.message.content', '');
        $reply = trim($reply);

        if ($reply === '') {
            throw ValidationException::withMessages([
                'message' => 'LM Studio did not return a reply. Try again after confirming the selected model is loaded.',
            ]);
        }

        return response()->json([
            'reply' => Str::limit($reply, 4000, ''),
        ]);
    }

    protected function resolveModel(string $baseUrl): ?string
    {
        $configuredModel = trim((string) env('LM_STUDIO_MODEL', ''));
        if ($configuredModel !== '') {
            return $configuredModel;
        }

        $requestBuilder = Http::timeout(15)->acceptJson();
        $apiKey = env('LM_STUDIO_API_KEY');

        if (is_string($apiKey) && $apiKey !== '') {
            $requestBuilder = $requestBuilder->withToken($apiKey);
        }

        try {
            $response = $requestBuilder->get($baseUrl . '/models');
        } catch (ConnectionException) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $models = data_get($response->json(), 'data', []);
        $firstModel = collect($models)->firstWhere('id');

        return is_array($firstModel) ? ($firstModel['id'] ?? null) : null;
    }
}
