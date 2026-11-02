<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\EventRepository;

class EventService
{
    public function __construct(
        private EventRepository $eventRepository
    ) {}

    public function validate(array $data): array
    {
        $errors = [];

        // Validation email
        if (
            !isset($data['email']) ||
            !is_string($data['email']) ||
            trim($data['email']) === ''
        ) {
            $errors[] = 'Le champ email est obligatoire';
        } elseif (
            !filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)
        ) {
            $errors[] = 'Le format de l’email est invalide';
        }

        // Validation name
        if (
            !isset($data['name']) ||
            !is_string($data['name']) ||
            trim($data['name']) === ''
        ) {
            $errors[] = 'Le champ name est obligatoire';
        }

        // Validation event_type
        if (
            !isset($data['event_type']) ||
            !is_string($data['event_type']) ||
            trim($data['event_type']) === ''
        ) {
            $errors[] = 'Le champ event type est obligatoire';
        }

        // Validation properties
        if (
            isset($data['properties']) &&
            !is_array($data['properties'])
        ) {
            $errors[] = 'Properties must be an object';
        } elseif (isset($data['properties'])) {
            foreach ($data['properties'] as $key => $value) {
                if (!is_string($key) || trim($key) === '') {
                    $errors[] = 'Invalid property name';
                    break;
                }

                if (
                    !is_scalar($value) &&
                    $value !== null &&
                    !is_array($value)
                ) {
                    $errors[] = 'Invalid property value';
                    break;
                }
            }
        }

        return $errors;
    }

    public function ingest(array $data): array
    {
        return $this->eventRepository->createEvent(
            trim($data['email']),
            trim($data['name']),
            trim($data['event_type']),
            $data['properties'] ?? []
        );
    }
}
