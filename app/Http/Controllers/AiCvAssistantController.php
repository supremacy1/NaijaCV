<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AiCvAssistantController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'section' => [
                'required',
                Rule::in([
                    'professional_summary',
                    'additional_details',
                    'responsibilities_achievements',
                ]),
            ],

            'action' => [
                'required',
                Rule::in([
                    'generate',
                    'improve',
                    'expand',
                    'shorten',
                ]),
            ],

            'text' => ['nullable', 'string', 'max:5000'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:150'],
            'experience_level' => ['nullable', 'string', 'max:100'],
            'extra_context' => ['nullable', 'string', 'max:2000'],
        ]);

        // GROQ CONFIGURATION
        $apiKey = config('services.groq.api_key');
        $model = config('services.groq.model', 'openai/gpt-oss-20b');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'AI Assistant is not configured yet. Please try again later.',
            ], 503);
        }

        $sectionNames = [
            'professional_summary' => 'Professional Summary',
            'additional_details' => 'Additional Details',
            'responsibilities_achievements' => 'Responsibilities and Achievements',
        ];

        $section = $sectionNames[$validated['section']];
        $action = $validated['action'];
        $existingText = trim($validated['text'] ?? '');

        if ($action !== 'generate' && $existingText === '') {
            return response()->json([
                'success' => false,
                'message' => 'Please enter some text before asking AI to improve it.',
            ], 422);
        }

        if (
            $action === 'generate' &&
            empty($validated['extra_context']) &&
            empty($validated['job_title'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a job title or some information about your experience first.',
            ], 422);
        }

        $actionInstructions = [
            'generate' => 'Create professional CV content from the information provided.',
            'improve' => 'Rewrite the supplied text to make it clearer, more professional and impactful.',
            'expand' => 'Expand the supplied text with relevant detail while keeping it truthful.',
            'shorten' => 'Make the supplied text shorter, clearer and more direct.',
        ];

        $instructions = <<<PROMPT
You are the professional CV-writing assistant for NaijaCV.

Your task is to help job seekers write clear, credible and professional CV content.

Rules:
- Use professional English suitable for job applications.
- Make the writing natural, specific and easy to understand.
- Never invent qualifications, employers, job titles, dates, awards, certifications, numbers or achievements.
- Do not claim the applicant has experience or skills they have not provided.
- If information is limited, write carefully without making unsupported claims.
- Avoid generic exaggerations and empty buzzwords.
- Do not include markdown headings such as "Here is your summary".
- Return only the finished CV content.
- For responsibilities and achievements, use concise bullet points, one responsibility per line.
- For a professional summary, write a polished paragraph.
- Keep the result appropriate for the applicant's experience level.

Section: {$section}

Requested action: {$actionInstructions[$action]}
PROMPT;

        $userInput = [
            'Existing text: ' . ($existingText ?: 'No existing text. Generate new content.'),
            'Job title: ' . ($validated['job_title'] ?? 'Not provided'),
            'Industry: ' . ($validated['industry'] ?? 'Not provided'),
            'Experience level: ' . ($validated['experience_level'] ?? 'Not provided'),
            'Additional context: ' . ($validated['extra_context'] ?? 'Not provided'),
        ];

        try {

            // SEND REQUEST TO GROQ
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(60)
                ->post('https://api.groq.com/openai/v1/chat/completions', [

                    'model' => $model,

                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $instructions,
                        ],
                        [
                            'role' => 'user',
                            'content' => implode("\n", $userInput),
                        ],
                    ],

                    'temperature' => 0.7,

                    'max_completion_tokens' => 700,
                ]);

            // HANDLE API ERRORS
            if (!$response->successful()) {

                Log::warning('NaijaCV Groq API request failed', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                $message = match ($response->status()) {
                    401, 403 => 'The AI service configuration is invalid. Please contact support.',
                    429 => 'The AI assistant has reached its usage limit. Please try again later.',
                    400 => 'The AI assistant could not process this request. Please try again.',
                    default => 'The AI assistant could not complete your request. Please try again.',
                };

                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 502);
            }

            // EXTRACT GROQ RESPONSE
            $data = $response->json();

            $generatedText = trim(
                $data['choices'][0]['message']['content'] ?? ''
            );

            if ($generatedText === '') {

                Log::warning('NaijaCV Groq returned an empty response', [
                    'model' => $model,
                    'response' => $data,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'AI returned an empty response. Please try again.',
                ], 502);
            }

            // SUCCESS
            return response()->json([
                'success' => true,
                'text' => $generatedText,
            ]);

        } catch (\Throwable $exception) {

            Log::error('NaijaCV Groq AI assistant error', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while contacting the AI assistant. Please try again.',
            ], 500);
        }
    }
}
