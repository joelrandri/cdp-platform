<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CustomerRepository;

class CustomerService
{
    public function __construct(
        private CustomerRepository $customerRepository
    ) {
    }

    public function getCustomerProfile(int $customerId): ?array
    {
        $customer = $this->customerRepository->findById($customerId);

        if (!$customer) {
            return null;
        }

        return [
            'customer' => $customer,

            'statistics' => [
                'total_purchases' => $this->customerRepository
                    ->countPurchases($customerId),

                'total_spend' => $this->customerRepository
                    ->getTotalSpend($customerId)
            ],

            'recent_events' => $this->customerRepository
                ->getRecentEvents($customerId)
        ];
    }
}