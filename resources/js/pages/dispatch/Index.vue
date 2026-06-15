<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button, buttonVariants } from '@/components/ui/button';
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
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { type BreadcrumbItem, type DispatchDay, type DispatchEntry, type TripCode, type Vehicle, type Driver } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Calendar, ChevronLeft, ChevronRight, Check } from 'lucide-vue-next';
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

watch(selectedDate, (newDate) => {
    router.get('/dispatch', { date: newDate }, { preserveState: true, preserveScroll: true });
});

const createDayForm = useForm({ service_date: props.date, notes: '' });

function createDispatchDay() {
    createDayForm.service_date = selectedDate.value;
    createDayForm.post('/dispatch', { preserveScroll: true });
}

type EntryFormShape = {
    trip_code_id: number | null;
    vehicle_id: number | null;
    driver_id: number | null;
    driver2_id: number | null;
    brand: string;
    bus_number: string;
    route: string;
    bus_type: string;
    departure_terminal: string;
    arrival_terminal: string;
    scheduled_departure: string;
    actual_departure: string;
    direction: string;
    status: string;
    remarks: string;
};

const emptyEntry: EntryFormShape = {
    trip_code_id: null, vehicle_id: null, driver_id: null, driver2_id: null,
    brand: '', bus_number: '', route: '', bus_type: '',
    departure_terminal: '', arrival_terminal: '',
    scheduled_departure: '', actual_departure: '',
    direction: '', status: 'scheduled', remarks: '',
};

const addForm = useForm({ ...emptyEntry });
const editForm = useForm({ ...emptyEntry });

const addStep = ref(1);
const editStep = ref(1);
const TOTAL_STEPS = 3;
const stepLabels = ['Trip & crew', 'Route & times', 'Status & remarks'];

function onTripCodeChange(form: typeof addForm, tripCodeId: number | null) {
    if (!tripCodeId) return;
    const tc = props.tripCodes.find((t) => t.id === tripCodeId);
    if (tc) {
        form.route = `${tc.origin_terminal} - ${tc.destination_terminal}`;
        form.bus_type = tc.bus_type;
        form.departure_terminal = tc.origin_terminal;
        form.arrival_terminal = tc.destination_terminal;
        form.scheduled_departure = tc.scheduled_departure_time;
        form.direction = tc.direction;
    }
}

function onVehicleChange(form: typeof addForm, vehicleId: number | null) {
    if (!vehicleId) return;
    const v = props.vehicles.find((vv) => vv.id === vehicleId);
    if (v) {
        form.brand = v.brand;
        form.bus_number = v.bus_number;
        if (!form.bus_type) form.bus_type = v.bus_type;
    }
}

function submitAddEntry() {
    if (!props.dispatchDay) return;
    addForm.post(`/dispatch/${props.dispatchDay.id}/entries`, {
        preserveScroll: true,
        onSuccess: () => {
            showAddDialog.value = false;
            addForm.reset();
            addStep.value = 1;
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
    editStep.value = 1;
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
    router.delete(`/dispatch/${props.dispatchDay.id}/entries/${entry.id}`, { preserveScroll: true });
}

const statusOptions = ['scheduled', 'departed', 'on_route', 'delayed', 'breakdown', 'arrived', 'cancelled'];

function setEntryStatus(entry: DispatchEntry, status: string) {
    if (!props.dispatchDay || entry.status === status) return;

    // Optimistic UI update - instantly show new status for immediate feedback
    const previousStatus = entry.status;
    entry.status = status;

    router.patch(`/dispatch/${props.dispatchDay.id}/entries/${entry.id}/status`, { status }, {
        preserveState: true,
        preserveScroll: true,
        onError: () => {
            // Revert on error
            entry.status = previousStatus;
        },
    });
}

function formatStatus(status: string): string {
    return status.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

function formatTime(time: string | null): string {
    if (!time) return '--';
    return time.substring(0, 5);
}

const entries = computed(() => props.dispatchDay?.entries || []);
const summary = computed(() => props.dispatchDay?.summary);

// Step 1 requires trip code + vehicle + driver 1
function canAdvance(form: typeof addForm, step: number): boolean {
    if (step === 1) return !!(form.trip_code_id && form.vehicle_id && form.driver_id);
    if (step === 2) return !!(form.scheduled_departure && form.direction);
    return true;
}

// Direction badge colors via inline style — same source of truth as StatusBadge
const directionStyle = (dir: string) =>
    dir === 'SB'
        ? { backgroundColor: 'hsl(214 95% 93%)', color: 'hsl(217 76% 38%)' }
        : { backgroundColor: 'hsl(280 65% 92%)', color: 'hsl(280 60% 38%)' };
</script>

<template>
    <Head title="Dispatch Board" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Dispatch Board</h1>
                    <p class="text-sm text-muted-foreground">Manage daily bus dispatch operations</p>
                </div>
                <div class="flex items-center gap-2">
                    <Calendar class="h-4 w-4 text-muted-foreground" />
                    <Input type="date" v-model="selectedDate" class="w-44" />
                </div>
            </div>

            <div v-if="summary" class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-6">
                <div class="rounded-lg border bg-card p-3 text-center">
                    <div class="text-lg font-bold">{{ summary.total_trips }}</div>
                    <div class="text-xs text-muted-foreground">Total</div>
                </div>
                <div class="rounded-lg border bg-card p-3 text-center">
                    <div class="text-lg font-bold" :style="{ color: 'hsl(217 76% 38%)' }">{{ summary.sb_trips }}</div>
                    <div class="text-xs text-muted-foreground">SB</div>
                </div>
                <div class="rounded-lg border bg-card p-3 text-center">
                    <div class="text-lg font-bold" :style="{ color: 'hsl(280 60% 38%)' }">{{ summary.nb_trips }}</div>
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

            <Card v-if="!dispatchDay">
                <CardContent class="flex flex-col items-center justify-center py-12">
                    <Calendar class="mb-4 h-12 w-12 text-muted-foreground" />
                    <h3 class="mb-2 text-lg font-semibold">No Dispatch Day</h3>
                    <p class="mb-4 text-sm text-muted-foreground">
                        No dispatch day exists for {{ selectedDate }}. Create one to start dispatching.
                    </p>
                    <Button @click="createDispatchDay" :disabled="createDayForm.processing">
                        <Plus class="mr-2 h-4 w-4" /> Create Dispatch Day
                    </Button>
                </CardContent>
            </Card>

            <Card v-else>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-3">
                    <CardTitle class="text-base">
                        Entries for {{ dispatchDay.service_date }}
                        <span class="ml-2 text-sm font-normal text-muted-foreground">({{ entries.length }} entries)</span>
                    </CardTitle>
                    <Dialog v-model:open="showAddDialog" @update:open="(v) => { if (v) addStep = 1 }">
                        <DialogTrigger as-child>
                            <Button size="sm"><Plus class="mr-1 h-4 w-4" /> Add Entry</Button>
                        </DialogTrigger>
                        <DialogContent class="max-w-2xl">
                            <DialogHeader>
                                <DialogTitle>Add Dispatch Entry</DialogTitle>
                                <DialogDescription>
                                    Step {{ addStep }} of {{ TOTAL_STEPS }} — {{ stepLabels[addStep - 1] }}
                                </DialogDescription>
                            </DialogHeader>

                            <!-- Stepper -->
                            <div class="flex items-center gap-2">
                                <template v-for="(label, i) in stepLabels" :key="label">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold"
                                            :class="addStep > i + 1
                                                ? 'bg-primary text-primary-foreground'
                                                : addStep === i + 1
                                                    ? 'bg-primary text-primary-foreground ring-2 ring-primary/30'
                                                    : 'bg-muted text-muted-foreground'"
                                        >
                                            <Check v-if="addStep > i + 1" class="h-3.5 w-3.5" />
                                            <span v-else>{{ i + 1 }}</span>
                                        </div>
                                        <span class="text-xs font-medium" :class="addStep === i + 1 ? 'text-foreground' : 'text-muted-foreground'">
                                            {{ label }}
                                        </span>
                                    </div>
                                    <div v-if="i < stepLabels.length - 1" class="h-px flex-1 bg-border" />
                                </template>
                            </div>

                            <form @submit.prevent="submitAddEntry" class="space-y-4">
                                <!-- Step 1: Trip & crew -->
                                <div v-if="addStep === 1" class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2 space-y-2">
                                        <Label>Trip Code <span class="text-destructive">*</span></Label>
                                        <Select
                                            :model-value="addForm.trip_code_id ? String(addForm.trip_code_id) : ''"
                                            @update:model-value="(v) => { addForm.trip_code_id = v ? Number(v) : null; onTripCodeChange(addForm, addForm.trip_code_id) }"
                                        >
                                            <SelectTrigger><SelectValue placeholder="Select trip code" /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="tc in tripCodes" :key="tc.id" :value="String(tc.id)">
                                                    {{ tc.code }} ({{ tc.origin_terminal }} → {{ tc.destination_terminal }})
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="addForm.errors.trip_code_id" class="text-xs text-destructive">{{ addForm.errors.trip_code_id }}</p>
                                    </div>
                                    <div class="col-span-2 space-y-2">
                                        <Label>Vehicle <span class="text-destructive">*</span></Label>
                                        <Select
                                            :model-value="addForm.vehicle_id ? String(addForm.vehicle_id) : ''"
                                            @update:model-value="(v) => { addForm.vehicle_id = v ? Number(v) : null; onVehicleChange(addForm, addForm.vehicle_id) }"
                                        >
                                            <SelectTrigger><SelectValue placeholder="Select vehicle" /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="v in vehicles" :key="v.id" :value="String(v.id)">
                                                    {{ v.brand }} {{ v.bus_number }} ({{ v.plate_number }})
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="addForm.errors.vehicle_id" class="text-xs text-destructive">{{ addForm.errors.vehicle_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Driver 1 <span class="text-destructive">*</span></Label>
                                        <Select
                                            :model-value="addForm.driver_id ? String(addForm.driver_id) : ''"
                                            @update:model-value="(v) => addForm.driver_id = v ? Number(v) : null"
                                        >
                                            <SelectTrigger><SelectValue placeholder="Select driver" /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="d in drivers" :key="d.id" :value="String(d.id)">{{ d.name }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="addForm.errors.driver_id" class="text-xs text-destructive">{{ addForm.errors.driver_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Driver 2 (optional)</Label>
                                        <Select
                                            :model-value="addForm.driver2_id ? String(addForm.driver2_id) : ''"
                                            @update:model-value="(v) => addForm.driver2_id = v ? Number(v) : null"
                                        >
                                            <SelectTrigger><SelectValue placeholder="Select driver" /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="d in drivers" :key="d.id" :value="String(d.id)">{{ d.name }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                <!-- Step 2: Route & times -->
                                <div v-if="addStep === 2" class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2"><Label>Brand</Label><Input v-model="addForm.brand" placeholder="e.g. DLTB" /></div>
                                    <div class="space-y-2"><Label>Bus Number</Label><Input v-model="addForm.bus_number" placeholder="e.g. 2801" /></div>
                                    <div class="col-span-2 space-y-2"><Label>Route</Label><Input v-model="addForm.route" placeholder="e.g. Cubao - Naga" /></div>
                                    <div class="space-y-2"><Label>Bus Type</Label><Input v-model="addForm.bus_type" placeholder="e.g. Airconditioned" /></div>
                                    <div class="space-y-2">
                                        <Label>Direction <span class="text-destructive">*</span></Label>
                                        <Select v-model="addForm.direction">
                                            <SelectTrigger><SelectValue placeholder="Select direction" /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="SB">SB (Southbound)</SelectItem>
                                                <SelectItem value="NB">NB (Northbound)</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div class="space-y-2"><Label>Departure Terminal</Label><Input v-model="addForm.departure_terminal" /></div>
                                    <div class="space-y-2"><Label>Arrival Terminal</Label><Input v-model="addForm.arrival_terminal" /></div>
                                    <div class="space-y-2">
                                        <Label>Scheduled Departure <span class="text-destructive">*</span></Label>
                                        <Input type="time" v-model="addForm.scheduled_departure" />
                                    </div>
                                    <div class="space-y-2"><Label>Actual Departure</Label><Input type="time" v-model="addForm.actual_departure" /></div>
                                </div>

                                <!-- Step 3: Status & remarks -->
                                <div v-if="addStep === 3" class="space-y-4">
                                    <div class="space-y-2">
                                        <Label>Status</Label>
                                        <Select v-model="addForm.status">
                                            <SelectTrigger><SelectValue /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="s in statusOptions" :key="s" :value="s">{{ formatStatus(s) }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Remarks</Label>
                                        <Input v-model="addForm.remarks" placeholder="Optional remarks" />
                                    </div>
                                    <div class="rounded-md border bg-muted/30 p-3 text-sm">
                                        <p class="mb-2 font-medium">Review</p>
                                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-muted-foreground">
                                            <span>Trip code:</span><span class="text-foreground">{{ tripCodes.find(t => t.id === addForm.trip_code_id)?.code || '—' }}</span>
                                            <span>Vehicle:</span><span class="text-foreground">{{ vehicles.find(v => v.id === addForm.vehicle_id)?.bus_number || '—' }}</span>
                                            <span>Driver 1:</span><span class="text-foreground">{{ drivers.find(d => d.id === addForm.driver_id)?.name || '—' }}</span>
                                            <span>Route:</span><span class="text-foreground">{{ addForm.route || '—' }}</span>
                                            <span>Scheduled:</span><span class="text-foreground">{{ addForm.scheduled_departure || '—' }}</span>
                                            <span>Direction:</span><span class="text-foreground">{{ addForm.direction || '—' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <DialogFooter class="!justify-between">
                                    <Button
                                        v-if="addStep > 1"
                                        type="button"
                                        variant="outline"
                                        @click="addStep--"
                                    >
                                        <ChevronLeft class="mr-1 h-4 w-4" /> Back
                                    </Button>
                                    <span v-else />
                                    <Button
                                        v-if="addStep < TOTAL_STEPS"
                                        type="button"
                                        :disabled="!canAdvance(addForm, addStep)"
                                        @click="addStep++"
                                    >
                                        Next <ChevronRight class="ml-1 h-4 w-4" />
                                    </Button>
                                    <Button v-else type="submit" :disabled="addForm.processing">
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
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Actual Arr.</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Dir.</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Driver 1</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Driver 2</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Status</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Quick Status</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Remarks</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="entries.length === 0">
                                    <td colspan="18" class="px-3 py-8 text-center text-sm text-muted-foreground">
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
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ formatTime(entry.actual_arrival) }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">
                                        <span v-if="entry.direction" class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium" :style="directionStyle(entry.direction)">
                                            {{ entry.direction }}
                                        </span>
                                        <span v-else>--</span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ entry.driver?.name || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ entry.driver2?.name || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">
                                        <StatusBadge :status="entry.status" />
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-1.5">
                                        <div class="flex flex-wrap items-center gap-1">
                                            <button
                                                v-for="s in statusOptions"
                                                :key="s"
                                                @click="setEntryStatus(entry, s)"
                                                class="rounded px-1.5 py-0.5 text-[10px] font-medium transition-colors"
                                                :class="entry.status === s
                                                    ? 'bg-primary text-primary-foreground'
                                                    : 'bg-muted text-muted-foreground hover:bg-muted/80'"
                                                :title="`Set ${formatStatus(s)}`"
                                            >
                                                {{ formatStatus(s) }}
                                            </button>
                                        </div>
                                    </td>
                                    <td class="max-w-[120px] truncate px-3 py-1.5" :title="entry.remarks || ''">{{ entry.remarks || '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">
                                        <div class="flex items-center gap-1">
                                            <button @click="openEditDialog(entry)" class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground" title="Edit">
                                                <Pencil class="h-3.5 w-3.5" />
                                            </button>
                                            <AlertDialog>
                                                <AlertDialogTrigger
                                                    :class="'rounded p-1 text-muted-foreground hover:bg-destructive/10 hover:text-destructive'"
                                                    title="Delete entry"
                                                >
                                                    <Trash2 class="h-3.5 w-3.5" />
                                                </AlertDialogTrigger>
                                                <AlertDialogContent>
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle>Delete this dispatch entry?</AlertDialogTitle>
                                                        <AlertDialogDescription>
                                                            The entry will be moved to Trash. You can restore it from Admin → Trash.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                                        <AlertDialogAction
                                                            :class="buttonVariants({ variant: 'destructive' })"
                                                            @click="deleteEntry(entry)"
                                                        >
                                                            Delete
                                                        </AlertDialogAction>
                                                    </AlertDialogFooter>
                                                </AlertDialogContent>
                                            </AlertDialog>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Edit Dialog (wizard) -->
            <Dialog v-model:open="showEditDialog">
                <DialogContent class="max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>Edit Dispatch Entry</DialogTitle>
                        <DialogDescription>
                            Step {{ editStep }} of {{ TOTAL_STEPS }} — {{ stepLabels[editStep - 1] }}
                        </DialogDescription>
                    </DialogHeader>

                    <div class="flex items-center gap-2">
                        <template v-for="(label, i) in stepLabels" :key="label">
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold"
                                    :class="editStep > i + 1
                                        ? 'bg-primary text-primary-foreground'
                                        : editStep === i + 1
                                            ? 'bg-primary text-primary-foreground ring-2 ring-primary/30'
                                            : 'bg-muted text-muted-foreground'"
                                >
                                    <Check v-if="editStep > i + 1" class="h-3.5 w-3.5" />
                                    <span v-else>{{ i + 1 }}</span>
                                </div>
                                <span class="text-xs font-medium" :class="editStep === i + 1 ? 'text-foreground' : 'text-muted-foreground'">{{ label }}</span>
                            </div>
                            <div v-if="i < stepLabels.length - 1" class="h-px flex-1 bg-border" />
                        </template>
                    </div>

                    <form @submit.prevent="submitEditEntry" class="space-y-4">
                        <div v-if="editStep === 1" class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 space-y-2">
                                <Label>Trip Code</Label>
                                <Select
                                    :model-value="editForm.trip_code_id ? String(editForm.trip_code_id) : ''"
                                    @update:model-value="(v) => { editForm.trip_code_id = v ? Number(v) : null; onTripCodeChange(editForm, editForm.trip_code_id) }"
                                >
                                    <SelectTrigger><SelectValue placeholder="Select trip code" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="tc in tripCodes" :key="tc.id" :value="String(tc.id)">
                                            {{ tc.code }} ({{ tc.origin_terminal }} → {{ tc.destination_terminal }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <Label>Vehicle</Label>
                                <Select
                                    :model-value="editForm.vehicle_id ? String(editForm.vehicle_id) : ''"
                                    @update:model-value="(v) => { editForm.vehicle_id = v ? Number(v) : null; onVehicleChange(editForm, editForm.vehicle_id) }"
                                >
                                    <SelectTrigger><SelectValue placeholder="Select vehicle" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="v in vehicles" :key="v.id" :value="String(v.id)">
                                            {{ v.brand }} {{ v.bus_number }} ({{ v.plate_number }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Driver 1</Label>
                                <Select
                                    :model-value="editForm.driver_id ? String(editForm.driver_id) : ''"
                                    @update:model-value="(v) => editForm.driver_id = v ? Number(v) : null"
                                >
                                    <SelectTrigger><SelectValue placeholder="Select driver" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="d in drivers" :key="d.id" :value="String(d.id)">{{ d.name }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Driver 2</Label>
                                <Select
                                    :model-value="editForm.driver2_id ? String(editForm.driver2_id) : ''"
                                    @update:model-value="(v) => editForm.driver2_id = v ? Number(v) : null"
                                >
                                    <SelectTrigger><SelectValue placeholder="Select driver" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="d in drivers" :key="d.id" :value="String(d.id)">{{ d.name }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div v-if="editStep === 2" class="grid grid-cols-2 gap-4">
                            <div class="space-y-2"><Label>Brand</Label><Input v-model="editForm.brand" /></div>
                            <div class="space-y-2"><Label>Bus Number</Label><Input v-model="editForm.bus_number" /></div>
                            <div class="col-span-2 space-y-2"><Label>Route</Label><Input v-model="editForm.route" /></div>
                            <div class="space-y-2"><Label>Bus Type</Label><Input v-model="editForm.bus_type" /></div>
                            <div class="space-y-2">
                                <Label>Direction</Label>
                                <Select v-model="editForm.direction">
                                    <SelectTrigger><SelectValue placeholder="Select direction" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="SB">SB (Southbound)</SelectItem>
                                        <SelectItem value="NB">NB (Northbound)</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2"><Label>Departure Terminal</Label><Input v-model="editForm.departure_terminal" /></div>
                            <div class="space-y-2"><Label>Arrival Terminal</Label><Input v-model="editForm.arrival_terminal" /></div>
                            <div class="space-y-2"><Label>Scheduled Departure</Label><Input type="time" v-model="editForm.scheduled_departure" /></div>
                            <div class="space-y-2"><Label>Actual Departure</Label><Input type="time" v-model="editForm.actual_departure" /></div>
                        </div>

                        <div v-if="editStep === 3" class="space-y-4">
                            <div class="space-y-2">
                                <Label>Status</Label>
                                <Select v-model="editForm.status">
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="s in statusOptions" :key="s" :value="s">{{ formatStatus(s) }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Remarks</Label>
                                <Input v-model="editForm.remarks" />
                            </div>
                        </div>

                        <DialogFooter class="!justify-between">
                            <Button v-if="editStep > 1" type="button" variant="outline" @click="editStep--">
                                <ChevronLeft class="mr-1 h-4 w-4" /> Back
                            </Button>
                            <span v-else />
                            <Button v-if="editStep < TOTAL_STEPS" type="button" @click="editStep++">
                                Next <ChevronRight class="ml-1 h-4 w-4" />
                            </Button>
                            <Button v-else type="submit" :disabled="editForm.processing">
                                Update Entry
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
