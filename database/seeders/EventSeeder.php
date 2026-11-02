<?php

declare(strict_types=1);

namespace Database\Seeders;

use PDO;
use RuntimeException;

class EventSeeder
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function run(): void
    {
        // Data of events
        $events = [
            [
                'email' => 'julien@test.com',
                'event_type' => 'page_view',
                'properties' => [
                    'page_url' => '/products',
                    'page_title' => 'Liste des produits',
                ],
            ],
            [
                'email' => 'julien@test.com',
                'event_type' => 'purchase',
                'properties' => [
                    'product_name' => 'Ordinateur portable',
                    'price' => '1200',
                    'currency' => 'EUR',
                    'quantity' => '1',
                ],
            ],
            [
                'email' => 'miora@test.com',
                'event_type' => 'product_view',
                'properties' => [
                    'product_name' => 'Smartphone',
                    'category' => 'Électronique',
                ],
            ],
            [
                'email' => 'marcus@test.com',
                'event_type' => 'add_to_cart',
                'properties' => [
                    'product_name' => 'Casque audio',
                    'quantity' => '2',
                ],
            ],
            [
                'email' => 'felana@test.com',
                'event_type' => 'purchase',
                'properties' => [
                    'product_name' => 'Clavier',
                    'price' => '80',
                    'currency' => 'EUR',
                    'quantity' => '1',
                ],
            ],
            [
                'email' => 'tojo@test.com',
                'event_type' => 'page_view',
                'properties' => [
                    'page_url' => '/about',
                    'page_title' => 'À propos',
                ],
            ],
        ];

        // Prepare the customer research by her mail
        $customerStmt = $this->pdo->prepare(
            "SELECT id FROM customers WHERE email = :email"
        );

        // Prepare event insert
        $eventStmt = $this->pdo->prepare(
            "INSERT INTO events (customer_id, event_type)
             VALUES (:customer_id, :event_type)"
        );

        // Préparer l'insertion des propriétés
        $propertyStmt = $this->pdo->prepare(
            "INSERT INTO event_properties
                (event_id, property_name, property_value)
             VALUES
                (:event_id, :property_name, :property_value)"
        );

        foreach ($events as $event) {

            // Search customer
            $customerStmt->execute([
                'email' => $event['email'],
            ]);

            $customer = $customerStmt->fetch();

            // Check if customer not exist
            if ($customer === false) {
                throw new RuntimeException(
                    "Customer not found : " . $event['email']
                );
            }

            // Insert event
            $eventStmt->execute([
                'customer_id' => $customer['id'],
                'event_type' => $event['event_type'],
            ]);

            // Retrieve id of new event
            $eventId = (int) $this->pdo->lastInsertId();

            // Insert the event's properties
            foreach ($event['properties'] as $propertyName => $propertyValue) {

                $propertyStmt->execute([
                    'event_id' => $eventId,
                    'property_name' => $propertyName,
                    'property_value' => $propertyValue,
                ]);
            }
        }
    }
}
