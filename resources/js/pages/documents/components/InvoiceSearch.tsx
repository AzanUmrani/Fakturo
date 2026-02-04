import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useLang } from '@/hooks/useLang';
import { Plus, Search } from 'lucide-react';

interface InvoiceSearchProps {
    searchTerm: string;
    onSearchChange: (value: string) => void;
    onAddClick: () => void;
    disabled?: boolean;
}

export default function InvoiceSearch({
    searchTerm,
    onSearchChange,
    onAddClick,
    disabled = false
}: InvoiceSearchProps) {
    const { __ } = useLang();

    return (
        <div className="flex flex-col sm:flex-row justify-between gap-4">
            <div className="relative flex-1">
                <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input
                    type="search"
                    placeholder={__('invoices.search_placeholder')}
                    className="pl-8"
                    value={searchTerm}
                    onChange={(e) => onSearchChange(e.target.value)}
                />
            </div>
            <Button onClick={onAddClick} disabled={disabled}>
                <Plus className="mr-2 h-4 w-4" />
                {__('invoices.add_invoice')}
            </Button>
        </div>
    );
}
