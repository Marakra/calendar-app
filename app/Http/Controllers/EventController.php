<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class EventController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        try {
            $title = $request->input('title');
            $description = $request->input('description');
            $startDate = $request->date('startDate', 'Y-m-d');
            $endDate = $request->date('endDate', 'Y-m-d');
            $priority = $request->integer('priority', 1);

            $this->validateRequest($title, $description, $startDate, $endDate);

            Event::create([
                'title' => $title,
                'description' => $description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'priority' => $priority,
            ]);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse([
                'error' => $exception->getMessage(),
                'property' => $exception->getCode(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show(Request $request): JsonResponse
    {
        $startDate = $request->date('start', DateTimeInterface::ATOM);
        $endDate = $request->date('end', DateTimeInterface::ATOM);

        $result = Event::query()
            ->where('start_date', '>=', $startDate)
            ->where(function (Builder $query) use ($endDate) {
                $query->where('end_date', '<', $endDate)
                    ->orWhere('end_date', null);
            })
            ->get()
            ->map(function (Event $event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_date->format('Y-m-d'),
                    'end' => $event->end_date?->addDay()->format('Y-m-d'),
                    'extendedProps' => [
                        'description' => $event->description,
                        'priority' => $event->priority,
                    ],
                    'className' => $event->is_completed ? 'finished text' : 'text',
                    'backgroundColor' => match ($event->priority) {
                        1 => '#0dcaf0',
                        2 => '#ffc107',
                        3 => '#dc3545',
                    },
                    'borderColor' => match ($event->priority) {
                        1 => '#0dcaf0',
                        2 => '#ffc107',
                        3 => '#dc3545',
                    },
                    'textColor' => match ($event->priority) {
                        1, 2 => 'black',
                        3 => 'white',
                    },
                ];
            });

        return new JsonResponse($result);
    }

    public function edit(Event $event, Request $request): JsonResponse
    {
        try {
            $title = $request->input('title');
            $description = $request->input('description');
            $startDate = $request->date('startDate', 'Y-m-d');
            $endDate = $request->date('endDate', 'Y-m-d');
            $priority = $request->integer('priority', 1);

            $this->validateRequest($title, $description, $startDate, $endDate);

            $event->update([
                'title' => $title,
                'description' => $description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'priority' => $priority,
            ]);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse([
                'error' => $exception->getMessage(),
                'property' => $exception->getCode(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(Event $event): JsonResponse
    {
        $event->delete();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function complete(Event $event): JsonResponse
    {
        $event->update([
            'is_completed' => true,
        ]);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function validateRequest(?string $title, ?string $description, ?Carbon $startDate, ?Carbon $endDate): void
    {
        if (!$title) {
            throw new InvalidArgumentException('Title can not be null', 1);
        }

        $titleLength = strlen($title);

        if ($titleLength < 1) {
            throw new InvalidArgumentException('Title length can not be less than 1 char', 1);
        }

        if ($titleLength > 254) {
            throw new InvalidArgumentException('Title length can not be bigger than 254 chars', 1);
        }

        if (!$startDate) {
            throw new InvalidArgumentException('Start date can not be null', 2);
        }

        if ($endDate && $endDate < $startDate) {
            throw new InvalidArgumentException('End date can not be lower that start date', 2);
        }
    }
}
