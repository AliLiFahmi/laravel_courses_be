<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Handle get all Courses.
     */
    public function index()
    {
        $courses = Course::with(['owner', 'tasks'])->get();

        $response = [
            'status'  => 'success',
            'message' => 'Courses retrieved successfully',
            'data'    => $courses,
        ];

        return response($response, 200);
    }

    /**
     * Handle create Course.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_name'  => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Ambil user yang sedang login
        $owner = $request->user();

        // Isi properti satu per satu
        $course = new Course();
        $course->title       = $request->title;
        $course->description = $request->description;
        $course->class_name  = $request->class_name;
        $course->owner_id    = $owner->id;
        $course->save();

        return response([
            'status'  => 'success',
            'message' => 'Course created successfully',
            'data'    => $course,
        ], 201);
    }

    /**
     * Handle show by id Course.
     */
    public function show($id)
    {
        $course = Course::with(['owner', 'tasks'])->find($id);

        if (!$course) {
            $response = [
              'status'  =>'error',
              'message' => 'Course not found',
            ];

            return response($response, 404);
        }

        $response = [
            'status'  =>'success',
            'message' => 'Course retrieved successfully',
            'data'    => $course
        ];

        return response($response, 200);
    }

    /**
     * Handle update Course.
     */
    public function update(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response([
                'status'  => 'error',
                'message' => 'Course not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'class_name'  => 'sometimes|required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Update field satu per satu sesuai request
        $course->title = $request->title;
        $course->description = $request->description;
        $course->class_name = $request->class_name;
        $course->save();

        return response([
            'status'  => 'success',
            'message' => 'Course updated successfully',
            'data'    => $course,
        ], 200);
    }

    /**
     * Handle delete Course.
     */
    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            $response = [
              'status'  =>'error',
              'message' => 'Course not found',
            ];

            return response($response, 404);
        }

        $course->delete();

        $response = [
           'status'  =>'success',
           'message' => 'Course deleted successfully',
        ];

        return response($response, 200);
    }
}
