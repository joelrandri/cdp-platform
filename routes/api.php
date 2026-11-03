<?php

use App\Controllers\EventController;
use App\Services\EventService;
use App\Repositories\EventRepository;
use Database\Connection\ConnectData;
use App\Controllers\CustomerController;
use App\Services\CustomerService;
use App\Repositories\CustomerRepository;
use App\Controllers\CustomerSegmentationController;
use App\Services\CustomerSegmentationService;
use App\Repositories\CustomerSegmentationRepository;

$pdo = ConnectData::getConnection();

// Events

$eventRepository = new EventRepository($pdo);

$eventService = new EventService($eventRepository);

$eventController = new EventController($eventService);

$router->post('/api/events', [$eventController, 'store']);

// Customers

$customerRepository = new CustomerRepository($pdo);

$customerService = new CustomerService($customerRepository);

$customerController = new CustomerController($customerService);

// Customers

$router->getDynamic(
    '/api/customers/{id}',
    [$customerController, 'show']
);

$router->get(
    '/api/customers',
    [$customerController, 'index']
);

// Customer Segmentation

$customerSegmentationRepository =
    new CustomerSegmentationRepository($pdo);

$customerSegmentationService =
    new CustomerSegmentationService($customerSegmentationRepository);

$customerSegmentationController =
    new CustomerSegmentationController($customerSegmentationService);

$router->get(
    '/api/segments/customers',
    [$customerSegmentationController, 'index']
);

$router->post(
    '/api/segments/query',
    [$customerSegmentationController, 'query']
);