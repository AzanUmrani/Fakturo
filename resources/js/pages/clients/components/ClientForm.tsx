import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { StateSelect } from '@/components/state-select';
import { type Client } from '@/types';
import { useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { useLang } from '@/hooks/useLang';

interface ClientFormProps {
    isOpen: boolean;
    onOpenChange: (open: boolean) => void;
    isEditing: boolean;
    editingClient: Client | null;
}

export default function ClientForm({
    isOpen,
    onOpenChange,
    isEditing,
    editingClient
}: ClientFormProps) {
    const { __ } = useLang();

    // Autofill states
    const [isAutofilling, setIsAutofilling] = useState(false);
    const [autofillError, setAutofillError] = useState<string | null>(null);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
        state: '',
        street: '',
        street_extra: '',
        zip: '',
        city: '',
        identification_number: '',
        vat_identification_number: '',
        vat_identification_number_sk: '',
        registry_info: '',
        contact_name: '',
        contact_phone: '',
        contact_email: '',
        contact_web: '',
    });

    // Reset form when dialog opens/closes or editing state changes
    useEffect(() => {
        if (!isOpen) {
            reset();
            setAutofillError(null);
        } else if (isEditing && editingClient) {
            setData({
                name: editingClient.name,
                state: editingClient.state,
                street: editingClient.street,
                street_extra: editingClient.street_extra || '',
                zip: editingClient.zip,
                city: editingClient.city,
                identification_number: editingClient.identification_number,
                vat_identification_number: editingClient.vat_identification_number,
                vat_identification_number_sk: editingClient.vat_identification_number_sk || '',
                registry_info: editingClient.registry_info || '',
                contact_name: editingClient.contact_name || '',
                contact_phone: editingClient.contact_phone || '',
                contact_email: editingClient.contact_email || '',
                contact_web: editingClient.contact_web || '',
            });
        }
    }, [isOpen, isEditing, editingClient, reset, setData]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (isEditing && editingClient) {
            put(route('clients.update', editingClient.id), {
                onSuccess: () => {
                    onOpenChange(false);
                }
            });
        } else {
            post(route('clients.store'), {
                onSuccess: () => {
                    onOpenChange(false);
                }
            });
        }
    };

    // Function to handle autofill
    const handleAutofill = async () => {
        // Reset error state
        setAutofillError(null);

        // Validate inputs
        if (!data.state) {
            setAutofillError('Please select a country');
            return;
        }

        if (!data.identification_number) {
            setAutofillError('Please enter a registration number');
            return;
        }

        // Set loading state
        setIsAutofilling(true);

        try {
            // Make API call using fetch
            const params = new URLSearchParams({
                country2code: data.state,
                registrationNumber: data.identification_number
            });

            const response = await fetch(`/api/autocomplete/company?${params.toString()}`);

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || 'Failed to fetch company data');
            }

            const responseData = await response.json();

            // Check if response contains data
            if (responseData && responseData.data) {
                const companyData = responseData.data;

                // Map response data to form fields
                setData({
                    ...data,
                    name: companyData.name || data.name,
                    street: companyData.address || data.street,
                    city: companyData.city || data.city,
                    zip: companyData.zip || data.zip,
                    identification_number: companyData.registrationNumber || data.identification_number,
                    vat_identification_number: companyData.taxNumber || data.vat_identification_number,
                    vat_identification_number_sk: companyData.vatNumber || data.vat_identification_number_sk,
                    registry_info: companyData.registration || data.registry_info
                });
            } else {
                setAutofillError('No data found for the provided registration number');
            }
        } catch (error) {
            console.error('Error fetching company data:', error);
            setAutofillError(
                error instanceof Error ? error.message : 'Failed to fetch company data. Please try again.'
            );
        } finally {
            setIsAutofilling(false);
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>{isEditing ? __('clients.edit_client') : __('clients.form.title_add')}</DialogTitle>
                    <DialogDescription>
                        {isEditing
                            ? __('clients.form.description_edit')
                            : __('clients.form.description_add')}
                    </DialogDescription>
                </DialogHeader>
                <div className="max-h-[60vh] overflow-y-auto pr-4">
                    <form onSubmit={handleSubmit}>
                        <div className="grid gap-4 py-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="name">{__('clients.form.name')} *</Label>
                                    <Input
                                        id="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        required
                                    />
                                    {errors.name && <p className="text-sm text-red-500">{errors.name}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="state">{__('clients.form.state')} *</Label>
                                    <StateSelect
                                        value={data.state}
                                        onChange={(value) => setData('state', value)}
                                        required
                                        placeholder={__('clients.form.state_placeholder') || 'Select a country'}
                                    />
                                    {errors.state && <p className="text-sm text-red-500">{errors.state}</p>}
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="street">Street Address *</Label>
                                <Input
                                    id="street"
                                    value={data.street}
                                    onChange={(e) => setData('street', e.target.value)}
                                    required
                                />
                                {errors.street && <p className="text-sm text-red-500">{errors.street}</p>}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="street_extra">Additional Address Info</Label>
                                <Input
                                    id="street_extra"
                                    value={data.street_extra}
                                    onChange={(e) => setData('street_extra', e.target.value)}
                                />
                                {errors.street_extra && <p className="text-sm text-red-500">{errors.street_extra}</p>}
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="zip">ZIP/Postal Code *</Label>
                                    <Input
                                        id="zip"
                                        value={data.zip}
                                        onChange={(e) => setData('zip', e.target.value)}
                                        required
                                    />
                                    {errors.zip && <p className="text-sm text-red-500">{errors.zip}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="city">City *</Label>
                                    <Input
                                        id="city"
                                        value={data.city}
                                        onChange={(e) => setData('city', e.target.value)}
                                        required
                                    />
                                    {errors.city && <p className="text-sm text-red-500">{errors.city}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="identification_number">{__('clients.form.identification_number')} *</Label>
                                    <Input
                                        id="identification_number"
                                        value={data.identification_number}
                                        onChange={(e) => setData('identification_number', e.target.value)}
                                        required
                                    />
                                    {errors.identification_number && <p className="text-sm text-red-500">{errors.identification_number}</p>}
                                    <Button
                                        type="button"
                                        variant="secondary"
                                        className="w-full"
                                        onClick={() => handleAutofill()}
                                        disabled={isAutofilling}
                                    >
                                        {isAutofilling ? (
                                            <>
                                                <svg className="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Fetching Data...
                                            </>
                                        ) : (
                                            'Autofill Form'
                                        )}
                                    </Button>
                                    {autofillError && <p className="text-sm text-red-500">{autofillError}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="vat_identification_number">VAT ID (DIC) *</Label>
                                    <Input
                                        id="vat_identification_number"
                                        value={data.vat_identification_number}
                                        onChange={(e) => setData('vat_identification_number', e.target.value)}
                                        required
                                    />
                                    {errors.vat_identification_number && <p className="text-sm text-red-500">{errors.vat_identification_number}</p>}
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="vat_identification_number_sk">VAT ID SK (ICDPH)</Label>
                                <Input
                                    id="vat_identification_number_sk"
                                    value={data.vat_identification_number_sk}
                                    onChange={(e) => setData('vat_identification_number_sk', e.target.value)}
                                />
                                {errors.vat_identification_number_sk && <p className="text-sm text-red-500">{errors.vat_identification_number_sk}</p>}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="registry_info">Registry Info</Label>
                                <Input
                                    id="registry_info"
                                    value={data.registry_info}
                                    onChange={(e) => setData('registry_info', e.target.value)}
                                />
                                {errors.registry_info && <p className="text-sm text-red-500">{errors.registry_info}</p>}
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="contact_name">Contact Name</Label>
                                    <Input
                                        id="contact_name"
                                        value={data.contact_name}
                                        onChange={(e) => setData('contact_name', e.target.value)}
                                    />
                                    {errors.contact_name && <p className="text-sm text-red-500">{errors.contact_name}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="contact_phone">Contact Phone</Label>
                                    <Input
                                        id="contact_phone"
                                        value={data.contact_phone}
                                        onChange={(e) => setData('contact_phone', e.target.value)}
                                    />
                                    {errors.contact_phone && <p className="text-sm text-red-500">{errors.contact_phone}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="contact_email">Contact Email</Label>
                                    <Input
                                        id="contact_email"
                                        type="email"
                                        value={data.contact_email}
                                        onChange={(e) => setData('contact_email', e.target.value)}
                                    />
                                    {errors.contact_email && <p className="text-sm text-red-500">{errors.contact_email}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="contact_web">Website</Label>
                                    <Input
                                        id="contact_web"
                                        value={data.contact_web}
                                        onChange={(e) => setData('contact_web', e.target.value)}
                                        placeholder="https://example.com"
                                    />
                                    {errors.contact_web && <p className="text-sm text-red-500">{errors.contact_web}</p>}
                                </div>
                            </div>
                        </div>
                        <DialogFooter>
                            <Button type="submit" disabled={processing}>
                                {isEditing ? __('clients.form.submit_edit') : __('clients.form.submit_add')}
                            </Button>
                        </DialogFooter>
                    </form>
                </div>
            </DialogContent>
        </Dialog>
    );
}
