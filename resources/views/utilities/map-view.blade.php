

<button type="button" class="btn btn-secondary-light" data-bs-toggle="modal" data-bs-target="#showlocationModal">
    <i class="fas fa-map-marker-alt me-2"></i>{{ trans('global.view_location') }}
</button>

<!-- Location Selection Modal -->
<div class="modal fade" id="showlocationModal" tabindex="-1" aria-labelledby="showlocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"> 
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('global.close') }}"></button>
            </div>
            <div class="modal-body"> 
                <div id="map" style="height: 400px; width: 100%;"></div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('global.close') }}</button> 
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
    document.getElementById('showlocationModal').addEventListener('shown.bs.modal', function() {
        if (!map) {
            // Initialize map
            map = L.map('map').setView([40.7128, -74.0060], 10); // Default to New York

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map); 
            
            // Load existing coordinates if available
            var existingLat = '{{ $latitude }}';
            var existingLng = '{{ $longitude }}';
            
            if (existingLat && existingLng) {
                var lat = parseFloat(existingLat);
                var lng = parseFloat(existingLng);
                map.setView([lat, lng], 15);
                marker = L.marker([lat, lng]).addTo(map);
                selectedLat = lat;
                selectedLng = lng; 
            }
        }
        
        // Invalidate size to fix display issues
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    });  
});
</script>