<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\SegmentService;
use InvalidArgumentException;
use JsonException;
use Throwable;

class SegmentController
{
    public function __construct(
        private SegmentService $segmentService
    ) {}

    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $minPurchases = filter_input(
                INPUT_GET,
                'min_purchases',
                FILTER_VALIDATE_INT
            );

            $minSpend = filter_input(
                INPUT_GET,
                'min_spend',
                FILTER_VALIDATE_FLOAT
            );

            if ($minPurchases === false || $minPurchases === null) {
                throw new InvalidArgumentException(
                    'Invalid min_purchases parameter'
                );
            }

            if ($minSpend === false || $minSpend === null) {
                throw new InvalidArgumentException(
                    'Invalid min_spend parameter'
                );
            }

            $customers = $this->segmentService->getSegmentedCustomers(
                $minPurchases,
                $minSpend
            );

            http_response_code(200);

            echo json_encode([
                'success' => true,
                'data' => $customers
            ]);

        } catch (InvalidArgumentException $e) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function query(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = json_decode(
                file_get_contents('php://input'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            if (!is_array($input)) {
                throw new InvalidArgumentException(
                    'Invalid request body'
                );
            }

            $customers = $this->segmentService->findCustomers($input);

            http_response_code(200);

            echo json_encode([
                'success' => true,
                'count' => count($customers),
                'data' => $customers
            ], JSON_THROW_ON_ERROR);

        } catch (JsonException $e) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON'
            ]);

        } catch (InvalidArgumentException $e) {

            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);

        } catch (Throwable $e) {

            http_response_code(500);

            error_log($e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'Internal server error'
            ]);
        }
    }
}
