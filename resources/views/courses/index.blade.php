@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1><i class="fas fa-graduation-cap"></i> All Courses</h1>
        <p class="text-muted mb-0">Manage your course collection</p>
    </div>
    <a href="{{ route('courses.create') }}" class="btn btn-primary btn-lg">
        <i class="fas fa-plus"></i> Create New Course
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


@if($courses->count() > 0)
    <div class="row" id="coursesGrid">
        @foreach($courses as $course)
            <div class="col-md-6 col-lg-4 mb-4 course-card">
                <div class="card h-100 shadow-sm hover-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title text-truncate" style="max-width: 200px;" title="{{ $course->title }}">
                                {{ $course->title }}
                            </h5>
                            @if($course->category)
                                <span class="badge bg-secondary">{{ $course->category }}</span>
                            @endif
                        </div>

                        <p class="card-text text-muted">
                            {{ $course->description ? Str::limit($course->description, 100) : 'No description provided.' }}
                        </p>

                        <!-- Course Stats -->
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="stats-item">
                                    <i class="fas fa-layer-group text-primary"></i>
                                    <small class="d-block">{{ $course->modules->count() }} Modules</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-item">
                                    <i class="fas fa-file-alt text-success"></i>
                                    <small class="d-block">{{ $course->total_contents }} Contents</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-item">
                                    <i class="fas fa-clock text-info"></i>
                                    <small class="d-block">{{ $course->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>

                        @php
                            $contentTypes = [];
                            foreach($course->modules as $module) {
                                foreach($module->contents as $content) {
                                    $contentTypes[$content->type] = ($contentTypes[$content->type] ?? 0) + 1;
                                }
                            }
                        @endphp

                        @if(!empty($contentTypes))
                            <div class="content-types-breakdown mb-3">
                                <small class="text-muted d-block mb-1">Content Types:</small>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($contentTypes as $type => $count)
                                        @php
                                            $typeConfig = [
                                                'text' => ['icon' => 'fas fa-align-left', 'color' => 'primary', 'label' => 'Text'],
                                                'image' => ['icon' => 'fas fa-image', 'color' => 'success', 'label' => 'Images'],
                                                'video' => ['icon' => 'fas fa-video', 'color' => 'danger', 'label' => 'Videos'],
                                                'link' => ['icon' => 'fas fa-link', 'color' => 'info', 'label' => 'Links'],
                                                'html' => ['icon' => 'fas fa-code', 'color' => 'warning', 'label' => 'HTML']
                                            ];
                                            $config = $typeConfig[$type] ?? ['icon' => 'fas fa-file', 'color' => 'secondary', 'label' => ucfirst($type)];
                                        @endphp
                                        <span class="badge bg-{{ $config['color'] }} badge-sm" title="{{ $config['label'] }}">
                                            <i class="{{ $config['icon'] }}"></i> {{ $count }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100">

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


@else
    <div class="text-center py-5">
        <div class="empty-state">
            <i class="fas fa-graduation-cap fa-5x text-muted mb-3"></i>
            <h3 class="text-muted">No courses found</h3>
            <p class="text-muted mb-4">
                @if(request('search') || request('category'))
                    No courses match your search criteria. Try adjusting your filters.
                @else
                    Create your first course to get started with your learning management system!
                @endif
            </p>

            @if(request('search') || request('category'))
                <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            @endif

            <a href="{{ route('courses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Your  Course
            </a>
        </div>
    </div>
@endif


@endsection


