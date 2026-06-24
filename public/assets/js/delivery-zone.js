/*let map;
let marker;
let infoWindow;
let polygon = null; // Current drawn polygon
let originalPolygon = null; // Original polygon if exists
let drawingManager;
let center = {lat: 40.749933, lng: -73.98633}; // Default center: NYC
let otherZonePolygons = []; // Other delivery zones overlays

async function initMap() {
    // Load needed libraries (marker, places, drawing)
    const [{Map}, {AdvancedMarkerElement}, {DrawingManager}] = await Promise.all([
        google.maps.importLibrary("marker"),
        google.maps.importLibrary("places"),
        google.maps.importLibrary("drawing")
    ]);

    // Center from hidden input if available
    const centerLatInput = document.getElementById('center-latitude');
    const centerLngInput = document.getElementById('center-longitude');
    if (centerLatInput.value && centerLngInput.value) {
        center = {
            lat: parseFloat(centerLatInput.value),
            lng: parseFloat(centerLngInput.value)
        };
    }

    // Initialize map
    map = new google.maps.Map(document.getElementById('map'), {
        center,
        zoom: 13,
        mapId: '4504f8b37365c3d0',
        mapTypeControl: false,
    });

    // Place Autocomplete
    const placeAutocomplete = new google.maps.places.PlaceAutocompleteElement();
    placeAutocomplete.id = 'place-autocomplete-input';
    placeAutocomplete.locationBias = center;
    const card = document.getElementById('place-autocomplete-card');
    card.appendChild(placeAutocomplete);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(card);

    marker = new google.maps.marker.AdvancedMarkerElement({map});
    infoWindow = new google.maps.InfoWindow({});

    placeAutocomplete.addEventListener('gmp-select', async ({placePrediction}) => {
        const place = placePrediction.toPlace();
        await place.fetchFields({fields: ['displayName', 'formattedAddress', 'location']});
        if (place.viewport) {
            map.fitBounds(place.viewport);
        } else {
            map.setCenter(place.location);
            map.setZoom(17);
        }
        let content = `<div id="infowindow-content">
            <span id="place-displayname" class="title">${place.displayName}</span><br />
            <span id="place-address">${place.formattedAddress}</span>
        </div>`;
        updateInfoWindow(content, place.location);
        marker.position = place.location;
    });

    // Drawing Manager for Polygon
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: ['polygon']
        },
        polygonOptions: {
            fillColor: '#FF0000',
            fillOpacity: 0.2,
            strokeWeight: 2,
            clickable: true,
            editable: true,
            zIndex: 1
        }
    });
    drawingManager.setMap(map);

    // Only allow one polygon at a time
    google.maps.event.addListener(drawingManager, 'polygoncomplete', function (newPolygon) {
        if (polygon) {
            polygon.setMap(null);
        }
        polygon = newPolygon;
        updateBoundaryInput(polygon);
        setPolygonListeners(polygon);
        drawingManager.setDrawingMode(null); // Stop drawing after one polygon
    });

    // Restore existing polygon if available
    const boundaryJsonInput = document.getElementById('boundary-json');
    if (boundaryJsonInput.value) {
        try {
            const pathArr = JSON.parse(boundaryJsonInput.value);
            if (Array.isArray(pathArr) && pathArr.length > 0) {
                const path = pathArr.map(coord => new google.maps.LatLng(coord.lat, coord.lng));
                originalPolygon = new google.maps.Polygon({
                    paths: path,
                    fillColor: '#FF0000',
                    fillOpacity: 0.2,
                    strokeWeight: 2,
                    editable: true,
                    map: map,
                });
                map.fitBounds(getBoundsForPath(path));
                polygon = originalPolygon;
                updateBoundaryInput(polygon);
                setPolygonListeners(polygon);
            }
        } catch (e) {
            // Ignore parse error
        }
    }

    // Render other delivery zones in blue (non-interactive) so admin can see nearby zones
    try {
        await renderOtherDeliveryZonesOnForm();
    } catch (e) {
        console.warn('Unable to render other delivery zones on form:', e);
    }

    // Clear last polygon button
    document.getElementById('clear-last')?.addEventListener('click', function () {
        if (polygon) {
            polygon.setMap(null);
            polygon = null;
            document.getElementById('boundary-json').value = "";
        }
    });

    // Reset to original polygon button
    document.getElementById('reset-zone')?.addEventListener('click', function () {
        if (originalPolygon) {
            if (polygon) polygon.setMap(null);
            // Deep-clone path to allow editing
            const origPath = originalPolygon.getPath().getArray().map(latlng => ({
                lat: latlng.lat(),
                lng: latlng.lng()
            }));
            polygon = new google.maps.Polygon({
                paths: origPath,
                fillColor: '#FF0000',
                fillOpacity: 0.2,
                strokeWeight: 2,
                editable: true,
                map: map,
            });
            map.fitBounds(getBoundsForPath(origPath.map(coord => new google.maps.LatLng(coord.lat, coord.lng))));
            updateBoundaryInput(polygon);
            setPolygonListeners(polygon);
        }
    });
}

// Fetch active delivery zones and draw them as blue polygons, excluding the current zone (if any)
async function renderOtherDeliveryZonesOnForm() {
    // Clear existing overlays
    if (otherZonePolygons.length) {
        otherZonePolygons.forEach(p => p.setMap(null));
        otherZonePolygons = [];
    }

    const currentZoneIdEl = document.getElementById('current-zone-id');
    const currentZoneId = currentZoneIdEl ? parseInt(currentZoneIdEl.value) : null;

    const response = await fetch('/api/delivery-zone?per_page=500', {headers: {Accept: 'application/json'}});
    if (!response.ok) return; // fail silently on admin form
    const json = await response.json();

    // API wraps collection inside data.data
    const items = (json && json.data && Array.isArray(json.data.data)) ? json.data.data : (Array.isArray(json.data) ? json.data : []);
    if (!items.length) return;

    items.forEach(zone => {
        if (currentZoneId && zone.id === currentZoneId) return; // skip current
        if (!zone.boundary_json || !Array.isArray(zone.boundary_json) || zone.boundary_json.length < 3) return;
        const path = zone.boundary_json
            .map(pt => ({lat: parseFloat(pt.lat), lng: parseFloat(pt.lng)}))
            .filter(p => !Number.isNaN(p.lat) && !Number.isNaN(p.lng));
        if (path.length < 3) return;

        const overlay = new google.maps.Polygon({
            paths: path,
            strokeColor: '#0066ff',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#1a73e8',
            fillOpacity: 0.08,
            clickable: false, // do not intercept clicks; keep drawing/editing smooth
            zIndex: 0, // stay beneath the editable polygon
            map: map,
        });

        otherZonePolygons.push(overlay);
    });
}

// Helper: update hidden field with polygon coordinates
function updateBoundaryInput(polygon) {
    const path = polygon.getPath().getArray().map(latlng => ({
        lat: latlng.lat(),
        lng: latlng.lng()
    }));
    document.getElementById('boundary-json').value = JSON.stringify(path);

    // Calculate centroid (center)
    const center = getPolygonCentroid(path);
    if (center) {
        document.getElementById('center-latitude').value = center.lat;
        document.getElementById('center-longitude').value = center.lng;
    }

    // Calculate max radius from center to any vertex (in km)
    const radiusKm = getMaxRadiusKm(center, path);
    console.log(radiusKm)

    document.getElementById('radius-km').value = radiusKm.toFixed(3);
}

// Calculate centroid of polygon (simple average, works for most lat/lng polygons)
function getPolygonCentroid(path) {
    if (!path.length) return null;
    let lat = 0, lng = 0;
    path.forEach(point => {
        lat += point.lat;
        lng += point.lng;
    });
    return {lat: lat / path.length, lng: lng / path.length};
}

// Calculate max distance from center to any vertex (in kilometers)
function getMaxRadiusKm(center, path) {
    let maxDist = 0;
    path.forEach(point => {
        const dist = haversineDistance(center, point);
        if (dist > maxDist) maxDist = dist;
    });
    return maxDist;
}

// Haversine formula for distance between two lat/lng points (in kilometers)
function haversineDistance(coord1, coord2) {
    const R = 6371; // Earth's radius in km
    const dLat = toRad(coord2.lat - coord1.lat);
    const dLng = toRad(coord2.lng - coord1.lng);
    const lat1 = toRad(coord1.lat);
    const lat2 = toRad(coord2.lat);

    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.sin(dLng / 2) * Math.sin(dLng / 2) * Math.cos(lat1) * Math.cos(lat2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function toRad(deg) {
    return deg * Math.PI / 180;
}

// Helper: set listeners for polygon edit events to update hidden input
function setPolygonListeners(polygon) {
    google.maps.event.clearListeners(polygon.getPath(), 'set_at');
    google.maps.event.clearListeners(polygon.getPath(), 'insert_at');
    google.maps.event.clearListeners(polygon.getPath(), 'remove_at');
    polygon.getPath().addListener('set_at', () => updateBoundaryInput(polygon));
    polygon.getPath().addListener('insert_at', () => updateBoundaryInput(polygon));
    polygon.getPath().addListener('remove_at', () => updateBoundaryInput(polygon));
}

// Helper: compute bounds from a path
function getBoundsForPath(path) {
    const bounds = new google.maps.LatLngBounds();
    path.forEach(latlng => bounds.extend(latlng));
    return bounds;
}

// InfoWindow helper
function updateInfoWindow(content, position) {
    infoWindow.setContent(content);
    infoWindow.setPosition(position);
    infoWindow.open({map, anchor: marker, shouldFocus: false});
}

try {
    initMap();
} catch (e) {
    console.error("Error initializing map:", e);
}
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (event) {
        handleDelete(event, '.delete-delivery-zone', `/${panel}/delivery-zones/`, 'You are about to delete this Zone.');
    });
});*/

/*New*/
let map;
let marker;
let infoWindow;
let polygon = null;
let originalPolygon = null;
let center = {lat: 40.749933, lng: -73.98633};
let otherZonePolygons = [];
let drawingMode = false;
let drawingPath = [];
let tempDrawingPolygon = null;

async function initMap() {
    const [{Map}, {AdvancedMarkerElement}] = await Promise.all([
        google.maps.importLibrary("marker"),
        google.maps.importLibrary("places")
    ]);

    const centerLatInput = document.getElementById('center-latitude');
    const centerLngInput = document.getElementById('center-longitude');
    if (centerLatInput && centerLatInput.value && centerLngInput && centerLngInput.value) {
        center = {
            lat: parseFloat(centerLatInput.value),
            lng: parseFloat(centerLngInput.value)
        };
    }

    map = new google.maps.Map(document.getElementById('map'), {
        center,
        zoom: 13,
        mapId: '4504f8b37365c3d0',
        mapTypeControl: false,
    });

    // Place Autocomplete
    const placeAutocomplete = new google.maps.places.PlaceAutocompleteElement();
    placeAutocomplete.id = 'place-autocomplete-input';
    placeAutocomplete.locationBias = center;
    const card = document.getElementById('place-autocomplete-card');
    if (card) {
        card.appendChild(placeAutocomplete);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(card);
    }

    marker = new google.maps.marker.AdvancedMarkerElement({map});
    infoWindow = new google.maps.InfoWindow({});

    placeAutocomplete.addEventListener('gmp-select', async ({placePrediction}) => {
        const place = placePrediction.toPlace();
        await place.fetchFields({fields: ['displayName', 'formattedAddress', 'location']});
        if (place.viewport) {
            map.fitBounds(place.viewport);
        } else {
            map.setCenter(place.location);
            map.setZoom(17);
        }
        let content = `<div id="infowindow-content">
            <span id="place-displayname" class="title">${place.displayName}</span><br />
            <span id="place-address">${place.formattedAddress}</span>
        </div>`;
        updateInfoWindow(content, place.location);
        marker.position = place.location;
    });

    // Add drawing buttons
    addDrawingButtons();

    // Restore existing polygon if editing
    const boundaryJsonInput = document.getElementById('boundary-json');
    if (boundaryJsonInput && boundaryJsonInput.value) {
        try {
            const pathArr = JSON.parse(boundaryJsonInput.value);
            if (Array.isArray(pathArr) && pathArr.length > 0) {
                const path = pathArr.map(coord => new google.maps.LatLng(parseFloat(coord.lat), parseFloat(coord.lng)));
                originalPolygon = new google.maps.Polygon({
                    paths: path,
                    fillColor: '#FF0000',
                    fillOpacity: 0.2,
                    strokeWeight: 2,
                    editable: true,
                    draggable: true,
                    map: map,
                });
                map.fitBounds(getBoundsForPath(path));
                polygon = originalPolygon;
                updateFormFields(polygon);
                addPolygonListeners(polygon);
            }
        } catch (e) {
            console.warn('Error parsing existing polygon:', e);
        }
    }

    // Load other delivery zones
    try {
        await loadOtherZones();
    } catch (e) {
        console.warn('Unable to load other zones:', e);
    }

    // Clear button
    document.getElementById('clear-last')?.addEventListener('click', function() {
        if (polygon) {
            polygon.setMap(null);
            polygon = null;
            clearFormFields();
        }
    });

    // Reset button
    document.getElementById('reset-zone')?.addEventListener('click', function() {
        if (originalPolygon) {
            if (polygon) polygon.setMap(null);
            const origPath = originalPolygon.getPath().getArray();
            polygon = new google.maps.Polygon({
                paths: origPath,
                fillColor: '#FF0000',
                fillOpacity: 0.2,
                strokeWeight: 2,
                editable: true,
                draggable: true,
                map: map,
            });
            map.fitBounds(getBoundsForPath(origPath));
            updateFormFields(polygon);
            addPolygonListeners(polygon);
        }
    });
}

function addDrawingButtons() {
    const buttonContainer = document.createElement('div');
    buttonContainer.style.cssText = `
        margin: 10px;
        padding: 10px;
        background: white;
        border-radius: 4px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        z-index: 1000;
    `;
    
    const drawBtn = document.createElement('button');
    drawBtn.textContent = '✏️ Start Drawing Polygon';
    drawBtn.style.cssText = `
        padding: 10px 20px;
        background: #4285f4;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        margin-right: 10px;
    `;
    
    const cancelBtn = document.createElement('button');
    cancelBtn.textContent = '❌ Cancel Drawing';
    cancelBtn.style.cssText = `
        padding: 10px 20px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        display: none;
    `;
    
    drawBtn.onclick = () => startDrawing(drawBtn, cancelBtn);
    cancelBtn.onclick = () => cancelDrawing(drawBtn, cancelBtn);
    
    buttonContainer.appendChild(drawBtn);
    buttonContainer.appendChild(cancelBtn);
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(buttonContainer);
    
    window.drawingButtons = { drawBtn, cancelBtn };
}

function startDrawing(drawBtn, cancelBtn) {
    if (drawingMode) {
        cancelDrawing(drawBtn, cancelBtn);
        return;
    }
    
    // Remove existing polygon if any
    if (polygon) {
        if (confirm('Start new drawing? This will replace existing polygon.')) {
            polygon.setMap(null);
            polygon = null;
            clearFormFields();
        } else {
            return;
        }
    }
    
    drawingMode = true;
    drawingPath = [];
    
    drawBtn.textContent = '📝 Drawing...';
    drawBtn.style.background = '#34a853';
    cancelBtn.style.display = 'inline-block';
    map.setOptions({ draggableCursor: 'crosshair' });
    
    // Create temporary polygon for visual feedback
    tempDrawingPolygon = new google.maps.Polygon({
        paths: drawingPath,
        fillColor: '#FF0000',
        fillOpacity: 0.3,
        strokeWeight: 2,
        strokeColor: '#FF0000',
        map: map,
    });
    
    // Show instructions
    showInstructions('Left-click to add points | Right-click to finish polygon');
    
    // Add click listener
    const clickListener = map.addListener('click', (e) => {
        const point = e.latLng;
        drawingPath.push(point);
        tempDrawingPolygon.setPaths(drawingPath);
        
        // Draw line between last two points
        if (drawingPath.length > 1) {
            new google.maps.Polyline({
                path: [drawingPath[drawingPath.length - 2], drawingPath[drawingPath.length - 1]],
                strokeColor: '#FF0000',
                strokeWeight: 2,
                map: map,
                zIndex: 1000
            });
        }
    });
    
    // Add right-click listener to finish
    const rightClickListener = map.addListener('rightclick', () => {
        if (drawingPath.length >= 3) {
            // Create final polygon
            polygon = new google.maps.Polygon({
                paths: drawingPath,
                fillColor: '#FF0000',
                fillOpacity: 0.2,
                strokeWeight: 2,
                editable: true,
                draggable: true,
                map: map,
            });
            
            // Update form fields
            updateFormFields(polygon);
            addPolygonListeners(polygon);
            
            // Clean up drawing
            cleanupDrawing(drawBtn, cancelBtn);
            
            // Remove listeners
            google.maps.event.removeListener(clickListener);
            google.maps.event.removeListener(rightClickListener);
            
            alert('Polygon created successfully! You can now drag the vertices to edit.');
        } else {
            alert(`Need at least 3 points. Currently have ${drawingPath.length} points.`);
        }
    });
    
    // Store for cleanup
    window.drawingListeners = { clickListener, rightClickListener };
}

function cancelDrawing(drawBtn, cancelBtn) {
    if (window.drawingListeners) {
        if (window.drawingListeners.clickListener) 
            google.maps.event.removeListener(window.drawingListeners.clickListener);
        if (window.drawingListeners.rightClickListener) 
            google.maps.event.removeListener(window.drawingListeners.rightClickListener);
    }
    cleanupDrawing(drawBtn, cancelBtn);
}

function cleanupDrawing(drawBtn, cancelBtn) {
    drawingMode = false;
    drawingPath = [];
    
    if (tempDrawingPolygon) {
        tempDrawingPolygon.setMap(null);
        tempDrawingPolygon = null;
    }
    
    map.setOptions({ draggableCursor: '' });
    
    if (drawBtn) {
        drawBtn.textContent = '✏️ Start Drawing Polygon';
        drawBtn.style.background = '#4285f4';
    }
    if (cancelBtn) cancelBtn.style.display = 'none';
    
    hideInstructions();
    window.drawingListeners = null;
}

function showInstructions(text) {
    let instr = document.getElementById('drawing-instructions');
    if (!instr) {
        instr = document.createElement('div');
        instr.id = 'drawing-instructions';
        document.body.appendChild(instr);
    }
    instr.textContent = text;
    instr.style.cssText = `
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-family: Arial;
        font-size: 14px;
        z-index: 10000;
        pointer-events: none;
    `;
}

function hideInstructions() {
    const instr = document.getElementById('drawing-instructions');
    if (instr) instr.remove();
}

function updateFormFields(polygonObj) {
    if (!polygonObj) return;
    
    try {
        // Get polygon path
        const path = polygonObj.getPath().getArray().map(latlng => ({
            lat: latlng.lat(),
            lng: latlng.lng()
        }));
        
        // Update boundary JSON
        const boundaryInput = document.getElementById('boundary-json');
        if (boundaryInput) {
            boundaryInput.value = JSON.stringify(path);
            console.log('✅ Boundary JSON set:', boundaryInput.value.substring(0, 100));
        }
        
        // Calculate and update center
        const centroid = calculateCentroid(path);
        if (centroid) {
            const latInput = document.getElementById('center-latitude');
            const lngInput = document.getElementById('center-longitude');
            if (latInput) {
                latInput.value = centroid.lat.toFixed(6);
                console.log('✅ Center latitude set:', centroid.lat);
            }
            if (lngInput) {
                lngInput.value = centroid.lng.toFixed(6);
                console.log('✅ Center longitude set:', centroid.lng);
            }
        }
        
        // Calculate and update radius
        const radius = calculateMaxRadius(centroid, path);
        const radiusInput = document.getElementById('radius-km');
        if (radiusInput) {
            radiusInput.value = radius.toFixed(3);
            console.log('✅ Radius set:', radius);
        }
        
        // Verify all fields are filled
        verifyFields();
        
    } catch (error) {
        console.error('Error updating form fields:', error);
    }
}

function calculateCentroid(path) {
    if (!path || path.length === 0) return null;
    let sumLat = 0, sumLng = 0;
    path.forEach(point => {
        sumLat += point.lat;
        sumLng += point.lng;
    });
    return {
        lat: sumLat / path.length,
        lng: sumLng / path.length
    };
}

function calculateMaxRadius(center, path) {
    if (!center || !path || path.length === 0) return 0;
    let maxDist = 0;
    path.forEach(point => {
        const dist = haversineDistance(center, point);
        if (dist > maxDist) maxDist = dist;
    });
    return maxDist;
}

function haversineDistance(p1, p2) {
    const R = 6371; // Earth's radius in km
    const dLat = (p2.lat - p1.lat) * Math.PI / 180;
    const dLng = (p2.lng - p1.lng) * Math.PI / 180;
    const lat1 = p1.lat * Math.PI / 180;
    const lat2 = p2.lat * Math.PI / 180;
    
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.sin(dLng/2) * Math.sin(dLng/2) * Math.cos(lat1) * Math.cos(lat2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function clearFormFields() {
    const fields = ['boundary-json', 'center-latitude', 'center-longitude', 'radius-km'];
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    console.log('Form fields cleared');
}

function verifyFields() {
    const boundary = document.getElementById('boundary-json')?.value;
    const lat = document.getElementById('center-latitude')?.value;
    const lng = document.getElementById('center-longitude')?.value;
    const radius = document.getElementById('radius-km')?.value;
    
    if (boundary && lat && lng && radius) {
        console.log('✅ All form fields are properly filled!');
        return true;
    } else {
        console.warn('⚠️ Some form fields are still empty:', { boundary: !!boundary, lat: !!lat, lng: !!lng, radius: !!radius });
        return false;
    }
}

function addPolygonListeners(polygonObj) {
    if (!polygonObj || !polygonObj.getPath) return;
    
    const path = polygonObj.getPath();
    google.maps.event.clearListeners(path, 'set_at');
    google.maps.event.clearListeners(path, 'insert_at');
    google.maps.event.clearListeners(path, 'remove_at');
    
    path.addListener('set_at', () => updateFormFields(polygonObj));
    path.addListener('insert_at', () => updateFormFields(polygonObj));
    path.addListener('remove_at', () => updateFormFields(polygonObj));
}

async function loadOtherZones() {
    if (otherZonePolygons.length) {
        otherZonePolygons.forEach(p => p.setMap(null));
        otherZonePolygons = [];
    }

    const currentZoneId = document.getElementById('current-zone-id')?.value;
    
    try {
        const response = await fetch('/api/delivery-zone?per_page=500', {
            headers: { Accept: 'application/json' }
        });
        
        if (!response.ok) return;
        
        const json = await response.json();
        const items = json?.data?.data || json?.data || [];
        
        items.forEach(zone => {
            if (currentZoneId && zone.id == currentZoneId) return;
            if (!zone.boundary_json || zone.boundary_json.length < 3) return;
            
            const path = zone.boundary_json.map(p => new google.maps.LatLng(p.lat, p.lng));
            const overlay = new google.maps.Polygon({
                paths: path,
                strokeColor: '#0066ff',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#1a73e8',
                fillOpacity: 0.08,
                clickable: false,
                map: map,
            });
            otherZonePolygons.push(overlay);
        });
    } catch (e) {
        console.warn('Error loading other zones:', e);
    }
}

function getBoundsForPath(path) {
    const bounds = new google.maps.LatLngBounds();
    path.forEach(latlng => bounds.extend(latlng));
    return bounds;
}

function updateInfoWindow(content, position) {
    infoWindow.setContent(content);
    infoWindow.setPosition(position);
    infoWindow.open({map, anchor: marker, shouldFocus: false});
}

// Add form submit handler to prevent empty submissions
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const boundary = document.getElementById('boundary-json')?.value;
            const lat = document.getElementById('center-latitude')?.value;
            const lng = document.getElementById('center-longitude')?.value;
            const radius = document.getElementById('radius-km')?.value;
            
            if (!boundary || !lat || !lng || !radius) {
                e.preventDefault();
                alert('Please draw a polygon on the map before submitting!');
                return false;
            }
        });
    }
});

// Initialize map
initMap().catch(console.error);