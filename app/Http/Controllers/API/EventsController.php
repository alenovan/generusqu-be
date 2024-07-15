<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EventsRepository;

class EventsController extends Controller
{
    private $repository;

    public function __construct()
    {
        $this->repository = new EventsRepository();
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'date' => 'required',
                'is_active' => 'required|boolean', // Assuming 'is_active' is part of the request
            ]);

            $insert =  $this->repository->createEvent($request);
            return response()->success($insert, 'Event created successfully');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), 400);
        }
    }

    public function show($id)
    {
        try {
            $event = $this->repository->getEventById($id);
            if (!$event) {
                return response()->error('Event not found', 404);
            }
            return response()->success($event, 'Event retrieved successfully');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'date' => 'required',
                'is_active' => 'required|boolean',
            ]);

            $update = $this->repository->updateEvent($id, $request);
            return response()->success($update, 'Event updated successfully');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), 400);
        }
    }

    public function destroy($id)
    {
        try {
            $delete = $this->repository->deleteEvent($id);
            return response()->success($delete, 'Event deleted successfully');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), 400);
        }
    }

    public function attendence(Request $request)
    {
        try {
            $this->validate($request, [
                'event_id' => 'required',
                'user_id' => 'required|exists:users,id',
                'late_permission' => 'required',
            ]);

            $insert =  $this->repository->attendence($request);
            return response()->success($insert, 'Event created successfully');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), 400);
        }
    }

    public function showDetail($id)
    {
        try {
            $event = $this->repository->getEventDetailById($id);
            if (!$event) {
                return response()->error('Event not found', 404);
            }
            return response()->success($event, 'Event retrieved successfully');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), 400);
        }
    }

    public function history()
    {
        try {
            $event = $this->repository->getEventHistory();
            return response()->success($event, 'Event retrieved successfully');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), 400);
        }
    }



}

