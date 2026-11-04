<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CustomerSegmentationRepository;
use InvalidArgumentException;

class SegmentService
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


    public function findCustomers(array $data): array
    {
        $logic = strtoupper($data['logic'] ?? 'AND');

        if (!in_array($logic, ['AND', 'OR'], true)) {
            throw new InvalidArgumentException(
                'Logic must be AND or OR'
            );
        }

        $conditions = $data['conditions'] ?? null;

        if (
            !is_array($conditions) ||
            !array_is_list($conditions) ||
            count($conditions) < 1 ||
            count($conditions) > 10
        ) {
            throw new InvalidArgumentException(
                'Provide between 1 and 10 conditions'
            );
        }

        $validatedConditions = [];

        foreach ($conditions as $condition) {

            if (!is_array($condition)) {
                throw new InvalidArgumentException(
                    'Each condition must be an object'
                );
            }

            $eventType = $condition['event_type'] ?? '';
            $property = $condition['property'] ?? null;
            $operator = $condition['operator'] ?? null;
            $value = $condition['value'] ?? null;

            if (
                !is_string($eventType) ||
                trim($eventType) === '' ||
                !is_string($property) ||
                trim($property) === ''
            ) {
                throw new InvalidArgumentException(
                    'Event and property are required'
                );
            }

            if (!is_string($operator)) {
                throw new InvalidArgumentException(
                    'Invalid operator'
                );
            }

            $operator = strtolower(trim($operator));

            // Déterminer le type de la valeur
            if (is_int($value) || is_float($value)) {
                $type = 'number';
            } elseif (is_string($value)) {
                $type = 'string';
            } else {
                throw new InvalidArgumentException(
                    'Value must be a number or a string'
                );
            }

            if ($type === 'number') {

                $allowedOperators = [
                    '=', '!=', '>', '<', '>=', '<='
                ];

                if (!is_finite((float) $value)) {
                    throw new InvalidArgumentException(
                        'Invalid numeric value'
                    );
                }

            } else {

                $allowedOperators = [
                    '=', '!=',
                    'contains',
                    'starts_with',
                    'ends_with'
                ];
            }

            if (!in_array($operator, $allowedOperators, true)) {
                throw new InvalidArgumentException(
                    'Invalid operator for value type'
                );
            }

            $validatedConditions[] = [
                'event' => trim($eventType),
                'property' => trim($property),
                'type' => $type,
                'operator' => $operator,
                'value' => $value
            ];
        }

        return $this->repository->findCustomersByConditions(
            $validatedConditions,
            $logic
        );
    }
}
