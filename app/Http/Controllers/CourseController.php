<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;
use App\Models\Module;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class CourseController extends Controller
{

     public function index()
    {
        $courses = Course::with(['modules.contents'])->latest()->paginate(10);
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $course = Course::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category' => $data['category'] ?? null,
                'meta' => [],
            ]);

            foreach ($data['modules'] as $moduleIndex => $moduleData) {
                $module = Module::create([
                    'course_id' => $course->id,
                    'title' => $moduleData['title'],
                    'description' => $moduleData['description'] ?? null,
                    'position' => $moduleData['position'] ?? $moduleIndex,
                ]);

                $contents = $moduleData['contents'] ?? [];
                foreach ($contents as $contentIndex => $contentData) {
                    $contentFields = [
                        'module_id' => $module->id,
                        'type' => $contentData['type'],
                        'text' => $contentData['text'] ?? null,
                        'video_url' => $contentData['video_url'] ?? null,
                        'link' => $contentData['link'] ?? null,
                        'position' => $contentData['position'] ?? $contentIndex,
                        'meta' => [],
                    ];

                    $fileKey = "modules.{$moduleIndex}.contents.{$contentIndex}.image";
                    if ($request->hasFile($fileKey)) {
                        $file = $request->file($fileKey);
                        if ($file && $file->isValid()) {
                            $path = $file->store('course_contents', 'public');
                            $contentFields['image_path'] = $path;
                        }
                    }

                    Content::create($contentFields);
                }
            }

            DB::commit();

            return redirect()->route('courses.index')
                ->with('success', 'Course created successfully!');

        } catch (Exception $e) {
            DB::rollBack();

            \Log::error('Course creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to save course. Please try again.']);
        }
    }
}
