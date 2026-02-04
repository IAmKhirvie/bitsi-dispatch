<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { type BreadcrumbItem, type DispatchDay, type DispatchEntry, type TripCode, type Vehicle, type Driver } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Calendar } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Dispatch Board', href: '/dispatch' },
];

const props = defineProps<{
    dispatchDay: DispatchDay | null;
    date: string;
    tripCodes: TripCode[];
    vehicles: Vehicle[];
    drivers: Driver[];
}>();

const selectedDate = ref(props.date);
const showAddDialog = ref(false);
const showEditDialog = ref(false);
const editingEntry = ref<DispatchEntry | null>(null);

// Watch date changes and reload page
watch(selectedDate, (newDate) => {
    router.get('/dispatch', { date: newDate }, { preserveState: true, preserveScroll: true });
});

// Create dispatch day form
const createDayForm = useForm({
    service_date: props.date,
    notes: '',
});

function createDispatchDay() {
    createDayForm.service_date = selectedDate.value;
    createDayForm.post('/dispatch', {
        preserveScroll: true,
    });
}

// Add entry form
const addForm = useForm({
    trip_code_id: null as number | null,
    vehicle_id: null as number | null,
    driver_id: null as number | null,
    driver2_id: null as number | null,
    brand: '',
    bus_number: '',
    route: '',
    bus_type: '',
    departure_terminal: '',
    arrival_terminal: '',
    scheduled_departure: '',
    actual_departure: '',
    direction: '' as string,
    status: 'scheduled',
    remarks: '',
});

// Edit entry form
const editForm = useForm({
    trip_code_id: null as number | null,
    vehicle_id: null as number | null,
    driver_id: null as number | null,
    driver2_id: null as number | null,
    brand: '',
    bus_number: '',
    route: '',
    bus_type: '',
    departure_terminal: '',
    arrival_terminal: '',
    scheduled_departure: '',
    actual_departure: '',
    direction: '' as string,
    status: 'scheduled',
    remarks: '',
});

// Auto-fill from trip code selection
function onTripCodeChange(form: typeof addForm, tripCodeId: number | null) {
    if (!tripCodeId) return;
    const tripCode = props.tripCodes.find((tc) => tc.id === tripCodeId);
    if (tripCode) {
        form.route = `${tripCode.origin_terminal} - ${tripCode.destination_terminal}`;
        form.bus_type = tripCode.bus_type;
        form.departure_terminal = tripCode.origin_terminal;
        form.arrival_terminal = tripCode.destination_terminal;
        form.scheduled_departure = tripCode.scheduled_departure_time;
        form.direction = tripCode.direction;
    }
}

// Auto-fill from vehicle selection
function onVehicleChange(form: typeof addForm, vehicleId: number | null) {
    if (!vehicleId) return;
    const vehicle = props.vehicles.find((v) => v.id === vehicleId);
    if (vehicle) {
        form.brand = vehicle.brand;
        form.bus_number = vehicle.bus_number;
        if (!form.bus_type) {
            form.bus_type = vehicle.bus_type;
        }
    }
}

function submitAddEntry() {
    if (!props.dispatchDay) return;
    addForm.post(`/dispatch/${props.dispatchDay.id}/entries`, {
        preserveScroll: true,
        onSuccess: () => {
            showAddDialog.value = false;
            addForm.reset();
        },
    });
}

function openEditDialog(entry: DispatchEntry) {
    editingEntry.value = entry;
    editForm.trip_code_id = entry.trip_code_id;
    editForm.vehicle_id = entry.vehicle_id;
    editForm.driver_id = entry.driver_id;
    editForm.driver2_id = entry.driver2_id;
    editForm.brand = entry.brand || '';
    editForm.bus_number = entry.bus_number || '';
    editForm.route = entry.route || '';
    editForm.bus_type = entry.bus_type || '';
    editForm.departure_terminal = entry.departure_terminal || '';
    editForm.arrival_terminal = entry.arrival_terminal || '';
    editForm.scheduled_departure = entry.scheduled_departure || '';
    editForm.actual_departure = entry.actual_departure || '';
    editForm.direction = entry.direction || '';
    editForm.status = entry.status;
    editForm.remarks = entry.remarks || '';
    showEditDialog.value = true;
}

function submitEditEntry() {
    if (!editingEntry.value || !props.dispatchDay) return;
    editForm.put(`/dispatch/${props.dispatchDay.id}/entries/${editingEntry.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showEditDialog.value = false;
            editingEntry.value = null;
            editForm.reset();
        },
    });
}

function deleteEntry(entry: DispatchEntry) {
    if (!props.dispatchDay) return;
    if (confirm('Are you sure you want to delete this entry?')) {
        router.delete(`/dispatch/${props.dispatchDay.id}/entries/${entry.id}`, {
            preserveScroll: true,
        });
    }
}

const statusClasses: Record<string, string> = {
    scheduled: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    departed: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
    on_route: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
    delayed: 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
    cancelled: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
    arrived: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
};

const statusOptions = ['scheduled', 'departed', 'on_route', 'delayed', 'cancelled', 'arrived'];

function formatStatus(status: string): string {
    return status.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

function formatTime(time: string | null): string {
    if (!time) return '--';
    return time.substring(0, 5);
}

const entries = computed(() => props.dispatchDay?.entries || []);
const summary = computed(() => props.dispatchDay?.summary);
</script>

<template>
    <Head title="Dispatch Board" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header with date picker -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Dispatch Board</h1>
                    <p class="text-sm text-muted-foreground">Manage daily bus dispatch operations</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                        <Input
                            type="date"
                            v-model="selectedDate"
                            class="w-44"
                        />
                    </div>
                </div>
            </div>

            <!-- Summary bar -->
            <div v-if="summary" class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-6">
                <div class="rounded-lg border bg-card p-3 text-center">
                    <div class="text-lg font-bold">{{ summary.total_trips }}</div>
                    <div class="text-xs text-muted-foreground">Total</div>
                </div>
                <div class="rounded-lg border bg-card p-3 text-center">
                    <div class="text-lg font-bold text-blue-600">{{ summary.sb_trips }}</div>
                    <div class="text-xs text-muted-foreground">SB</div>
                </div>
                <div class="rounded-lg border bg-card p-3 text-center">
                    <div class="text-lg font-bold text-purple-600">{{ summary.nb_trips }}</div>
                    <div class="text-xs text-muted-foreground">NB</div>
                </div>
                <div class="rounded-lg border bg-card p-3 text-center">
                    <div class="text-lg font-bold">{{ summary.naga_trips }}</div>
                    <div class="text-xs text-muted-foreground">Naga</div>
                </div>
                <div class="rounded-lg border bg-card p-3 text-center">
                    <div class="text-lg font-bold">{{ summary.legazpi_trips }}</div>
                    <div class="text-xs text-muted-foreground">Legazpi</div>
                </div>
                <div class="rounded-lg border bg-card p-3 text-center">
                    <div class="text-lg font-bold">{{ summary.sorsogon_trips }}</div>
                    <div class="text-xs text-muted-foreground">Sorsogon</div>
                </div>
            </div>

            <!-- No dispatch day state -->
            <Card v-if="!dispatchDay">
                <CardContent class="flex flex-col items-center justify-center py-12">
                    <Calendar class="mb-4 h-12 w-12 text-muted-foreground" />
                    <h3 class="mb-2 text-lg font-semibold">No Dispatch Day</h3>
                    <p class="mb-4 text-sm text-muted-foreground">No dispatch day exists for {{ selectedDate }}. Create one to start dispatching.</p>
                    <Button @click="createDispatchDay" :disabled="createDayForm.processing">
                        <Plus class="mr-2 h-4 w-4" />
                        Create Dispatch Day
                    </Button>
                </CardContent>
            </Card>

            <!-- Dispatch Table -->
            <Card v-else>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-3">
                    <CardTitle class="text-base">
                        Entries for {{ dispatchDay.service_date }}
                        <span class="ml-2 text-sm font-normal text-muted-foreground">({{ entries.length }} entries)</span>
                    </CardTitle>
                    <Dialog v-model:open="showAddDialog">
                        <DialogTrigger as-child>
                            <Button size="sm">
                                <Plus class="mr-1 h-4 w-4" />
                                Add Entry
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="max-w-2xl">
                            <DialogHeader>
                                <DialogTitle>Add Dispatch Entry</DialogTitle>
                                <DialogDescription>Add a new bus dispatch entry for this day.</DialogDescription>
                            </DialogHeader>
                            <form @submit.prevent="submitAddEntry" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label>Trip Code</Label>
                                        <select v-model="addForm.trip_code_id" @change="onTripCodeChange(addForm, addForm.trip_code_id)" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                            <option :value="null">-- Select Trip Code --</option>
                                            <option v-for="tc in tripCodes" :key="tc.id" :value="tc.id">{{ tc.code }} ({{ tc.origin_terminal }} - {{ tc.destination_terminal }})</option>
                                        </select>
                                        <p v-if="addForm.errors.trip_code_id" class="text-xs text-red-500">{{ addForm.errors.trip_code_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Vehicle</Label>
                                        <select v-model="addForm.vehicle_id" @change="onVehicleChange(addForm, addForm.vehicle_id)" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                            <option :value="null">-- Select Vehicle --</option>
                                            <option v-for="v in vehicles" :key="v.id" :value="v.id">{{ v.brand }} {{ v.bus_number }} ({{ v.plate_number }})</option>
                                        </select>
                                        <p v-if="addForm.errors.vehicle_id" class="text-xs text-red-500">{{ addForm.errors.vehicle_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Driver 1</Label>
                                        <select v-model="addForm.driver_id" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                            <option :value="null">-- Select Driver --</option>
                                            <option v-for="d in drivers" :key="d.id" :value="d.id">{{ d.name }}</option>
                                        </select>
                                        <p v-if="addForm.errors.driver_id" class="text-xs text-red-500">{{ addForm.errors.driver_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Driver 2</Label>
                                        <select v-model="addForm.driver2_id" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                            <option :value="null">-- Select Driver --</option>
                                            <option v-for="d in drivers" :key="d.id" :value="d.id">{{ d.name }}</option>
                                        </select>
                                        <p v-if="addForm.errors.driver2_id" class="text-xs text-red-500">{{ addForm.errors.driver2_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Brand</Label>
                                        <Input v-model="addForm.brand" placeholder="e.g. DLTB" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Bus Number</Label>
                                        <Input v-model="addForm.bus_number" placeholder="e.g. 2801" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Route</Label>
                                        <Input v-model="addForm.route" placeholder="e.g. Cubao - Naga" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Bus Type</Label>
                                        <Input v-model="addForm.bus_type" placeholder="e.g. Airconditioned" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Departure Terminal</Label>
                                        <Input v-model="addForm.departure_terminal" placeholder="e.g. Cubao" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Arrival Terminal</Label>
                                        <Input v-model="addForm.arrival_terminal" placeholder="e.g. Naga" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Sched. Departure</Label>
                                        <Input type="time" v-model="addForm.scheduled_departure" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Actual Departure</Label>
                                        <Input type="time" v-model="addForm.actual_departure" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Direction</Label>
                                        <select v-model="addForm.direction" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                            <option value="">-- Select --</option>
                                            <option value="SB">SB (Southbound)</option>
                                            <option value="NB">NB (Northbound)</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Status</Label>
                                        <select v-model="addForm.status" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                            <option v-for="s in statusOptions" :key="s" :value="s">{{ formatStatus(s) }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2 space-y-2">
                                        <Label>Remarks</Label>
                                        <Input v-model="addForm.remarks" placeholder="Optional remarks" />
                                    </div>
                                </div>
                                <DialogFooter>
                                    <Button type="submit" :disabled="addForm.processing">
                                        Add Entry
                                    </Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">#</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Brand</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Bus No.</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Trip Code</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Route</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Bus Type</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Dep. Terminal</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Arr. Terminal</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Sched. Dep.</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Actual Dep.</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Dir.</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Driver 1</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Driver 2</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Status</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Remarks</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="entries.length === 0">
                                    <td colspan="16" class="px-3 py-8 text-center text-sm text-muted-foreground">
                                        No entries yet. Click "Add Entry" to start dispatching.
                                    </td>
                                </tr>
                                <tr v-for="(entry, index) in entries" :key="entry.id" class="border-b hover:bg-muted/30 transition-colors">
                                    <td class="whitespace-nowrap px-3 py-1.5 text-muted-foreground">{{ index + 1 }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5 font-medium">{{ entry.brand || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5 font-semibold">{{ entry.bus_number || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ entry.trip_code?.code || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ entry.route || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ entry.bus_type || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ entry.departure_terminal || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ entry.arrival_terminal || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ formatTime(entry.scheduled_departure) }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ formatTime(entry.actual_departure) }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">
                                        <span v-if="entry.direction" class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium"
                                            :class="entry.direction === 'SB' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300'">
                                            {{ entry.direction }}
                                        </span>
                                        <span v-else>--</span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ entry.driver?.name || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ entry.driver2?.name || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium"
                                            :class="statusClasses[entry.status] || statusClasses.scheduled">
                                            {{ formatStatus(entry.status) }}
                                        </span>
                                    </td>
                                    <td class="max-w-[120px] truncate px-3 py-1.5" :title="entry.remarks || ''">{{ entry.remarks || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">
                                        <div class="flex items-center gap-1">
                                            <button @click="openEditDialog(entry)" class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground" title="Edit">
                                                <Pencil class="h-3.5 w-3.5" />
                                            </button>
                                            <button @click="deleteEntry(entry)" class="rounded p-1 text-muted-foreground hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900 dark:hover:text-red-400" title="Delete">
                                                <Trash2 class="h-3.5 w-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Edit Dialog -->
            <Dialog v-model:open="showEditDialog">
                <DialogContent class="max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>Edit Dispatch Entry</DialogTitle>
                        <DialogDescription>Update the dispatch entry details.</DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="submitEditEntry" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label>Trip Code</Label>
                                <select v-model="editForm.trip_code_id" @change="onTripCodeChange(editForm, editForm.trip_code_id)" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option :value="null">-- Select Trip Code --</option>
                                    <option v-for="tc in tripCodes" :key="tc.id" :value="tc.id">{{ tc.code }} ({{ tc.origin_terminal }} - {{ tc.destination_terminal }})</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <Label>Vehicle</Label>
                                <select v-model="editForm.vehicle_id" @change="onVehicleChange(editForm, editForm.vehicle_id)" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option :value="null">-- Select Vehicle --</option>
                                    <option v-for="v in vehicles" :key="v.id" :value="v.id">{{ v.brand }} {{ v.bus_number }} ({{ v.plate_number }})</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <Label>Driver 1</Label>
                                <select v-model="editForm.driver_id" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option :value="null">-- Select Driver --</option>
                                    <option v-for="d in drivers" :key="d.id" :value="d.id">{{ d.name }}</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <Label>Driver 2</Label>
                                <select v-model="editForm.driver2_id" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option :value="null">-- Select Driver --</option>
                                    <option v-for="d in drivers" :key="d.id" :value="d.id">{{ d.name }}</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <Label>Brand</Label>
                                <Input v-model="editForm.brand" placeholder="e.g. DLTB" />
                            </div>
                            <div class="space-y-2">
                                <Label>Bus Number</Label>
                                <Input v-model="editForm.bus_number" placeholder="e.g. 2801" />
                            </div>
                            <div class="space-y-2">
                                <Label>Route</Label>
                                <Input v-model="editForm.route" placeholder="e.g. Cubao - Naga" />
                            </div>
                            <div class="space-y-2">
                                <Label>Bus Type</Label>
                                <Input v-model="editForm.bus_type" placeholder="e.g. Airconditioned" />
                            </div>
                            <div class="space-y-2">
                                <Label>Departure Terminal</Label>
                                <Input v-model="editForm.departure_terminal" placeholder="e.g. Cubao" />
                            </div>
                            <div class="space-y-2">
                                <Label>Arrival Terminal</Label>
                                <Input v-model="editForm.arrival_terminal" placeholder="e.g. Naga" />
                            </div>
                            <div class="space-y-2">
                                <Label>Sched. Departure</Label>
                                <Input type="time" v-model="editForm.scheduled_departure" />
                            </div>
                            <div class="space-y-2">
                                <Label>Actual Departure</Label>
                                <Input type="time" v-model="editForm.actual_departure" />
                            </div>
                            <div class="space-y-2">
                                <Label>Direction</Label>
                                <select v-model="editForm.direction" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option value="">-- Select --</option>
                                    <option value="SB">SB (Southbound)</option>
                                    <option value="NB">NB (Northbound)</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <Label>Status</Label>
                                <select v-model="editForm.status" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option v-for="s in statusOptions" :key="s" :value="s">{{ formatStatus(s) }}</option>
                                </select>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <Label>Remarks</Label>
                                <Input v-model="editForm.remarks" placeholder="Optional remarks" />
                            </div>
                        </div>
                        <DialogFooter>
                            <Button type="submit" :disabled="editForm.processing">
                                Update Entry
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
