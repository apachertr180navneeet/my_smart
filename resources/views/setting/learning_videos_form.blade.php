<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? 'Learning Videos' }}</h5>
                            <button type="button" class="btn btn-sm btn-primary" id="add-video-btn">
                                <i class="fa fa-plus"></i> Add Video
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('learning-videos-save'))->id('learning-videos-form')->open() }}
                        
                        <div id="video-list">
                            @if(!empty($videos) && count($videos) > 0)
                                @foreach($videos as $index => $video)
                                    <div class="card mb-3 video-item border p-3">
                                        <div class="row align-items-center">
                                            <div class="form-group col-md-5">
                                                <label class="form-control-label">Video Title <span class="text-danger">*</span></label>
                                                <input type="text" name="titles[]" class="form-control" value="{{ $video['title'] ?? '' }}" placeholder="e.g. How to use the app" required>
                                            </div>
                                            <div class="form-group col-md-5">
                                                <label class="form-control-label">Video URL <span class="text-danger">*</span></label>
                                                <input type="url" name="urls[]" class="form-control" value="{{ $video['url'] ?? '' }}" placeholder="https://..." required>
                                            </div>
                                            <div class="col-md-2 d-flex gap-2 mt-2">
                                                <button type="button" class="btn btn-sm btn-danger remove-video-btn" title="Delete">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="card mb-3 video-item border p-3">
                                    <div class="row align-items-center">
                                        <div class="form-group col-md-5">
                                            <label class="form-control-label">Video Title <span class="text-danger">*</span></label>
                                            <input type="text" name="titles[]" class="form-control" placeholder="e.g. How to use the app" required>
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label class="form-control-label">Video URL <span class="text-danger">*</span></label>
                                            <input type="url" name="urls[]" class="form-control" placeholder="https://..." required>
                                        </div>
                                        <div class="col-md-2 d-flex gap-2 mt-2">
                                            <button type="button" class="btn btn-sm btn-danger remove-video-btn" title="Delete">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(auth()->user()->hasRole(['admin', 'demo_admin']))
                            <div class="mt-3">
                                {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-end') }}
                            </div>
                        @endif
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
    <script>
        document.getElementById('add-video-btn').addEventListener('click', function() {
            var list = document.getElementById('video-list');
            var item = document.createElement('div');
            item.className = 'card mb-3 video-item border p-3';
            item.innerHTML = `
                <div class="row align-items-center">
                    <div class="form-group col-md-5">
                        <label class="form-control-label">Video Title <span class="text-danger">*</span></label>
                        <input type="text" name="titles[]" class="form-control" placeholder="e.g. How to use the app" required>
                    </div>
                    <div class="form-group col-md-5">
                        <label class="form-control-label">Video URL <span class="text-danger">*</span></label>
                        <input type="url" name="urls[]" class="form-control" placeholder="https://..." required>
                    </div>
                    <div class="col-md-2 d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-danger remove-video-btn" title="Delete">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            `;
            list.appendChild(item);
        });

        document.getElementById('video-list').addEventListener('click', function(e) {
            if (e.target.closest('.remove-video-btn')) {
                var item = e.target.closest('.video-item');
                if (document.querySelectorAll('.video-item').length > 1) {
                    item.remove();
                } else {
                    item.querySelectorAll('input').forEach(function(input) { input.value = ''; });
                }
            }
        });
    </script>
    @endsection
</x-master-layout>
