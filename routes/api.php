<?php

use App\Controllers\EventController;
use App\Services\EventService;
use App\Repositories\EventRepository;
use Database\Connection\ConnectData;
use App\Controllers\CustomerController;
use App\Services\CustomerService;
use App\Repositories\CustomerRepository;
use App\Controllers\SegmentController;
use App\Repositories\CustomerSegmentationRepository;
use App\Services\SegmentService;

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

$segmentationRepository = new CustomerSegmentationRepository($pdo);

$segmentService = new SegmentService(
    $segmentationRepository
);

$segmentController = new SegmentController(
    $segmentService
);

$router->get(
    '/api/segments/customers',
    [$segmentController, 'index']
);

$router->post(
    '/api/segments/query',
    [$segmentController, 'query']
);