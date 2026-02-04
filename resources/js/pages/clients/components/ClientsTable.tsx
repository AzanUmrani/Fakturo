import { Button } from '@/components/ui/button';
import { type Client } from '@/types';
import { router } from '@inertiajs/react';
import { useLang } from '@/hooks/useLang';

interface ClientsTableProps {
    clients: Client[];
    filters: {
        search?: string;
        sort_field?: string;
        sort_direction?: string;
    };
    onEdit: (client: Client) => void;
    onDelete: (client: Client) => void;
}

export default function ClientsTable({
    clients,
    filters,
    onEdit,
    onDelete
}: ClientsTableProps) {
    const { __ } = useLang();

    const handleSort = (field: string) => {
        router.get(route('clients.index'), {
            sort_field: field,
            sort_direction: filters.sort_field === field && filters.sort_direction === 'asc' ? 'desc' : 'asc',
            search: filters.search
        }, {
            preserveState: true,
            preserveScroll: true,
            only: ['clients', 'filters']
        });
    };

    return (
        <div className="rounded-md border">
            <div className="relative w-full overflow-auto">
                <table className="w-full caption-bottom text-sm">
                    <thead className="[&_tr]:border-b">
                        <tr className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                <Button
                                    variant="ghost"
                                    className="p-0 font-medium text-muted-foreground hover:text-foreground"
                                    onClick={() => handleSort('name')}
                                >
                                    {__('clients.table.name')}
                                    {filters.sort_field === 'name' && (
                                        <span className="ml-2">
                                            {filters.sort_direction === 'asc' ? '↑' : '↓'}
                                        </span>
                                    )}
                                </Button>
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                <Button
                                    variant="ghost"
                                    className="p-0 font-medium text-muted-foreground hover:text-foreground"
                                    onClick={() => handleSort('identification_number')}
                                >
                                    {__('clients.table.id_number')}
                                    {filters.sort_field === 'identification_number' && (
                                        <span className="ml-2">
                                            {filters.sort_direction === 'asc' ? '↑' : '↓'}
                                        </span>
                                    )}
                                </Button>
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                <Button
                                    variant="ghost"
                                    className="p-0 font-medium text-muted-foreground hover:text-foreground"
                                    onClick={() => handleSort('city')}
                                >
                                    {__('clients.table.city')}
                                    {filters.sort_field === 'city' && (
                                        <span className="ml-2">
                                            {filters.sort_direction === 'asc' ? '↑' : '↓'}
                                        </span>
                                    )}
                                </Button>
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                {__('clients.table.contact')}
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                {__('clients.table.actions')}
                            </th>
                        </tr>
                    </thead>
                    <tbody className="[&_tr:last-child]:border-0">
                        {clients.length === 0 ? (
                            <tr>
                                <td colSpan={5} className="p-4 text-center text-muted-foreground">
                                    {__('clients.no_clients')}
                                </td>
                            </tr>
                        ) : (
                            clients.map((client) => (
                                <tr key={client.id} className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <td className="p-4 align-middle">{client.name}</td>
                                    <td className="p-4 align-middle">{client.identification_number}</td>
                                    <td className="p-4 align-middle">{client.city}</td>
                                    <td className="p-4 align-middle">
                                        {client.contact_email && (
                                            <a href={`mailto:${client.contact_email}`} className="text-blue-500 hover:underline">
                                                {client.contact_email}
                                            </a>
                                        )}
                                        {client.contact_phone && !client.contact_email && client.contact_phone}
                                    </td>
                                    <td className="p-4 align-middle">
                                        <div className="flex gap-2">
                                            <Button variant="outline" size="sm" onClick={() => onEdit(client)}>
                                                {__('clients.buttons.edit')}
                                            </Button>
                                            <Button variant="destructive" size="sm" onClick={() => onDelete(client)}>
                                                {__('clients.buttons.delete')}
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
