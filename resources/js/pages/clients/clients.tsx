import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Client } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { useLang } from '@/hooks/useLang';
import ClientSearch from './components/ClientSearch';
import ClientForm from './components/ClientForm';
import DeleteConfirmation from './components/DeleteConfirmation';
import ClientsTable from './components/ClientsTable';
import Pagination from '@/components/pagination';

interface ClientsPageProps {
    clients: {
        data: Client[];
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

export default function Clients({ clients, filters }: ClientsPageProps) {
    const { __ } = useLang();
    const [isDialogOpen, setIsDialogOpen] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [editingClient, setEditingClient] = useState<Client | null>(null);
    const [searchTerm, setSearchTerm] = useState(filters.search || '');

    // State for delete confirmation dialog
    const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
    const [deletingClient, setDeletingClient] = useState<Client | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    // Debounce search input
    useEffect(() => {
        const timeoutId = setTimeout(() => {
            if (searchTerm !== filters.search) {
                router.get(route('clients.index'), { search: searchTerm }, {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['clients', 'filters']
                });
            }
        }, 300); // 300ms debounce time

        return () => clearTimeout(timeoutId);
    }, [searchTerm]);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Clients',
            href: '/clients',
        },
    ];

    const handleEdit = (client: Client) => {
        setIsEditing(true);
        setEditingClient(client);
        setIsDialogOpen(true);
    };

    const handleDelete = (client: Client) => {
        setDeletingClient(client);
        setIsDeleteDialogOpen(true);
    };

    const handleAddClick = () => {
        setIsEditing(false);
        setEditingClient(null);
        setIsDialogOpen(true);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Clients" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                {/* Search and Add Client Button */}
                <ClientSearch
                    searchTerm={searchTerm}
                    onSearchChange={setSearchTerm}
                    onAddClick={handleAddClick}
                />

                {/* Client Form Dialog */}
                <ClientForm
                    isOpen={isDialogOpen}
                    onOpenChange={setIsDialogOpen}
                    isEditing={isEditing}
                    editingClient={editingClient}
                />

                {/* Delete Confirmation Dialog */}
                <DeleteConfirmation
                    isOpen={isDeleteDialogOpen}
                    onOpenChange={setIsDeleteDialogOpen}
                    client={deletingClient}
                    isDeleting={isDeleting}
                    setIsDeleting={setIsDeleting}
                />

                {/* Clients Table */}
                <ClientsTable
                    clients={clients.data}
                    filters={filters}
                    onEdit={handleEdit}
                    onDelete={handleDelete}
                />

                {/* Pagination */}
                {clients.data.length > 0 && (
                    <Pagination
                        from={clients.from}
                        to={clients.to}
                        total={clients.total}
                        links={clients.links}
                        routeName="clients.index"
                        entityName="clients.pagination.clients"
                    />
                )}
            </div>
        </AppLayout>
    );
}
