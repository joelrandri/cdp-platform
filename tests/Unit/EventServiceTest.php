<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repositories\EventRepository;
use App\Services\EventService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class EventServiceTest extends TestCase
{
   private EventRepository&MockObject $eventRepository;    
   private EventService $eventService;

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepository::class);

        $this->eventService = new EventService($this->eventRepository);
    }

    public function testValidDataReturnsNoErrors(): void
    {
        $data = [
            'email' => 'lala@test.com',
            'name' => 'lala Marie',
            'event_type' => 'purchase',
            'properties' => [
                'price' => 1200,
                'currency' => 'EUR'
            ]
        ];

        $errors = $this->eventService->validate($data);

        $this->assertSame([], $errors);
    }

    public function testMissingEmailReturnsError(): void
    {
        $data = [
            'name' => 'lala Marie',
            'event_type' => 'purchase'
        ];

        $errors = $this->eventService->validate($data);

        $this->assertContains(
            'Le champ email est obligatoire',
            $errors
        );
    }

    public function testInvalidEmailReturnsError(): void
    {
        $data = [
            'email' => 'email-invalide',
            'name' => 'lala Marie',
            'event_type' => 'purchase'
        ];

        $errors = $this->eventService->validate($data);

        $this->assertContains(
            'Le format de l’email est invalide',
            $errors
        );
    }

    public function testMissingNameReturnsError(): void
    {
        $data = [
            'email' => 'lala@test.com',
            'event_type' => 'purchase'
        ];

        $errors = $this->eventService->validate($data);

        $this->assertContains(
            'Le champ name est obligatoire',
            $errors
        );
    }

    public function testMissingEventTypeReturnsError(): void
    {
        $data = [
            'email' => 'lala@test.com',
            'name' => 'lala Marie'
        ];

        $errors = $this->eventService->validate($data);

        $this->assertContains(
            'Le champ event type est obligatoire',
            $errors
        );
    }

    public function testInvalidPropertiesReturnsError(): void
    {
        $data = [
            'email' => 'lala@test.com',
            'name' => 'lala Marie',
            'event_type' => 'purchase',
            'properties' => 'invalid-properties'
        ];

        $errors = $this->eventService->validate($data);

        $this->assertContains(
            'Properties must be an object',
            $errors
        );
    }

    public function testIngestCallsRepositoryWithCorrectData(): void
    {
        $data = [
            'email' => ' lala@test.com ',
            'name' => ' lala Marie ',
            'event_type' => ' purchase ',
            'properties' => [
                'price' => 1200,
                'currency' => 'EUR'
            ]
        ];

        $expectedResult = [
            'customer_id' => 1,
            'event_id' => 25
        ];

        // Vérifier que createEvent est appelé une seule fois
        // avec les données nettoyées par trim()
        $this->eventRepository
            ->expects($this->once())
            ->method('createEvent')
            ->with(
                'lala@test.com',
                'lala Marie',
                'purchase',
                $data['properties']
            )
            ->willReturn($expectedResult);

        $result = $this->eventService->ingest($data);

        $this->assertSame($expectedResult, $result);
    }
}