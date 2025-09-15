@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-plus-circle"></i> Create New Course</h2>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="courseForm" action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Course Title *</label>
                                <input name="title" class="form-control" required value="{{ old('title') }}"
                                    placeholder="Enter course title">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Category</label>
                                <input name="category" class="form-control" value="{{ old('category') }}"
                                    placeholder="e.g., Programming, Design">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Course Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Describe what students will learn">{{ old('description') }}</textarea>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4><i class="fas fa-layer-group"></i> Course Modules</h4>
                            <button type="button" class="btn btn-success" id="addModuleBtn">
                                <i class="fas fa-plus"></i> Add Module
                            </button>
                        </div>

                        <div id="modulesContainer">
                        </div>

                        <div class="mt-4 text-center">
                            <button class="btn btn-primary btn-lg px-5" type="submit">
                                <i class="fas fa-save"></i> Save Course
                            </button>
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-lg ms-2 px-4">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <template id="moduleTemplate">
        <div class="module card mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="module-title mb-0"><i class="fas fa-layer-group"></i> Module</h5>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-module">
                        <i class="fas fa-trash"></i> Remove Module
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Module Title *</label>
                        <input class="form-control module-title-input" name="" required
                            placeholder="Enter module title">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Position</label>
                        <input type="number" class="form-control module-position-input" name="" placeholder="0"
                            min="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Module Description</label>
                    <textarea class="form-control module-description-input" name="" rows="2"
                        placeholder="Describe this module"></textarea>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6><i class="fas fa-file-alt"></i> Module Contents</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary addContentBtn">
                        <i class="fas fa-plus"></i> Add Content
                    </button>
                </div>

                <div class="contentsContainer">
                </div>
            </div>
        </div>
    </template>

    <template id="contentTemplate">
        <div class="content bg-light mb-3 rounded border p-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="content-label mb-0"><i class="fas fa-file"></i> Content Item</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-content">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Content Type</label>
                    <select class="form-control content-type" name="">
                        <option value="text">📝 Text</option>
                        <option value="image">🖼️ Image</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Position</label>
                    <input type="number" class="form-control content-position" name="" placeholder="0"
                        min="0">
                </div>
            </div>

            <div class="content-field content-field-text">
                <label class="form-label fw-bold">Text Content</label>
                <textarea class="form-control content-text" name="" rows="3"
                    placeholder="Enter your text content here"></textarea>
            </div>

            <div class="content-field content-field-image" style="display:none">
                <label class="form-label fw-bold">Select Image</label>
                <input type="file" class="form-control content-image" name="" accept="image/*">
                <small class="form-text text-muted">Max file size: 5MB. Supported: JPG, PNG, GIF</small>
            </div>
        </div>
    </template>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modulesContainer = document.getElementById('modulesContainer');
            const addModuleBtn = document.getElementById('addModuleBtn');
            const moduleTemplate = document.getElementById('moduleTemplate');
            const contentTemplate = document.getElementById('contentTemplate');

            let moduleCounter = 0;

            function updateNames() {
                const modules = modulesContainer.querySelectorAll('.module');
                modules.forEach((moduleEl, mIdx) => {
                    moduleEl.querySelector('.module-title-input').setAttribute('name',
                        `modules[${mIdx}][title]`);
                    moduleEl.querySelector('.module-description-input').setAttribute('name',
                        `modules[${mIdx}][description]`);
                    moduleEl.querySelector('.module-position-input').setAttribute('name',
                        `modules[${mIdx}][position]`);

                    const title = moduleEl.querySelector('.module-title-input').value || 'Module';
                    moduleEl.querySelector('.module-title').innerHTML =
                        `<i class="fas fa-layer-group"></i> ${title} (${mIdx + 1})`;

                    const contents = moduleEl.querySelectorAll('.content');
                    contents.forEach((contentEl, cIdx) => {
                        contentEl.querySelector('.content-type').setAttribute('name',
                            `modules[${mIdx}][contents][${cIdx}][type]`);
                        contentEl.querySelector('.content-text').setAttribute('name',
                            `modules[${mIdx}][contents][${cIdx}][text]`);
                        contentEl.querySelector('.content-position').setAttribute('name',
                            `modules[${mIdx}][contents][${cIdx}][position]`);
                        contentEl.querySelector('.content-image').setAttribute('name',
                            `modules[${mIdx}][contents][${cIdx}][image]`);

                        // Update content label
                        const type = contentEl.querySelector('.content-type').value;
                        const typeIcons = {
                            'text': '📝',
                            'image': '🖼️'
                        };
                        contentEl.querySelector('.content-label').innerHTML =
                            `<i class="fas fa-file"></i> ${typeIcons[type]} ${type.charAt(0).toUpperCase() + type.slice(1)} Content (${cIdx + 1})`;
                    });
                });
            }

            function addModule() {
                const clone = moduleTemplate.content.cloneNode(true);
                modulesContainer.appendChild(clone);
                moduleCounter++;
                updateNames();

                const newModule = modulesContainer.lastElementChild;
                newModule.querySelector('.module-title-input').focus();
            }

            function addContentToModule(moduleEl) {
                const clone = contentTemplate.content.cloneNode(true);
                moduleEl.querySelector('.contentsContainer').appendChild(clone);
                updateNames();
            }

            addModuleBtn.addEventListener('click', () => addModule());

            modulesContainer.addEventListener('click', (e) => {
                if (e.target.closest('.remove-module')) {
                    if (confirm('Are you sure you want to remove this module?')) {
                        e.target.closest('.module').remove();
                        updateNames();
                    }
                } else if (e.target.closest('.addContentBtn')) {
                    const moduleEl = e.target.closest('.module');
                    addContentToModule(moduleEl);
                } else if (e.target.closest('.remove-content')) {
                    if (confirm('Are you sure you want to remove this content?')) {
                        e.target.closest('.content').remove();
                        updateNames();
                    }
                }
            });

            modulesContainer.addEventListener('change', (e) => {
                if (e.target.matches('.content-type')) {
                    const contentEl = e.target.closest('.content');
                    const type = e.target.value;

                    contentEl.querySelectorAll('.content-field').forEach(f => f.style.display = 'none');

                    const fieldMap = {
                        'text': '.content-field-text',
                        'image': '.content-field-image'
                    };

                    if (fieldMap[type]) {
                        contentEl.querySelector(fieldMap[type]).style.display = 'block';
                    }

                    updateNames();
                } else if (e.target.matches('.module-title-input')) {
                    updateNames();
                }
            });

            modulesContainer.addEventListener('input', (e) => {
                if (e.target.matches('.module-title-input')) {
                    updateNames();
                }
            });

            document.getElementById('courseForm').addEventListener('submit', function(e) {
                const modules = modulesContainer.querySelectorAll('.module');

                if (modules.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one module to your course.');
                    return false;
                }

                let hasError = false;
                modules.forEach((moduleEl, idx) => {
                    const title = moduleEl.querySelector('.module-title-input').value.trim();
                    if (!title) {
                        hasError = true;
                        moduleEl.querySelector('.module-title-input').classList.add('is-invalid');
                    } else {
                        moduleEl.querySelector('.module-title-input').classList.remove(
                        'is-invalid');
                    }
                });

                if (hasError) {
                    e.preventDefault();
                    alert('Please provide a title for all modules.');
                    return false;
                }
            });

            addModule();

            modulesContainer.addEventListener('change', (e) => {
                if (e.target.matches('.content-image')) {
                    const file = e.target.files[0];
                    const contentEl = e.target.closest('.content');

                    const existingPreview = contentEl.querySelector('.image-preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }

                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const preview = document.createElement('div');
                            preview.className = 'image-preview mt-2';
                            preview.innerHTML = `
                        <img src="${event.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 150px;" alt="Preview">
                        <small class="d-block text-muted mt-1">Preview: ${file.name}</small>
                    `;
                            e.target.parentNode.appendChild(preview);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });

            modulesContainer.addEventListener('input', (e) => {
                if (e.target.matches('textarea')) {
                    e.target.style.height = 'auto';
                    e.target.style.height = e.target.scrollHeight + 'px';
                }
            });

            const alertElements = document.querySelectorAll('.alert-dismissible');
            alertElements.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.style.transition = 'opacity 0.5s';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            if (alert.parentNode) {
                                alert.remove();
                            }
                        }, 500);
                    }
                }, 5000);
            });
        });
    </script>
@endsection
