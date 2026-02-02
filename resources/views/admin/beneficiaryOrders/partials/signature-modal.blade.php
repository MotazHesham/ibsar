<!-- Signature Modal Component -->
<div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signatureModalLabel">
                    <i class="ri-edit-line me-2"></i>{{ trans('global.signature') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="signature-modal-container"> 
                    <div class="signature-canvas-container">
                        <canvas id="signatureCanvas" width="600" height="300" style="border: 2px dashed #ddd; border-radius: 8px; cursor: crosshair; width: 100%; max-width: 600px;"></canvas>
                    </div>
                    <div class="signature-controls mt-3 d-flex justify-content-between">
                        <div>
                            <button type="button" id="clearSignature" class="btn btn-outline-danger btn-sm">
                                <i class="ri-eraser-line me-1"></i>{{ trans('global.clear') }}
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-secondary btn-sm me-2" data-bs-dismiss="modal">
                                <i class="ri-close-line me-1"></i>{{ trans('global.cancel') }}
                            </button>
                            <button type="submit" name="finish" value="1" id="saveSignature" class="btn btn-primary btn-sm">
                                <i class="ri-save-line me-1"></i>{{ trans('global.save_signature') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
