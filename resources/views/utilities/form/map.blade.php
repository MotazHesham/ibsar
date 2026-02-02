<input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', isset($latitude) ? $latitude : '') }}">
<input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', isset($longitude) ? $longitude : '') }}">
<div class="form-group mb-3 {{ $grid ?? '' }}">
    <label class="form-label" for="{{ $name }}">
        {{ trans($label) }}
        @if ($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-secondary-light" data-bs-toggle="modal" data-bs-target="#locationModal">
            <i class="fas fa-map-marker-alt me-2"></i>{{ trans('global.select_location') }}
        </button>
        <span id="locationDisplay" class="text-muted">
            @if((isset($latitude) && $latitude != '') || (isset($longitude) && $longitude != ''))
                {{ trans('global.location_selected') }}
                ({{ $latitude ?? '' }}, {{ $longitude ?? '' }})
            @else
                {{ trans('global.no_location_selected') }}
            @endif
        </span>
    </div>
</div>

<!-- Location Selection Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">{{ trans('global.select_location') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('global.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="position-relative">
                            <input type="text" id="searchLocation" class="form-control" placeholder="{{ trans('global.search_for_location') }}" autocomplete="off">
                            <div id="searchDropdown" class="dropdown-menu w-100" style="display: none; max-height: 200px; overflow-y: auto; z-index: 9999; position: absolute; top: 100%; left: 0;"></div>
                            <div id="searchLoading" class="text-center py-2" style="display: none; z-index: 9999; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #dee2e6; border-top: none;">
                                <small class="text-muted">
                                    <i class="fas fa-spinner fa-spin me-1"></i>{{ trans('global.searching') }}...
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="map" style="height: 400px; width: 100%;"></div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('global.latitude') }}:</label>
                            <input type="text" id="modalLatitude" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('global.longitude') }}:</label>
                            <input type="text" id="modalLongitude" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('global.cancel') }}</button>
                <button type="button" id="confirmLocation" class="btn btn-primary">{{ trans('global.confirm_location') }}</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    let map;
    let marker;
    let selectedLat = null;
    let selectedLng = null;

    // Initialize map when modal is shown
    document.getElementById('locationModal').addEventListener('shown.bs.modal', function() {
        if (!map) {
            // Initialize map centered on Saudi Arabia
            map = L.map('map').setView([24.7136, 46.6753], 6); // Default to Riyadh, Saudi Arabia

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add click event to map
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                
                // Remove existing marker
                if (marker) {
                    map.removeLayer(marker);
                }
                
                // Add new marker
                marker = L.marker([lat, lng]).addTo(map);
                
                // Update coordinates
                selectedLat = lat;
                selectedLng = lng;
                document.getElementById('modalLatitude').value = lat.toFixed(6);
                document.getElementById('modalLongitude').value = lng.toFixed(6);
            });

            // Load existing coordinates if available
            const existingLat = document.getElementById('latitude').value;
            const existingLng = document.getElementById('longitude').value;
            
            if (existingLat && existingLng) {
                const lat = parseFloat(existingLat);
                const lng = parseFloat(existingLng);
                map.setView([lat, lng], 15);
                marker = L.marker([lat, lng]).addTo(map);
                selectedLat = lat;
                selectedLng = lng;
                document.getElementById('modalLatitude').value = lat.toFixed(6);
                document.getElementById('modalLongitude').value = lng.toFixed(6);
            }
        }
        
        // Invalidate size to fix display issues
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    });

    // Search functionality with dropdown
    let searchTimeout;
    const searchInput = document.getElementById('searchLocation');
    const searchDropdown = document.getElementById('searchDropdown');
    const searchLoading = document.getElementById('searchLoading');

    function showLoading() {
        searchLoading.style.display = 'block';
        searchDropdown.style.display = 'none';
    }

    function hideLoading() {
        searchLoading.style.display = 'none';
    }

    function showDropdown() {
        searchDropdown.style.display = 'block';
        searchDropdown.classList.add('show');
    }

    function hideDropdown() {
        searchDropdown.style.display = 'none';
        searchDropdown.classList.remove('show');
    }

    function selectLocation(lat, lng, displayName) {
        map.setView([lat, lng], 15);
        
        // Remove existing marker
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Add new marker
        marker = L.marker([lat, lng]).addTo(map);
        
        // Update coordinates
        selectedLat = lat;
        selectedLng = lng;
        document.getElementById('modalLatitude').value = lat.toFixed(6);
        document.getElementById('modalLongitude').value = lng.toFixed(6);
        
        // Update search input with selected location
        searchInput.value = displayName;
        hideDropdown();
    }

    function performSearch(query) {
        if (query.trim().length < 3) {
            hideDropdown();
            hideLoading();
            return;
        }

        showLoading();

        // Using Nominatim for geocoding
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                hideLoading();
                
                if (data.length > 0) {
                    searchDropdown.innerHTML = '';
                    
                    data.forEach(result => {
                        const lat = parseFloat(result.lat);
                        const lng = parseFloat(result.lon);
                        const displayName = result.display_name;
                        
                        const item = document.createElement('a');
                        item.className = 'dropdown-item';
                        item.href = '#';
                        item.innerHTML = `
                            <div class="d-flex flex-column">
                                <span class="fw-bold">${result.name || 'Unknown'}</span>
                                <small class="text-muted">${displayName}</small>
                            </div>
                        `;
                        
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            selectLocation(lat, lng, displayName);
                        });
                        
                        searchDropdown.appendChild(item);
                    });
                    
                    showDropdown();
                } else {
                    searchDropdown.innerHTML = '<div class="dropdown-item-text text-muted">{{ trans('global.no_locations_found') }}</div>';
                    showDropdown();
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Search error:', error);
                searchDropdown.innerHTML = '<div class="dropdown-item-text text-danger">{{ trans('global.search_error_occurred') }}</div>';
                showDropdown();
            });
    }

    // Real-time search as user types
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value;
        
        if (query.trim().length === 0) {
            hideDropdown();
            hideLoading();
            return;
        }
        
        // Debounce search to avoid too many requests
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const items = searchDropdown.querySelectorAll('.dropdown-item');
        let activeItem = searchDropdown.querySelector('.dropdown-item.active');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!activeItem) {
                if (items.length > 0) {
                    items[0].classList.add('active');
                }
            } else {
                activeItem.classList.remove('active');
                const nextItem = activeItem.nextElementSibling;
                if (nextItem && nextItem.classList.contains('dropdown-item')) {
                    nextItem.classList.add('active');
                } else if (items.length > 0) {
                    items[0].classList.add('active');
                }
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (!activeItem) {
                if (items.length > 0) {
                    items[items.length - 1].classList.add('active');
                }
            } else {
                activeItem.classList.remove('active');
                const prevItem = activeItem.previousElementSibling;
                if (prevItem && prevItem.classList.contains('dropdown-item')) {
                    prevItem.classList.add('active');
                } else if (items.length > 0) {
                    items[items.length - 1].classList.add('active');
                }
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeItem) {
                activeItem.click();
            }
        } else if (e.key === 'Escape') {
            hideDropdown();
        }
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            hideDropdown();
        }
    });

    // Show dropdown on focus if there are results
    searchInput.addEventListener('focus', function() {
        if (searchDropdown.children.length > 0 && this.value.trim().length >= 3) {
            showDropdown();
        }
    });

    // Confirm location selection
    document.getElementById('confirmLocation').addEventListener('click', function() {
        if (selectedLat !== null && selectedLng !== null) {
            document.getElementById('latitude').value = selectedLat.toFixed(6);
            document.getElementById('longitude').value = selectedLng.toFixed(6);
            document.getElementById('locationDisplay').textContent = `{{ trans('global.location_selected') }} (${selectedLat.toFixed(4)}, ${selectedLng.toFixed(4)})`;
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('locationModal'));
            modal.hide();
        } else {
            alert('{{ trans('global.please_select_location_first') }}');
        }
    });
});
</script>