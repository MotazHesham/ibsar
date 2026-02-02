
<div class="row mb-3">
    @foreach ($beneficiary->beneficiaryFiles as $file)
        <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6">
            <div class="card custom-card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="main-card-icon primary">
                            <div class="avatar avatar-md bg-primary-transparent border border-primary border-opacity-10">
                                <div class="avatar avatar-sm text-primary">
                                    <i class="ti ti-file-description fs-24"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-fill">
                            <a href="{{ $file->file ? $file->file->getUrl() : '' }}" target="_blank" class="d-block fw-medium">{{ $file->name }}</a> 
                        </div>
                        <div class="text-end"> 
                            <span class="d-block fs-12 text-muted">{{ formatFileSize($file->file ? $file->file->size : 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach 
</div>