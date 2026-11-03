<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\CustomerSegmentationService;
use InvalidArgumentException;
use JsonException;
use Throwable;

class CustomerSegmentationController
{
    public function __construct(
        private CustomerSegmentationService $service
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

            $customers = $this->service->getSegmentedCustomers(
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

            if (
                !is_array($input) ||
                !isset($input['conditions']) ||
                !is_array($input['conditions'])
            ) {
                throw new InvalidArgumentException(
                    'A conditions array is required'
                );
            }

            $customers = $this->service->querySegments(
                $input['conditions']
            );

            http_response_code(200);

            echo json_encode([
                'success' => true,
                'data' => $customers,
                'total' => count($customers)
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
            error_log($e->getMessage());

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Internal server error'
            ]);
        }
    }
}