import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href?: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
    items?: NavItem[];
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface Client {
    id: number;
    uuid: string;
    user_id: number;
    name: string;
    state: string;
    street: string;
    street_extra?: string | null;
    zip: string;
    city: string;
    identification_number: string;
    vat_identification_number: string;
    vat_identification_number_sk?: string | null;
    registry_info?: string | null;
    contact_name?: string | null;
    contact_phone?: string | null;
    contact_email?: string | null;
    contact_web?: string | null;
    created_at: string;
    updated_at: string;
}

export interface Product {
    id: number;
    uuid: string;
    user_id: number;
    type: string;
    name: string;
    description?: string | null;
    price: number;
    taxRate: number;
    discount?: number | null;
    unit: string;
    sku?: string | null;
    weight?: number | null;
    has_image?: boolean | null;
    image_url?: string | null;
    created_at: string;
    updated_at: string;
}

export interface Company {
    id: number;
    uuid: string;
    user_id: number;
    default: boolean;
    name: string;
    state?: string | null;
    street?: string | null;
    street_extra?: string | null;
    zip?: string | null;
    city?: string | null;
    tax_type?: string | null;
    identification_number?: string | null;
    vat_identification_number?: string | null;
    vat_identification_number_sk?: string | null;
    registry_info?: string | null;
    contact_name?: string | null;
    contact_phone?: string | null;
    contact_email?: string | null;
    contact_web?: string | null;
    payment_methods?: any;
    template?: any;
    created_at: string;
    updated_at: string;
}

export interface InvoiceItem {
    name: string;
    quantity: number;
    price: number;
    tax: number;
}

export interface Invoice {
    id: number;
    uuid: string;
    company_id: number;
    company_uuid?: string;

    number: string;
    billed_date: string;
    due_date: string;
    send_date: string;

    variable_symbol?: string | null;
    constant_symbol?: string | null;
    specific_symbol?: string | null;

    order_id?: string | null;

    billed_from_client: any;
    billed_to_client: any;
    billed_to_client_id?: string | null;
    items: InvoiceItem[];

    payment: string;
    bank_transfer?: any;

    note?: string | null;

    totalPrice: number;
    totalPrice_with_tax: number;
    totalPrice_tax: number;
    cash_payment_rounding?: number;
    tax_data?: any;

    currency_3_code: string;
    currency_symbol?: string;
    language_2_code: string;
    template?: string;
    template_primary_color?: string;
    template_date_format?: string;
    template_price_decimal_format?: string;
    template_price_thousands_format?: string;

    template_show_due_date?: boolean;
    template_show_send_date?: boolean;
    template_show_quantity?: boolean;
    template_show_payment?: boolean;
    template_show_qr_payment?: boolean;

    paid: boolean;
    sent: boolean;
    open: boolean;

    created_at: string;
    updated_at: string;
}
