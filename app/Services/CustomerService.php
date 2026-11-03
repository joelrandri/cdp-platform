<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CustomerRepository;
use InvalidArgumentException;
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

    public function getCustomers(int $page, int $limit): array
    {
        if ($page < 1) {
            throw new InvalidArgumentException(
                'Page must be greater than or equal to 1'
            );
        }

        if ($limit < 1 || $limit > 100) {
            throw new InvalidArgumentException(
                'Limit must be between 1 and 100'
            );
        }

        $offset = ($page - 1) * $limit;

        $customers = $this->customerRepository->findAll($limit, $offset);
        $total = $this->customerRepository->countAll();

        return [
            'customers' => $customers,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_customers' => $total,
                'total_pages' => (int) ceil($total / $limit)
            ]
        ];
    }

}