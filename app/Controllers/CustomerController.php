<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\CustomerService;

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
}