import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { useLang } from '@/hooks/useLang';
import { type Client } from '@/types';
import { Check, ChevronsUpDown, Loader2 } from 'lucide-react';
import { useState, useEffect } from 'react';
import { cn } from '@/lib/utils';
import { router } from '@inertiajs/react';

interface ClientSelectProps {
    value: string;
    onChange: (value: string, client: Client | null) => void;
    placeholder?: string;
    disabled?: boolean;
}

export default function ClientSelect({
    value,
    onChange,
    placeholder,
    disabled = false,
}: ClientSelectProps) {
    const { __ } = useLang();
    const [open, setOpen] = useState(false);
    const [clients, setClients] = useState<Client[]>([]);
    const [loading, setLoading] = useState(false);
    const [searchTerm, setSearchTerm] = useState('');

    // Load clients when the component mounts or when search term changes
    useEffect(() => {
        const fetchClients = async () => {
            setLoading(true);
            try {
                const response = await fetch(
                    route('documents.clients') +
                    (searchTerm ? `?search=${encodeURIComponent(searchTerm)}` : '')
                );

                if (response.ok) {
                    const data = await response.json();
                    console.log('Fetched clients:', data.clients);
                    setClients(data.clients || []);
                }
            } catch (error) {
                console.error('Error fetching clients:', error);
            } finally {
                setLoading(false);
            }
        };

        // Debounce search
        const timeoutId = setTimeout(() => {
            fetchClients();
        }, 300);

        return () => clearTimeout(timeoutId);
    }, [searchTerm]);

    // Find the selected client
    const selectedClient = clients.find(client => client.id.toString() === value);

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    role="combobox"
                    aria-expanded={open}
                    className="w-full justify-between"
                    disabled={disabled}
                >
                    {value && selectedClient
                        ? selectedClient.name
                        : placeholder || __('invoices.form.select_client')}
                    <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-[400px] p-0">
                <Command>
                    <CommandInput
                        placeholder={__('invoices.form.search_clients')}
                        value={searchTerm}
                        onValueChange={setSearchTerm}
                    />
                    {loading && (
                        <div className="flex items-center justify-center py-6">
                            <Loader2 className="h-6 w-6 animate-spin text-muted-foreground" />
                        </div>
                    )}
                    {!loading && (
                        <CommandList>
                            <CommandEmpty>{__('invoices.form.no_clients_found')}</CommandEmpty>
                            <CommandGroup>
                                {clients.map((client) => (
                                    <CommandItem
                                        key={client.id}
                                        value={`${client.name} ${client.identification_number} ${client.contact_email || ''}`}
                                        onSelect={() => {
                                            onChange(client.id.toString(), client);
                                            setOpen(false);
                                        }}
                                        className="flex flex-col items-start"
                                    >
                                        <div className="flex w-full items-center">
                                            <Check
                                                className={cn(
                                                    "mr-2 h-4 w-4",
                                                    value === client.id.toString() ? "opacity-100" : "opacity-0"
                                                )}
                                            />
                                            <span className="font-medium">{client.name}</span>
                                        </div>
                                        <div className="ml-6 mt-1 text-xs text-muted-foreground">
                                            <div>{client.street}, {client.zip} {client.city}</div>
                                            <div>
                                                {__('clients.form.identification_number')}: {client.identification_number}
                                                {client.vat_identification_number && `, ${__('clients.form.vat_identification_number')}: ${client.vat_identification_number}`}
                                            </div>
                                        </div>
                                    </CommandItem>
                                ))}
                            </CommandGroup>
                        </CommandList>
                    )}
                </Command>
            </PopoverContent>
        </Popover>
    );
}
