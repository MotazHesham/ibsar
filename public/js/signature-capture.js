/**
 * Signature Capture Component
 * Handles canvas-based signature capture with modal popup
 */
class SignatureCapture {
    constructor(options = {}) {
        this.canvasId = options.canvasId || 'signatureCanvas';
        this.previewId = options.previewId || 'signaturePreview';
        this.dataInputId = options.dataInputId || 'signatureData';
        this.modalId = options.modalId || 'signatureModal';
        this.openButtonId = options.openButtonId || 'openSignatureModal';
        
        this.canvas = null;
        this.ctx = null;
        this.isDrawing = false;
        this.lastX = 0;
        this.lastY = 0;
        this.modal = null;
        
        this.init();
    }
    
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupElements();
            this.setupEventListeners();
            this.loadExistingSignature();
        });
    }
    
    setupElements() {
        this.canvas = document.getElementById(this.canvasId);
        console.log('Canvas element:', this.canvas);
        if (!this.canvas) {
            console.log('Canvas not found, returning');
            return;
        }
        
        this.ctx = this.canvas.getContext('2d');
        this.modal = new bootstrap.Modal(document.getElementById(this.modalId));
        
        console.log('Canvas context:', this.ctx);
        console.log('Modal:', this.modal);
        
        // Set up canvas
        this.ctx.strokeStyle = '#000';
        this.ctx.lineWidth = 2;
        this.ctx.lineCap = 'round';
        this.ctx.lineJoin = 'round';
        
        console.log('Canvas setup complete');
    }
    
    setupEventListeners() {
        if (!this.canvas) return;
        
        // Canvas drawing events
        this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
        this.canvas.addEventListener('mousemove', (e) => this.draw(e));
        this.canvas.addEventListener('mouseup', () => this.stopDrawing());
        this.canvas.addEventListener('mouseout', () => this.stopDrawing());
        
        // Touch events for mobile
        this.canvas.addEventListener('touchstart', (e) => this.handleTouch(e));
        this.canvas.addEventListener('touchmove', (e) => this.handleTouch(e));
        this.canvas.addEventListener('touchend', () => this.stopDrawing());
        
        // Button events
        const openBtn = document.getElementById(this.openButtonId);
        const clearBtn = document.getElementById('clearSignature');
        const saveBtn = document.getElementById('saveSignature');
        
        console.log('Open button element:', openBtn);
        console.log('Clear button element:', clearBtn);
        console.log('Save button element:', saveBtn);
        
        if (openBtn) {
            console.log('Adding click listener to open button');
            openBtn.addEventListener('click', () => {
                console.log('Open button clicked');
                this.openModal();
            });
        } else {
            console.log('Open button not found');
        }
        
        if (clearBtn) {
            clearBtn.addEventListener('click', () => this.clearSignature());
        }
        
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveSignature());
        }
        
        // Modal events
        const modalElement = document.getElementById(this.modalId);
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', () => this.resetCanvas());
        }
    }
    
    startDrawing(e) {
        this.isDrawing = true;
        const rect = this.canvas.getBoundingClientRect();
        this.lastX = e.clientX - rect.left;
        this.lastY = e.clientY - rect.top;
    }
    
    draw(e) {
        if (!this.isDrawing) return;
        
        const rect = this.canvas.getBoundingClientRect();
        const currentX = e.clientX - rect.left;
        const currentY = e.clientY - rect.top;
        
        this.ctx.beginPath();
        this.ctx.moveTo(this.lastX, this.lastY);
        this.ctx.lineTo(currentX, currentY);
        this.ctx.stroke();
        
        this.lastX = currentX;
        this.lastY = currentY;
    }
    
    stopDrawing() {
        this.isDrawing = false;
    }
    
    handleTouch(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent(
            e.type === 'touchstart' ? 'mousedown' : 
            e.type === 'touchmove' ? 'mousemove' : 'mouseup', 
            {
                clientX: touch.clientX,
                clientY: touch.clientY
            }
        );
        this.canvas.dispatchEvent(mouseEvent);
    }
    
    openModal() {
        console.log('openModal called');
        console.log('Modal object:', this.modal);
        if (this.modal) {
            console.log('Showing modal');
            this.modal.show();
        } else {
            console.log('Modal not found');
        }
    }
    
    clearSignature() {
        if (this.ctx) {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }
    }
    
    saveSignature() {
        if (!this.canvas) return;
        
        const dataURL = this.canvas.toDataURL('image/png');
        const signatureData = document.getElementById(this.dataInputId);
        
        if (signatureData) {
            signatureData.value = dataURL;
        }
        
        // Update preview
        this.updateSignaturePreview(dataURL);
        
        // Show success feedback
        this.showSuccessFeedback();
        
        // Close modal after delay
        setTimeout(() => {
            if (this.modal) {
                this.modal.hide();
            }
        }, 1500);
    }
    
    updateSignaturePreview(dataURL) {
        const preview = document.getElementById(this.previewId);
        if (!preview) return;
        
        if (dataURL) {
            preview.innerHTML = `
                <img src="${dataURL}" alt="Signature" class="signature-image">
                <div class="signature-overlay">
                    <button type="button" id="editSignature" class="btn btn-sm btn-outline-primary">
                        <i class="ri-edit-line me-1"></i>{{ trans('global.edit') }}
                    </button>
                </div>
            `;
            
            // Add edit functionality
            const editBtn = document.getElementById('editSignature');
            if (editBtn) {
                editBtn.addEventListener('click', () => this.openModal());
            }
        } else {
            preview.innerHTML = `
                <div class="signature-placeholder">
                    <i class="ri-edit-line fs-24 text-muted"></i>
                    <p class="text-muted mb-0">{{ trans('global.click_to_sign') }}</p>
                </div>
            `;
        }
    }
    
    showSuccessFeedback() {
        const saveBtn = document.getElementById('saveSignature');
        if (!saveBtn) return;
        
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="ri-check-line me-1"></i>{{ trans("global.saved") }}';
        saveBtn.classList.remove('btn-primary');
        saveBtn.classList.add('btn-success');
        
        setTimeout(() => {
            saveBtn.innerHTML = originalText;
            saveBtn.classList.remove('btn-success');
            saveBtn.classList.add('btn-primary');
        }, 2000);
    }
    
    loadExistingSignature() {
        const signatureData = document.getElementById(this.dataInputId);
        if (signatureData && signatureData.value) {
            this.updateSignaturePreview(signatureData.value);
        }
    }
    
    resetCanvas() {
        if (this.ctx) {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }
    }
}

// Auto-initialize if elements exist
function initializeSignatureCapture() {
    console.log('Attempting to initialize signature capture...');
    const canvas = document.getElementById('signatureCanvas');
    const preview = document.getElementById('signaturePreview');
    const openButton = document.getElementById('openSignatureModal');
    
    console.log('Canvas found:', !!canvas);
    console.log('Preview found:', !!preview);
    console.log('Open button found:', !!openButton);
    
    if (canvas && preview && openButton) {
        console.log('All elements found, initializing signature capture');
        new SignatureCapture();
    } else {
        console.log('Some elements missing, retrying in 100ms...');
        setTimeout(initializeSignatureCapture, 100);
    }
}

// Simple direct approach - Button to Modal
function setupSignatureCapture() {
    console.log('Setting up signature capture...');
    
    const openButton = document.getElementById('openSignatureModal');
    const modal = document.getElementById('signatureModal');
    const canvas = document.getElementById('signatureCanvas');
    const clearBtn = document.getElementById('clearSignature');
    const saveBtn = document.getElementById('saveSignature');
    const signatureData = document.getElementById('signatureData');
    
    const modalTriggerButton = document.querySelector('button[data-bs-toggle="modal"][data-bs-target="#signatureModal"]');
    
    console.log('Elements found:');
    console.log('- Open button:', !!openButton);
    console.log('- Modal trigger button:', !!modalTriggerButton);
    console.log('- Modal:', !!modal);
    console.log('- Canvas:', !!canvas);
    console.log('- Clear button:', !!clearBtn);
    console.log('- Save button:', !!saveBtn);
    console.log('- Signature data input:', !!signatureData);
    
    // Setup button to open modal - look for button with data-bs-toggle="modal"
    if (modalTriggerButton && modal) {
        console.log('Found modal trigger button, adding click listener');
        modalTriggerButton.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Modal trigger button clicked - opening modal');
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        });
    } else if (openButton && modal) {
        console.log('Adding click listener to open button');
        openButton.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Open button clicked - opening modal');
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        });
    } else {
        console.log('Missing modal trigger button or modal');
    }
    
    // Setup canvas functionality
    if (canvas && clearBtn && saveBtn && signatureData) {
        console.log('Setting up canvas functionality');
        
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;
        
        // Set up canvas
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        
        // Drawing events
        canvas.addEventListener('mousedown', function(e) {
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            lastX = e.clientX - rect.left;
            lastY = e.clientY - rect.top;
            console.log('Drawing started at:', lastX, lastY);
        });
        
        canvas.addEventListener('mousemove', function(e) {
            if (!isDrawing) return;
            const rect = canvas.getBoundingClientRect();
            const currentX = e.clientX - rect.left;
            const currentY = e.clientY - rect.top;
            
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(currentX, currentY);
            ctx.stroke();
            
            lastX = currentX;
            lastY = currentY;
        });
        
        canvas.addEventListener('mouseup', function() {
            isDrawing = false;
            console.log('Drawing stopped');
        });
        
        canvas.addEventListener('mouseout', function() {
            isDrawing = false;
        });
        
        // Touch events for mobile
        canvas.addEventListener('touchstart', function(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousedown', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        });
        
        canvas.addEventListener('touchmove', function(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousemove', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        });
        
        canvas.addEventListener('touchend', function(e) {
            e.preventDefault();
            const mouseEvent = new MouseEvent('mouseup', {});
            canvas.dispatchEvent(mouseEvent);
        });
        
        // Clear button
        clearBtn.addEventListener('click', function() {
            console.log('Clear button clicked');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });
        
        // Save button
        saveBtn.addEventListener('click', function() {
            console.log('Save button clicked');
            const dataURL = canvas.toDataURL('image/png');
            signatureData.value = dataURL;
            
            console.log('Signature saved to hidden input:', dataURL.substring(0, 50) + '...');
            
            // Show success feedback
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="ri-check-line me-1"></i>Saved';
            saveBtn.classList.remove('btn-primary');
            saveBtn.classList.add('btn-success');
            
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.classList.remove('btn-success');
                saveBtn.classList.add('btn-primary');
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.hide();
                console.log('Modal closed');
            }, 1000);
        });
        
        console.log('Canvas functionality setup complete');
    } else {
        console.log('Missing canvas elements');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Signature capture script loaded');
    setupSignatureCapture();
});
