import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Company } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { useLang } from '@/hooks/useLang';
import CompanySearch from './components/CompanySearch';
import CompanyForm from './components/CompanyForm';
import DeleteConfirmation from './components/DeleteConfirmation';
import CompaniesTable from './components/CompaniesTable';
import Pagination from './components/Pagination';

interface CompaniesPageProps {
    companies: {
        data: Company[];
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
}

export default function Companies({ companies, filters }: CompaniesPageProps) {
    const { __ } = useLang();
    const [isDialogOpen, setIsDialogOpen] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [editingCompany, setEditingCompany] = useState<Company | null>(null);
    const [searchTerm, setSearchTerm] = useState(filters.search || '');

    // State for delete confirmation dialog
    const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
    const [deletingCompany, setDeletingCompany] = useState<Company | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    // Debounce search input
    useEffect(() => {
        const timeoutId = setTimeout(() => {
            if (searchTerm !== filters.search) {
                router.get(route('companies.index'), { search: searchTerm }, {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['companies', 'filters']
                });
            }
        }, 300); // 300ms debounce time

        return () => clearTimeout(timeoutId);
    }, [searchTerm]);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: __('companies.title'),
            href: '/companies',
        },
    ];

    const handleEdit = (company: Company) => {
        setIsEditing(true);
        setEditingCompany(company);
        setIsDialogOpen(true);
    };

    const handleDelete = (company: Company) => {
        setDeletingCompany(company);
        setIsDeleteDialogOpen(true);
    };

    const handleAddClick = () => {
        setIsEditing(false);
        setEditingCompany(null);
        setIsDialogOpen(true);
    };

    const handleFormSuccess = () => {
        setIsDialogOpen(false);
        // Refetch the companies list
        router.get(route('companies.index'), { search: searchTerm }, {
            preserveState: true,
            preserveScroll: true,
            only: ['companies', 'filters']
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={__('companies.title')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                {/* Search and Add Company Button */}
                <CompanySearch
                    searchTerm={searchTerm}
                    onSearchChange={setSearchTerm}
                    onAddClick={handleAddClick}
                />

                {/* Company Form Dialog */}
                <CompanyForm
                    isOpen={isDialogOpen}
                    onOpenChange={setIsDialogOpen}
                    isEditing={isEditing}
                    editingCompany={editingCompany}
                    onSuccess={handleFormSuccess}
                />

                {/* Delete Confirmation Dialog */}
                <DeleteConfirmation
                    isOpen={isDeleteDialogOpen}
                    onOpenChange={setIsDeleteDialogOpen}
                    company={deletingCompany}
                    isDeleting={isDeleting}
                    setIsDeleting={setIsDeleting}
                />

                {/* Companies Table */}
                <CompaniesTable
                    companies={companies.data}
                    filters={filters}
                    onEdit={handleEdit}
                    onDelete={handleDelete}
                />

                {/* Pagination */}
                {companies.data.length > 0 && (
                    <Pagination
                        from={companies.from}
                        to={companies.to}
                        total={companies.total}
                        links={companies.links}
                    />
                )}
            </div>
        </AppLayout>
    );
}
