<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\EventService;
use JsonException;
use Throwable;

class EventController
{
    public function __construct(
        private EventService $eventService
    ) {}

    public function store(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // 1. Retrieve query content
        $input = file_get_contents('php://input');

        if ($input === false || trim($input) === '') {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Request body is empty'
            ]);

            return;
        }

        // 2. Decode  JSON
        try {
            $data = json_decode(
                $input,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON'
            ]);

            return;
        }

        if (!is_array($data)) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'JSON body must be an object'
            ]);

            return;
        }

        // 3. Validate the data
        $errors = $this->eventService->validate($data);

        if (!empty($errors)) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);

            return;
        }

        // 4. Save event
        try {
            $result = $this->eventService->ingest($data);

            http_response_code(201);

            echo json_encode([
                'success' => true,
                'message' => 'Event created successfully',
                'data' => $result
            ]);

        } catch (Throwable $e) {
            error_log($e->getMessage());

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'An internal server error occurred'
            ]);
        }
    }
}
