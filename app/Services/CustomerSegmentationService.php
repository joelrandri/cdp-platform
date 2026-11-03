<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\CustomerSegmentationRepository;
use InvalidArgumentException;

class CustomerSegmentationService
{
    public function __construct(
        private CustomerSegmentationRepository $repository
    ) {}

    public function getSegmentedCustomers(
        int $minPurchases,
        float $minSpend
    ): array {

        // Check the segmentation criteria
        if ($minPurchases < 0) {
            throw new InvalidArgumentException(
                'Minimum purchases cannot be negative'
            );
        }

        if (!is_finite($minSpend) || $minSpend < 0) {
            throw new InvalidArgumentException(
                'Minimum spend must be a valid positive number'
            );
        }

        // Retrieve customers matching the criteria
        return $this->repository->findCustomersBySegment(
            $minPurchases,
            $minSpend
        );
    }

    public function querySegments(array $conditions): array
    {
        if (
            empty($conditions) ||
            !array_is_list($conditions) ||
            count($conditions) > 10
        ) {
            throw new InvalidArgumentException(
                'Provide between 1 and 10 conditions'
            );
        }

        $allowedOperators = ['=', '!=', '>', '<', '>=', '<='];

        foreach ($conditions as $condition) {
            if (!is_array($condition)) {
                throw new InvalidArgumentException('Invalid condition');
            }

            foreach (['event', 'property', 'operator', 'value'] as $field) {
                if (!array_key_exists($field, $condition)) {
                    throw new InvalidArgumentException(
                        "Missing field: $field"
                    );
                }
            }

            if (
                !is_string($condition['event']) ||
                trim($condition['event']) === '' ||
                !is_string($condition['property']) ||
                trim($condition['property']) === ''
            ) {
                throw new InvalidArgumentException(
                    'Event and property must be non-empty strings'
                );
            }

            if (
                !is_string($condition['operator']) ||
                !in_array($condition['operator'], $allowedOperators, true)
            ) {
                throw new InvalidArgumentException('Invalid operator');
            }

            if (
                !is_numeric($condition['value']) ||
                !is_finite((float) $condition['value'])
            ) {
                throw new InvalidArgumentException(
                    'Condition value must be numeric'
                );
            }
        }

        return $this->repository->findCustomersByConditions($conditions);
    }
}