<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\CustomerService;
use InvalidArgumentException;
use Throwable;
use App\Core\Constants;
class CustomerController
{
    public function __construct(
        private CustomerService $customerService
    ) {
    }

    public function show(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $profile = $this->customerService->getCustomerProfile($id);

        if ($profile === null) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Customer not found'
            ]);

            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'data' => $profile
        ]);
    }

    public function index(): void
{
    header('Content-Type: application/json; charset=utf-8');

    try {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);

        // Default value
        $page = $page === null ? Constants::DEFAULT_PAGE : $page;
        $limit = $limit === null ? Constants::DEFAULT_PER_PAGE : $limit;

        if ($page === false || $limit === false) {
            throw new InvalidArgumentException(
                'Page and limit must be integers'
            );
        }

        $result = $this->customerService->getCustomers($page, $limit);

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'data' => $result['customers'],
            'pagination' => $result['pagination']
        ], JSON_THROW_ON_ERROR);

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