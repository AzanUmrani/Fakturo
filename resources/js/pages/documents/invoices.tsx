import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Company, type Invoice } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { useLang } from '@/hooks/useLang';
import InvoiceSearch from './components/InvoiceSearch';
import InvoiceForm from './components/InvoiceForm';
import DeleteConfirmation from './components/DeleteConfirmation';
import InvoicesTable from './components/InvoicesTable';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { AlertCircle } from 'lucide-react';
import Pagination from "@/components/pagination";

interface InvoicesPageProps {
    invoices: {
        data: Invoice[];
        links: { url: string | null; label: string; active: boolean }[];
        from: number;
        to: number;
        total: number;
        current_page: number;
        last_page: number;
    };
    filters: {
        search?: string;
        sort_field?: string;
        sort_direction?: string;
    };
    company: Company | null;
    companies: Company[];
    error?: string;
}

export default function Invoices({ invoices, filters, company, companies, error }: InvoicesPageProps) {
    const { __ } = useLang();
    const [isDialogOpen, setIsDialogOpen] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [editingInvoice, setEditingInvoice] = useState<Invoice | null>(null);
    const [searchTerm, setSearchTerm] = useState(filters.search || '');

    // State for delete confirmation dialog
    const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
    const [deletingInvoice, setDeletingInvoice] = useState<Invoice | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    // Debounce search input
    useEffect(() => {
        const timeoutId = setTimeout(() => {
            if (searchTerm !== filters.search) {
                router.get(route('documents.invoices.index'), { search: searchTerm }, {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['invoices', 'filters']
                });
            }
        }, 300); // 300ms debounce time

        return () => clearTimeout(timeoutId);
    }, [searchTerm]);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: __('documents.title'),
            href: '/documents',
        },
        {
            title: __('invoices.title'),
            href: '/documents/invoices',
        },
    ];

    const handleEdit = (invoice: Invoice) => {
        setIsEditing(true);
        setEditingInvoice(invoice);
        setIsDialogOpen(true);
    };

    const handleDelete = (invoice: Invoice) => {
        setDeletingInvoice(invoice);
        setIsDeleteDialogOpen(true);
    };

    const handleAddClick = () => {
        if (!company) {
            // Show error message if no active company
            return;
        }

        setIsEditing(false);
        setEditingInvoice(null);
        setIsDialogOpen(true);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={__('invoices.title')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                {error && (
                    <Alert variant="destructive" className="mb-4">
                        <AlertCircle className="h-4 w-4" />
                        <AlertDescription>{error}</AlertDescription>
                    </Alert>
                )}

                {/* Search and Add Invoice Button */}
                <InvoiceSearch
                    searchTerm={searchTerm}
                    onSearchChange={setSearchTerm}
                    onAddClick={handleAddClick}
                    disabled={!company}
                />

                {/* Invoice Form Dialog */}
                {company && (
                    <InvoiceForm
                        isOpen={isDialogOpen}
                        onOpenChange={setIsDialogOpen}
                        isEditing={isEditing}
                        editingInvoice={editingInvoice}
                        company={company}
                        companies={companies}
                    />
                )}

                {/* Delete Confirmation Dialog */}
                <DeleteConfirmation
                    isOpen={isDeleteDialogOpen}
                    onOpenChange={setIsDeleteDialogOpen}
                    invoice={deletingInvoice}
                    isDeleting={isDeleting}
                    setIsDeleting={setIsDeleting}
                />

                {/* Invoices Table */}
                <InvoicesTable
                    invoices={invoices.data}
                    companyUuid={company?.uuid || ''}
                    filters={filters}
                    onEdit={handleEdit}
                    onDelete={handleDelete}
                    onStatusChange={() => router.reload()}
                />

                {/* Pagination */}
                {invoices.data.length > 0 && (
                    <Pagination
                        from={invoices.from}
                        to={invoices.to}
                        total={invoices.total}
                        links={invoices.links}
                        routeName="documents.invoices.index"
                        entityName="invoices.pagination.invoices"
                    />
                )}
            </div>
        </AppLayout>
    );
}
