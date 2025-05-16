<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    // Get all documents
    public function index(Request $request)
    {
        // Ambil user yang sedang login
        $owner = $request->user();

        $documents = Document::with(['task', 'owner'])->where('id', $owner->id)->get();

        $response = [
            'status'  => 'success',
            'message' => 'Documents retrieved successfully',
            'data'    => $documents
        ];

        return response($response, 200);
    }

    // Create a new document
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'    => 'required|file|max:5120',
            'task_id' => 'sometimes|required|string|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            $response = [
               'status'  =>'error',
               'message' => 'Validation failed',
               'errors'  => $validator->errors()
            ];

            return response($response, 422);
        }

        // Ambil user yang sedang login
        $owner = $request->user();

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();

        $randomDigits = Str::random(8);
        $uniqueName = 'Docs' . $randomDigits . '.' . $request->file('file')->getClientOriginalExtension();
        $path = Storage::putFileAs('documents', $request->file('file'), $uniqueName);

        $document = new Document();
        $document->content_title = $originalName;
        $document->content_type  = $mimeType;
        $document->content_path  = $path;
        $document->task_id       = $request->task_id;
        $document->owner_id      = $owner->id;
        $document->save();

        $response = [
            'status'  =>'success',
            'message' => 'Document created successfully',
            'data'    => $document
        ];

        return response($response, 201);
    }

    // Get one document by ID
    public function show(Request $request, $id)
    {
        // Ambil user yang sedang login
        $owner = $request->user();

        $document = Document::with(['task', 'owner'])->where('id', $owner->id)->find($id);

        if (!$document) {
            $response = [
             'status'  =>'error',
             'message' => 'Document not found',
            ];

            return response($response, 404);
        }

        $response = [
            'status'  =>'success',
            'message' => 'Document retrieved successfully',
            'data'    => $document
        ];

        return response($response, 200);
    }

    // Update a document
    public function update(Request $request, $id)
    {
        $document = Document::find($id);

        if (!$document) {
            $response = [
                'status'  =>'error',
                'message' => 'Document not found',
            ];

            return response($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'file'    => 'required|file|max:5120',
            'task_id' => 'sometimes|required|string|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            $response = [
                'status'  =>'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ];

            return response($response, 422);
        }

        // Ambil user yang sedang login
        $owner = $request->user();

        // Hapus gambar lama
        if (Storage::exists($document->content_path)) {
            Storage::delete($document->content_path);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();

        $randomDigits = Str::random(8);
        $uniqueName = 'Docs' . $randomDigits . '.' . $request->file('file')->getClientOriginalExtension();
        $path = Storage::putFileAs('documents', $request->file('file'), $uniqueName);

        $document->content_title = $originalName;
        $document->content_type  = $mimeType;
        $document->content_path  = $path;
        $document->task_id       = $request->task_id;
        $document->owner_id      = $owner->id;
        $document->save();

        $response = [
            'status'  =>'success',
            'message' => 'Document updated successfully',
            'data'    => $document
        ];

        return response($response, 200);
    }

    // Delete a document
    public function destroy($id)
    {
        $document = Document::find($id);

        if (!$document) {
            $response = [
                'status'  =>'error',
                'message' => 'Document not found',
            ];

            return response($response, 404);
        }

        $document->delete();

        $response = [
           'status'  =>'success',
           'message' => 'Document deleted successfully',
        ];

        return response($response, 200);
    }
}
