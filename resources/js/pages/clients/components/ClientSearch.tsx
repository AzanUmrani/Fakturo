import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SearchIcon, PlusIcon } from 'lucide-react';
import { useLang } from '@/hooks/useLang';

interface ClientSearchProps {
    searchTerm: string;
    onSearchChange: (value: string) => void;
    onAddClick: () => void;
}

export default function ClientSearch({
    searchTerm,
    onSearchChange,
    onAddClick
}: ClientSearchProps) {
    const { __ } = useLang();

    return (
        <div className="flex justify-between items-center">
            <div className="relative">
                <SearchIcon className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    placeholder={__('clients.search_placeholder')}
                    className="pl-10 w-[250px]"
                    value={searchTerm}
                    onChange={(e) => onSearchChange(e.target.value)}
                />
            </div>
            <Button onClick={onAddClick}>
                <PlusIcon className="h-4 w-4 mr-2" />
                {__('clients.add_client')}
            </Button>
        </div>
    );
}
