import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Company } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { useLang } from '@/hooks/useLang';
import PreInvoiceSearch from './precomponents/PreInvoiceSearch';
import PreInvoiceForm from './precomponents/PreInvoiceForm';
import PreInvoiceDeleteConfirmation from './precomponents/PreinvoiceDeleteConfirmation';
import PreInvoicesTable from './precomponents/PreinvoicesTable';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { AlertCircle } from 'lucide-react';
import Pagination from "@/components/pagination";

export interface PreInvoice {
  id: number;
  uuid: string;
  number: string;
  date: string;
  client_name: string;
  total: number;
  currency: string;
  status: 'draft' | 'sent' | 'approved';
}

interface PreInvoicesPageProps {
  preInvoices: {
    data: PreInvoice[];
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
  companies: Company[];
  error?: string;
}

export default function PreInvoicesPage({ preInvoices, filters, companies, error }: PreInvoicesPageProps) {
  const { __ } = useLang();
  const filtersObj = filters || {};
  const preInvoicesData = preInvoices || { data: [], links: [], from: 0, to: 0, total: 0, current_page: 1, last_page: 1 };
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [editingInvoice, setEditingInvoice] = useState<PreInvoice | null>(null);
  const [searchTerm, setSearchTerm] = useState((filtersObj?.search as string) || '');
  
  // Get company UUID from companies prop or localStorage
  const companyUuid = companies && companies.length > 0 
    ? companies[0].uuid 
    : localStorage.getItem('companyUuid') || '';

  // State for delete confirmation dialog
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
  const [deletingInvoice, setDeletingInvoice] = useState<PreInvoice | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);

  // Debounce search input
  useEffect(() => {
    if (companyUuid) {
      localStorage.setItem('companyUuid', companyUuid);
    }
  }, [companyUuid]);

  useEffect(() => {
    const timeoutId = setTimeout(() => {
      if (searchTerm !== (filtersObj?.search as string)) {
        router.get('/documents/pre-invoices', { search: searchTerm }, {
          preserveState: true,
          preserveScroll: true,
          only: ['preInvoices', 'filters']
        });
      }
    }, 300); // 300ms debounce time

    return () => clearTimeout(timeoutId);
  }, [searchTerm]);

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Documents', href: '/documents' },
    { title: 'Pre Invoices', href: '/documents/pre-invoices' },
  ];

  const handleEdit = (invoice: PreInvoice) => {
    setIsEditing(true);
    setEditingInvoice(invoice);
    setIsDialogOpen(true);
  };

  const handleDelete = (invoice: PreInvoice) => {
    setDeletingInvoice(invoice);
    setIsDeleteDialogOpen(true);
  };

  const handleAddClick = () => {
    setIsEditing(false);
    setEditingInvoice(null);
    setIsDialogOpen(true);
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Pre Invoices" />
      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
        {error && (
          <Alert variant="destructive" className="mb-4">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>{error}</AlertDescription>
          </Alert>
        )}

        {/* Search and Add Pre Invoice Button */}
        <PreInvoiceSearch
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
          onAddClick={handleAddClick}
        />

        {/* Pre Invoice Form Dialog */}
        <PreInvoiceForm
          isOpen={isDialogOpen}
          onOpenChange={setIsDialogOpen}
          isEditing={isEditing}
          editingInvoice={editingInvoice}
          companyUuid={companyUuid}
          companies={companies}
        />

        {/* Delete Confirmation Dialog */}
        <PreInvoiceDeleteConfirmation
          isOpen={isDeleteDialogOpen}
          onOpenChange={setIsDeleteDialogOpen}
          invoice={deletingInvoice}
          isDeleting={isDeleting}
          setIsDeleting={setIsDeleting}
        />

        {/* Pre Invoices Table */}
        <PreInvoicesTable
          invoices={preInvoicesData.data}
          filters={filters}
          onEdit={handleEdit}
          onDelete={handleDelete}
          onStatusChange={() => {
            router.get('/documents/pre-invoices', { company_uuid: companyUuid }, {
              preserveState: true,
              preserveScroll: true,
              only: ['preInvoices', 'filters']
            });
          }}
          companyUuid={companyUuid}
        />

        {/* Pagination */}
        {preInvoicesData.data.length > 0 && (
          <Pagination
            from={preInvoicesData.from}
            to={preInvoicesData.to}
            total={preInvoicesData.total}
            links={preInvoicesData.links}
            routeName="documents.pre-invoices.index"
            entityName="pre_invoices.pagination.pre_invoices"
          />
        )}
      </div>
    </AppLayout>
  );
}
