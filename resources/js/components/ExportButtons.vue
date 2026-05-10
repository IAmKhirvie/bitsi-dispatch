\<script setup lang="ts">
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Calendar, Download, FileSpreadsheet } from 'lucide-vue-next';

const props = defineProps<{
    /** The entity slug, e.g. 'users', 'drivers', 'vehicles', 'trip-codes', 'attendance' */
    entity: string;
}>();

const showCustomDialog = ref(false);
const dateFrom = ref('');
const dateTo = ref('');

const today = computed(() => new Date().toISOString().split('T')[0]);

const isValidRange = computed(() => {
    return dateFrom.value && dateTo.value && dateFrom.value <= dateTo.value;
});

const dailyUrl = computed(() => `/admin/export/${props.entity}/daily`);
const weeklyUrl = computed(() => `/admin/export/${props.entity}/weekly`);
const monthlyUrl = computed(() => `/admin/export/${props.entity}/monthly`);

const customUrl = computed(() => {
    const params = new URLSearchParams({ date_from: dateFrom.value, date_to: dateTo.value });
    return `/admin/export/${props.entity}?${params.toString()}`;
});

function downloadCustom() {
    if (!isValidRange.value) return;
    window.location.href = customUrl.value;
}
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline">
                <Download class="mr-2 h-4 w-4" />
                Export Excel
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-48">
            <DropdownMenuLabel>Generate Report</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem as-child>
                <a :href="dailyUrl" class="flex items-center cursor-pointer">
                    <FileSpreadsheet class="mr-2 h-4 w-4" />
                    Daily Report
                </a>
            </DropdownMenuItem>
            <DropdownMenuItem as-child>
                <a :href="weeklyUrl" class="flex items-center cursor-pointer">
                    <FileSpreadsheet class="mr-2 h-4 w-4" />
                    Weekly Report
                </a>
            </DropdownMenuItem>
            <DropdownMenuItem as-child>
                <a :href="monthlyUrl" class="flex items-center cursor-pointer">
                    <FileSpreadsheet class="mr-2 h-4 w-4" />
                    Monthly Report
                </a>
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem @click="showCustomDialog = true">
                <Calendar class="mr-2 h-4 w-4 text-orange-600" />
                Custom Range...
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>

    <!-- Custom Date Range Dialog -->
    <Dialog v-model:open="showCustomDialog">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Custom Export Range</DialogTitle>
                <DialogDescription>
                    Select a date range for your {{ entity }} export.
                </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="date-from">From</Label>
                    <Input
                        id="date-from"
                        type="date"
                        v-model="dateFrom"
                        :max="today"
                    />
                </div>
                <div class="grid gap-2">
                    <Label for="date-to">To</Label>
                    <Input
                        id="date-to"
                        type="date"
                        v-model="dateTo"
                        :max="today"
                        :min="dateFrom"
                    />
                </div>
            </div>
            <DialogFooter>
                <Button variant="outline" @click="showCustomDialog = false">
                    Cancel
                </Button>
                <Button :disabled="!isValidRange" @click="downloadCustom">
                    <FileSpreadsheet class="mr-2 h-4 w-4" />
                    Export Excel
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
