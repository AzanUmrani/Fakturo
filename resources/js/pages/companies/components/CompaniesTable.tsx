import { Button } from '@/components/ui/button';
import { type Company } from '@/types';
import { router } from '@inertiajs/react';
import { useLang } from '@/hooks/useLang';

interface CompaniesTableProps {
    companies: Company[];
    filters: {
        search?: string;
        sort_field?: string;
        sort_direction?: string;
    };
    onEdit: (company: Company) => void;
    onDelete: (company: Company) => void;
}

export default function CompaniesTable({
    companies,
    filters,
    onEdit,
    onDelete
}: CompaniesTableProps) {
    const { __ } = useLang();

    const handleSort = (field: string) => {
        router.get(route('companies.index'), {
            sort_field: field,
            sort_direction: filters.sort_field === field && filters.sort_direction === 'asc' ? 'desc' : 'asc',
            search: filters.search
        }, {
            preserveState: true,
            preserveScroll: true,
            only: ['companies', 'filters']
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
                                    {__('companies.table.name')}
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
                                    {__('companies.table.id_number')}
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
                                    {__('companies.table.city')}
                                    {filters.sort_field === 'city' && (
                                        <span className="ml-2">
                                            {filters.sort_direction === 'asc' ? '↑' : '↓'}
                                        </span>
                                    )}
                                </Button>
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                {__('companies.table.contact')}
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                {__('companies.table.actions')}
                            </th>
                        </tr>
                    </thead>
                    <tbody className="[&_tr:last-child]:border-0">
                        {companies.length === 0 ? (
                            <tr>
                                <td colSpan={5} className="p-4 text-center text-muted-foreground">
                                    {__('companies.no_companies')}
                                </td>
                            </tr>
                        ) : (
                            companies.map((company) => (
                                <tr key={company.id} className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <td className="p-4 align-middle">{company.name}</td>
                                    <td className="p-4 align-middle">{company.identification_number}</td>
                                    <td className="p-4 align-middle">{company.city}</td>
                                    <td className="p-4 align-middle">
                                        {company.contact_email && (
                                            <a href={`mailto:${company.contact_email}`} className="text-blue-500 hover:underline">
                                                {company.contact_email}
                                            </a>
                                        )}
                                        {company.contact_phone && !company.contact_email && company.contact_phone}
                                    </td>
                                    <td className="p-4 align-middle">
                                        <div className="flex gap-2">
                                            <Button variant="outline" size="sm" onClick={() => onEdit(company)}>
                                                {__('companies.buttons.edit')}
                                            </Button>
                                            <Button variant="destructive" size="sm" onClick={() => onDelete(company)}>
                                                {__('companies.buttons.delete')}
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
