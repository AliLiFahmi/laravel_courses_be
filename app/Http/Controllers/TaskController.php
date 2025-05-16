<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Handle retrieving all tasks.
     */
    public function index(Request $request)
    {
        // Ambil user yang sedang login
        $owner = $request->user();

        $tasks = Task::with(['course', 'owner', 'documents'])->where('id', $owner->id)->get();

        $response = [
            'status'  => 'success',
            'message' => 'All tasks retrieved successfully',
            'data'    => $tasks,
        ];

        return response($response, 200);
    }

    /**
     * Handle retrieving a specific task by ID.
     */
    public function show(Request $request, $id)
    {
        // Ambil user yang sedang login
        $owner = $request->user();

        $task = Task::with(['course', 'owner', 'documents'])->where('id', $owner->id)->find($id);

        if (!$task) {
            $response = [
                'status'  => 'error',
                'message' => 'Task not found',
            ];

            return response($response, 404);
        }

        $response = [
            'status'  =>'success',
            'message' => 'Task retrieved successfully',
            'data'    => $task,
        ];

        return response($response, 200);
    }

    /**
     * Handle creating a new task.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline'    => 'nullable|date',
            'status'      => 'required|string|in:pending,ongoing,completed',
            'course_id'   => 'required|string|exists:courses,id',
        ]);

        if ($validator->fails()) {
            $response = [
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ];

            return response($response, 422);
        }

        // Ambil user yang sedang login
        $owner = $request->user();

        $task = new Task();
        $task->title       = $request->title;
        $task->description = $request->description;
        $task->deadline    = date("Y-m-d H:i:s", strtotime($request->deadline));
        $task->status      = $request->status;
        $task->course_id   = $request->course_id;
        $task->owner_id    = $owner->id;
        $task->save();

        $response = [
            'status'  =>'success',
            'message' => 'Task created successfully',
            'data'    => $task,
        ];

        return response($response, 201);
    }

    /**
     * Handle updating an existing task by ID.
     */
    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            $response = [
                 'status'  => 'error',
                'message' => 'Task not found',
            ];

            return response($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'deadline'    => 'nullable|date',
            'status'      => 'sometimes|required|string|in:pending,ongoing,completed',
            'course_id'   => 'sometimes|required|string|exists:courses,id',
        ]);

        if ($validator->fails()) {
            $response = [
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ];

            return response($response, 422);
        }

        $task->title       = $request->title;
        $task->description = $request->description;
        $task->deadline    = date("Y-m-d H:i:s", strtotime($request->deadline));
        $task->status      = $request->status;
        $task->course_id   = $request->course_id;

        $task->save();

        $response = [
            'status'  =>'success',
            'message' => 'Task updated successfully',
            'data'    => $task,
        ];

        return response($response, 200);
    }

    /**
     * Handle deleting a task by ID.
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            $response = [
                'status'  => 'error',
                'message' => 'Task not found',
            ];

            return response($response, 404);
        }

        $task->delete();

        $response = [
            'status'  =>'success',
            'message' => 'Task deleted successfully',
        ];

        return response($response, 200);
    }
}
