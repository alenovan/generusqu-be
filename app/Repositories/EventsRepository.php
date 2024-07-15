<?php

namespace App\Repositories;
use App\Traits\GlobalTrait;
use App\Models\Events;
use App\Models\EventDetails;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
class EventsRepository
{   
     use GlobalTrait;
    public function createEvent($request)
    {
        $event = new Events();
        $event->name = $request->name;
        $event->date = $request->date;
        $event->is_active = $request->is_active;

        DB::beginTransaction();
        try {
            $event->save();
            DB::commit();
            return "Event saved, room status updated";
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return $e;
        }
    }

    public function getEventById($eventId)
    {
        try {
            return Events::findOrFail($eventId);
        } catch (\Exception $e) {
            report($e);
            return null; // or handle error as needed
        }
    }

    public function getEventDetailById($eventId)
    {
        try {
            $data = EventDetails::with('users','event')->where('event_id', $eventId)->get();
            return $data;
        } catch (\Exception $e) {
            report($e);
            return $e; // or handle error as needed
        }
    }

    public function getEventHistory()
    {
        try {
            // Query EventDetails model with eager loading of 'event' relationship
            $eventDetails = EventDetails::with('event')->get();

            // Prepare the formatted data structure
            $formattedData = [];

            foreach ($eventDetails as $detail) {
                // Convert event date to Carbon instance
                $eventDate = Carbon::parse($detail->event->date);
                $date = $eventDate->format('d F Y'); // Format date as "01 October 2023"
                $status = $detail->status; // Assuming 'status' field exists in EventDetails

                // Check if the date already exists in formattedData array
                if (!isset($formattedData[$date])) {
                    $formattedData[$date] = [
                        'time' => "Hari Ini, {$date}",
                        'data' => [],
                    ];
                }

                // Push event detail data to 'data' array
                $formattedData[$date]['data'][] = [
                    'label' => $detail->event->name, // Assuming 'name' field exists in Event model
                    'status' => $status,
                ];
            }

            // Convert array to indexed array (optional, if needed)
            $formattedData = array_values($formattedData);
            return $formattedData;
        } catch (\Exception $e) {
            report($e);

            // Handle the exception (e.g., return error response)
            return response()->json(['error' => 'EventDetails not found'], 404);
        }
    }


    

    public function updateEvent($eventId, $request)
    {
        DB::beginTransaction();
        try {
            $event = Events::findOrFail($eventId);
            $event->name = $request->name;
            $event->date = $request->date;
            $event->is_active = $request->is_active;
            $event->save();
            DB::commit();
            return "Event updated successfully";
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return $e;
        }
    }

    public function deleteEvent($eventId)
    {
        DB::beginTransaction();
        try {
            $event = Events::findOrFail($eventId);
            $event->delete();
            DB::commit();
            return "Event deleted successfully";
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return $e;
        }
    }

    public function attendence($request)
    {
        // Find the event by ID
        $event = Events::findOrFail($request->event_id);

        // Check if event has ended (assuming is_active indicates this)
        if ($event->is_active == 0) {
            throw new Exception('Event has ended');
        }

        // Check if user has already attended the event
        $existingAttendance = EventDetails::where('event_id', $request->event_id)
                                          ->where('user_id', $request->user_id)
                                          ->first();

        if ($existingAttendance) {
            throw new Exception('User already attended this event');
        }

        // Determine attendance status based on current time and event date
        $eventDate = Carbon::parse($event->date);
        $currentTime = Carbon::now();
        $toleranceTime = $eventDate->copy()->addMinutes(10);
        if ($currentTime < $eventDate) {
            $status = 'INTIME';
        } elseif ($currentTime <= $toleranceTime) {
            $status = 'ONTIME';
        } else {
            $status = 'LATE';
        }

        // Prepare and save attendance details
        $eventDetail = new EventDetails();
        $eventDetail->user_id = $request->user_id;
        $eventDetail->event_id = $request->event_id;
        $eventDetail->status = $status;
        $eventDetail->late_permission =$request->late_permission;

        DB::beginTransaction();
        try {
            $eventDetail->save();
            DB::commit();
            return "Attendance recorded successfully";
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to record attendance');
        }
    }

}
