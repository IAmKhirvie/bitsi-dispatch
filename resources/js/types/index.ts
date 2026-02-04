import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: {
        location: string;
        url: string;
        port: null | number;
        defaults: Record<string, unknown>;
        routes: Record<string, string>;
    };
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    role: 'admin' | 'operations_manager' | 'dispatcher';
    phone: string | null;
    is_active: boolean;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type DriverStatus = 'available' | 'dispatched' | 'on_route' | 'on_leave';

export interface Driver {
    id: number;
    name: string;
    phone: string | null;
    license_number: string | null;
    is_active: boolean;
    status: DriverStatus;
    created_at: string;
    updated_at: string;
}

export interface Vehicle {
    id: number;
    bus_number: string;
    brand: string;
    bus_type: string;
    plate_number: string;
    status: string;
    gps_device_id: string | null;
    pms_unit: string;
    pms_threshold: number;
    current_pms_value: number;
    last_pms_date: string | null;
    idle_days: number;
    last_used_at: string | null;
    is_pms_warning?: boolean;
    pms_percentage?: number;
    created_at: string;
    updated_at: string;
}

export interface TripCode {
    id: number;
    code: string;
    operator: string;
    origin_terminal: string;
    destination_terminal: string;
    bus_type: string;
    scheduled_departure_time: string;
    direction: 'SB' | 'NB';
    is_active: boolean;
    route_display?: string;
    created_at: string;
    updated_at: string;
}

export interface DispatchDay {
    id: number;
    service_date: string;
    created_by: number;
    notes: string | null;
    entries?: DispatchEntry[];
    summary?: DailySummary;
    creator?: User;
    created_at: string;
    updated_at: string;
}

export interface DispatchEntry {
    id: number;
    dispatch_day_id: number;
    vehicle_id: number | null;
    trip_code_id: number | null;
    driver_id: number | null;
    brand: string | null;
    bus_number: string | null;
    route: string | null;
    bus_type: string | null;
    departure_terminal: string | null;
    arrival_terminal: string | null;
    scheduled_departure: string | null;
    actual_departure: string | null;
    direction: 'SB' | 'NB' | null;
    status: string;
    remarks: string | null;
    sort_order: number;
    vehicle?: Vehicle;
    trip_code?: TripCode;
    driver?: Driver;
    dispatch_day?: DispatchDay;
    created_at: string;
    updated_at: string;
}

export interface DailySummary {
    id: number;
    dispatch_day_id: number;
    total_trips: number;
    sb_trips: number;
    nb_trips: number;
    naga_trips: number;
    legazpi_trips: number;
    sorsogon_trips: number;
    virac_trips: number;
    masbate_trips: number;
    tabaco_trips: number;
    visayas_trips: number;
    cargo_trips: number;
}

export interface PaginatedData<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export type BreadcrumbItemType = BreadcrumbItem;
