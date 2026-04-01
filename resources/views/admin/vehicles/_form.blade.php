@php
    use App\Enums\BusType;
    use App\Enums\VehicleStatus;
    use App\Enums\PmsUnit;
    $isEdit = isset($vehicle);
    $v = $isEdit ? (is_array($vehicle) ? (object) $vehicle : $vehicle) : null;
@endphp

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="bus_number" class="text-sm font-medium leading-none">Bus Number</label>
        <input id="bus_number" name="bus_number" type="text" value="{{ old('bus_number', $v->bus_number ?? '') }}" placeholder="e.g. 2801" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('bus_number')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="brand" class="text-sm font-medium leading-none">Brand</label>
        <input id="brand" name="brand" type="text" value="{{ old('brand', $v->brand ?? '') }}" placeholder="e.g. DLTB" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('brand')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="bus_type" class="text-sm font-medium leading-none">Bus Type</label>
        <select id="bus_type" name="bus_type" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
            <option value="">-- Select Bus Type --</option>
            @foreach (BusType::cases() as $type)
                <option value="{{ $type->value }}" {{ old('bus_type', $v->bus_type ?? '') === $type->value ? 'selected' : '' }}>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        @error('bus_type')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="plate_number" class="text-sm font-medium leading-none">Plate Number</label>
        <input id="plate_number" name="plate_number" type="text" value="{{ old('plate_number', $v->plate_number ?? '') }}" placeholder="e.g. ABC 1234" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('plate_number')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="status" class="text-sm font-medium leading-none">Status</label>
        <select id="status" name="status" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
            @foreach (VehicleStatus::cases() as $status)
                <option value="{{ $status->value }}" {{ old('status', $v->status ?? 'OK') === $status->value ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        @error('status')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="current_location" class="text-sm font-medium leading-none">Current Location</label>
        <input id="current_location" name="current_location" type="text" value="{{ old('current_location', $v->current_location ?? '') }}" placeholder="e.g. Tabaco Terminal" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('current_location')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="total_kilometers" class="text-sm font-medium leading-none">Total Kilometers (Odometer)</label>
        <input id="total_kilometers" name="total_kilometers" type="number" value="{{ old('total_kilometers', $v->total_kilometers ?? 0) }}" min="0" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('total_kilometers')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="border-t pt-4">
    <h3 class="mb-3 font-semibold">Preventive Maintenance Schedule (PMS)</h3>
    <div class="grid grid-cols-3 gap-4">
        <div class="space-y-2">
            <label for="pms_unit" class="text-sm font-medium leading-none">PMS Unit</label>
            <select id="pms_unit" name="pms_unit" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                @foreach (PmsUnit::cases() as $unit)
                    <option value="{{ $unit->value }}" {{ old('pms_unit', $v->pms_unit ?? 'kilometers') === $unit->value ? 'selected' : '' }}>
                        {{ $unit->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="space-y-2">
            <label for="pms_threshold" class="text-sm font-medium leading-none">PMS Threshold</label>
            <div class="flex items-center gap-2">
                <input id="pms_threshold" name="pms_threshold" type="text" value="{{ old('pms_threshold', $v->pms_threshold ?? 10000) }}" data-type="number" data-max="50000"
                    oninput="formatNumberInput(this)"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
            </div>
            <input id="pms_threshold_slider" type="range" min="0" max="50000" step="100" value="{{ old('pms_threshold', $v->pms_threshold ?? 10000) }}" 
                oninput="updateNumberInput('pms_threshold', this.value)"
                class="w-full accent-primary" />
            <p class="text-xs text-muted-foreground">Max: 50,000</p>
            @error('pms_threshold')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="space-y-2">
            <label for="current_pms_value" class="text-sm font-medium leading-none">Current Value</label>
            <div class="flex items-center gap-2">
                <input id="current_pms_value" name="current_pms_value" type="text" value="{{ old('current_pms_value', $v->current_pms_value ?? 0) }}" data-type="number" data-max="100000"
                    oninput="formatNumberInput(this)"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
            </div>
            <input id="current_pms_value_slider" type="range" min="0" max="100000" step="100" value="{{ old('current_pms_value', $v->current_pms_value ?? 0) }}" 
                oninput="updateNumberInput('current_pms_value', this.value)"
                class="w-full accent-primary" />
            <p class="text-xs text-muted-foreground">Max: 100,000</p>
            <div id="current_pms_value_warning"></div>
            @error('current_pms_value')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="mt-4 grid grid-cols-2 gap-4">
        <div class="space-y-2">
            <label for="last_pms_date" class="text-sm font-medium leading-none">Last PMS Date</label>
            <div class="relative">
                <input id="last_pms_date" name="last_pms_date" type="date" value="{{ old('last_pms_date', $v->last_pms_date ?? '') }}"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
        </div>
        <div class="space-y-2">
            <label for="pms_interval_months" class="text-sm font-medium leading-none">PMS Interval (Months)</label>
            <input id="pms_interval_months" name="pms_interval_months" type="number" value="{{ old('pms_interval_months', $v->pms_interval_months ?? '') }}" min="1" max="24" placeholder="e.g. 3" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
            <p class="text-xs text-muted-foreground">How often PMS should be performed (in months)</p>
            @error('pms_interval_months')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
    @if ($isEdit && $v->next_pms_date)
        <div class="mt-3 rounded-md bg-blue-50 p-3 text-sm text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
            Next scheduled PMS: <strong>{{ \Carbon\Carbon::parse($v->next_pms_date)->format('M d, Y') }}</strong>
        </div>
    @endif
</div>

<script>
function formatNumberInput(input) {
    // Remove all non-numeric characters
    let value = input.value.replace(/[^0-9]/g, '');
    
    // Don't allow empty value
    if (value === '') {
        input.value = '';
        input.setAttribute('data-raw-value', '0');
        updateSlider(input.id, 0);
        validatePmsValues();
        return;
    }
    
    // Parse as integer
    let numValue = parseInt(value, 10);
    const maxValue = parseInt(input.getAttribute('data-max'), 10) || 100000;
    
    // Cap at max value
    if (numValue > maxValue) {
        numValue = maxValue;
    }
    
    // Store raw value
    input.setAttribute('data-raw-value', numValue.toString());
    
    // Format with commas
    input.value = numValue.toLocaleString('en-US');
    
    // Update corresponding slider
    updateSlider(input.id, numValue);
    
    // Validate PMS values
    validatePmsValues();
}

function updateNumberInput(inputId, value) {
    const input = document.getElementById(inputId);
    const numValue = parseInt(value, 10) || 0;
    
    // Store raw value
    input.setAttribute('data-raw-value', numValue.toString());
    
    // Format with commas
    input.value = numValue.toLocaleString('en-US');
    
    // Validate PMS values
    validatePmsValues();
}

function updateSlider(inputId, value) {
    const sliderId = inputId + '_slider';
    const slider = document.getElementById(sliderId);
    if (slider) {
        slider.value = value;
    }
}

function getRawValue(input) {
    return parseInt(input.getAttribute('data-raw-value'), 10) || 0;
}

function validatePmsValues() {
    const thresholdInput = document.getElementById('pms_threshold');
    const currentInput = document.getElementById('current_pms_value');
    const warningContainer = document.getElementById('current_pms_value_warning');
    
    if (!thresholdInput || !currentInput || !warningContainer) return;
    
    const threshold = getRawValue(thresholdInput);
    const current = getRawValue(currentInput);
    
    // Clear existing warning
    warningContainer.innerHTML = '';
    
    // Check if current value exceeds threshold - show WARNING (not error)
    if (threshold > 0 && current > 0 && current > threshold) {
        warningContainer.innerHTML = '<p class="text-xs text-orange-500 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Current PMS value exceeds threshold - requires investigation</p>';
    }
}

// Initialize all number inputs on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[data-type="number"]').forEach(function(input) {
        // Store initial raw value
        const rawValue = input.value.replace(/,/g, '');
        input.setAttribute('data-raw-value', rawValue || '0');
        
        // Format initial value with commas
        if (rawValue) {
            input.value = parseInt(rawValue, 10).toLocaleString('en-US');
        }
    });
    
    // Validate on initial load
    validatePmsValues();
});

// Form submission - convert formatted values back to raw numbers
document.querySelector('form[method="POST"][action*="vehicles"]')?.addEventListener('submit', function(e) {
    document.querySelectorAll('input[data-type="number"]').forEach(function(input) {
        const rawValue = getRawValue(input);
        // Create hidden input with raw value
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = input.name;
        hiddenInput.value = rawValue;
        this.appendChild(hiddenInput);
        // Disable the formatted input so it doesn't get submitted
        input.disabled = true;
    }, this);
});
</script>
