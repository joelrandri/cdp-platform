<?php

use App\Controllers\EventController;
use App\Services\EventService;
use App\Repositories\EventRepository;
use Database\Connection\ConnectData;

$pdo = ConnectData::getConnection();

$eventRepository = new EventRepository($pdo);

$eventService = new EventService($eventRepository);

$eventController = new EventController($eventService);

$router->post('/api/events', [$eventController, 'store']);
