import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useLang } from '@/hooks/useLang';
import { Plus, Search } from 'lucide-react';

interface PreInvoiceSearchProps {
    searchTerm: string;
    onSearchChange: (value: string) => void;
    onAddClick: () => void;
}

export default function PreInvoiceSearch({
    searchTerm,
    onSearchChange,
    onAddClick,
}: PreInvoiceSearchProps) {
    const { __ } = useLang();

    return (
        <div className="flex flex-col sm:flex-row justify-between gap-4">
            <div className="relative flex-1">
                <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input
                    type="search"
                    placeholder="Search pre invoices..."
                    className="pl-8"
                    value={searchTerm}
                    onChange={(e) => onSearchChange(e.target.value)}
                />
            </div>
            <Button onClick={onAddClick}>
                <Plus className="mr-2 h-4 w-4" />
                Add Pre Invoice
            </Button>
        </div>
    );
}
