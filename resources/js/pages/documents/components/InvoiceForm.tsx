import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type Client, type Company, type Invoice } from '@/types';
import { useForm, router, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { useLang } from '@/hooks/useLang';
import ClientSelect from './ClientSelect';
import { Textarea } from '@/components/ui/textarea';
import { format } from 'date-fns';
import { Loader2 } from 'lucide-react';

interface InvoiceFormProps {
    isOpen: boolean;
    onOpenChange: (open: boolean) => void;
    isEditing: boolean;
    editingInvoice: Invoice | null;
    company: Company;
    companies: Company[];
}

export default function InvoiceForm({
    isOpen,
    onOpenChange,
    isEditing,
    editingInvoice,
    company,
    companies,
}: InvoiceFormProps) {
    const { __ } = useLang();
    const { props } = usePage();
    const [selectedClient, setSelectedClient] = useState<Client | null>(null);
    const [selectedCompanyUuid, setSelectedCompanyUuid] = useState<string>(company?.uuid || '');
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Check for flash error message from backend
    useEffect(() => {
        if (props.flash?.error) {
            setErrorMessage(props.flash.error);
        }
    }, [props.flash?.error]);

    // Update company when it changes
    useEffect(() => {
        if (company?.uuid) {
            setSelectedCompanyUuid(company.uuid);
        }
    }, [company?.uuid]);

    // Form state
    const { data, setData, post, put, processing, errors, reset } = useForm({
        number: '',
        billed_date: format(new Date(), 'yyyy-MM-dd'),
        due_date: format(new Date(Date.now() + 14 * 24 * 60 * 60 * 1000), 'yyyy-MM-dd'), // 14 days from now
        send_date: format(new Date(), 'yyyy-MM-dd'),
        variable_symbol: '',
        constant_symbol: '',
        specific_symbol: '',
        order_id: '',
        billed_client_id: '',
        payment: 'BANK',
        note: '',
        items: [{ name: '', quantity: 1, price: 0, tax: 20 }],
        totalPrice: 0,
        currency_3_code: 'EUR',
        language_2_code: 'en',
    });

    // Reset form when dialog opens/closes or editing state changes
    useEffect(() => {
        if (!isOpen) {
            reset();
            setSelectedClient(null);
        } else if (isEditing && editingInvoice) {
            // For editing, extract client ID from the billed_to_client object
            const billedClient = editingInvoice.billed_to_client;
            const clientId = billedClient?.id ? String(billedClient.id) : '';

            setData({
                number: editingInvoice.number,
                billed_date: format(new Date(editingInvoice.billed_date), 'yyyy-MM-dd'),
                due_date: format(new Date(editingInvoice.due_date), 'yyyy-MM-dd'),
                send_date: format(new Date(editingInvoice.send_date), 'yyyy-MM-dd'),
                variable_symbol: editingInvoice.variable_symbol || '',
                constant_symbol: editingInvoice.constant_symbol || '',
                specific_symbol: editingInvoice.specific_symbol || '',
                order_id: editingInvoice.order_id || '',
                billed_client_id: clientId,
                payment: editingInvoice.payment,
                note: editingInvoice.note || '',
                items: editingInvoice.items || [{ name: '', quantity: 1, price: 0, tax: 20 }],
                totalPrice: editingInvoice.totalPrice || 0,
                currency_3_code: editingInvoice.currency_3_code,
                language_2_code: editingInvoice.language_2_code,
            });
        } else {
            // For new invoice, set default values
            reset();
            setData({
                number: `${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}-001`,
                billed_date: format(new Date(), 'yyyy-MM-dd'),
                due_date: format(new Date(Date.now() + 14 * 24 * 60 * 60 * 1000), 'yyyy-MM-dd'), // 14 days from now
                send_date: format(new Date(), 'yyyy-MM-dd'),
                variable_symbol: '',
                constant_symbol: '',
                specific_symbol: '',
                order_id: '',
                billed_client_id: '',
                payment: 'BANK',
                note: '',
                items: [{ name: '', quantity: 1, price: 0, tax: 20 }],
                totalPrice: 0,
                currency_3_code: 'EUR',
                language_2_code: 'en',
            });
            setSelectedClient(null);
        }
    }, [isOpen, isEditing, editingInvoice, reset, setData]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        setIsSubmitting(true);

        // Prepare data exactly as per CreateInvoiceRequest validation rules
        const submitData = {
            number: data.number,
            billed_date: data.billed_date,
            due_date: data.due_date,
            send_date: data.send_date,
            variable_symbol: data.variable_symbol || '',
            constant_symbol: data.constant_symbol || '',
            specific_symbol: data.specific_symbol || '',
            order_id: data.order_id || '',
            billed_client_id: data.billed_client_id ? Number(data.billed_client_id) : '',
            items: data.items,
            payment: data.payment,
            cash_payment_rounding: 0,
            bank_transfer: {},
            note: data.note || '',
            totalPrice: Number(data.totalPrice),
            currency_3_code: data.currency_3_code,
            language_2_code: data.language_2_code,
        };

        if (isEditing && editingInvoice) {
            router.put(route('documents.invoices.update', { uuid: editingInvoice.uuid, companyUuid: selectedCompanyUuid }), submitData, {
                onSuccess: () => {
                    setIsSubmitting(false);
                    onOpenChange(false);
                },
                onError: (errors: any) => {
                    setIsSubmitting(false);
                    const errorMsg = errors?.message || 
                        Object.values(errors || {}).flat().join(', ') || 
                        __('common.error_occurred');
                    setErrorMessage(String(errorMsg));
                }
            });
        } else {
            router.post(route('documents.invoices.store', { companyUuid: selectedCompanyUuid }), submitData, {
                onSuccess: () => {
                    setIsSubmitting(false);
                    onOpenChange(false);
                },
                onError: (errors: any) => {
                    setIsSubmitting(false);
                    const errorMsg = errors?.message || 
                        Object.values(errors || {}).flat().join(', ') || 
                        __('common.error_occurred');
                    setErrorMessage(String(errorMsg));
                }
            });
        }
    };

    const handleClientChange = (clientId: string, client: Client | null) => {
        setData('billed_client_id', clientId);
        setSelectedClient(client);
    };

    // Add a new item to the invoice
    const addItem = () => {
        setData('items', [
            ...data.items,
            { name: '', quantity: 1, price: 0, tax: 20 }
        ]);
    };

    // Remove an item from the invoice
    const removeItem = (index: number) => {
        if (data.items.length <= 1) return; // Keep at least one item

        const newItems = [...data.items];
        newItems.splice(index, 1);
        setData('items', newItems);
    };

    // Update an item in the invoice
    const updateItem = (index: number, field: string, value: any) => {
        const newItems = [...data.items];
        newItems[index] = { ...newItems[index], [field]: value };
        setData('items', newItems);
    };

    // Calculate totals
    const calculateTotals = () => {
        let totalPrice = 0;
        let totalTax = 0;

        data.items.forEach(item => {
            const price = parseFloat(item?.price?.toString() || '0') || 0;
            const quantity = parseFloat(item?.quantity?.toString() || '1') || 1;
            const tax = parseFloat(item?.tax?.toString() || '0') || 0;
            
            const itemPrice = price * quantity;
            const itemTax = itemPrice * (tax / 100);

            totalPrice += itemPrice;
            totalTax += itemTax;
        });

        return {
            totalPrice,
            totalTax,
            totalWithTax: totalPrice + totalTax
        };
    };

    const totals = calculateTotals();

    // Update totalPrice in form data whenever items change
    useEffect(() => {
        setData('totalPrice', totals.totalPrice);
    }, [totals.totalPrice, setData]);

    return (
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[800px] max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{isEditing ? __('invoices.edit_invoice') : __('invoices.form.title_add')}</DialogTitle>
                    <DialogDescription>
                        {isEditing
                            ? __('invoices.form.description_edit')
                            : __('invoices.form.description_add')}
                    </DialogDescription>
                </DialogHeader>

                {/* Error Alert */}
                {errorMessage && (
                    <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative mb-4">
                        <strong className="font-bold">Error!</strong>
                        <span className="block sm:inline ml-2">{errorMessage}</span>
                        <button
                            onClick={() => setErrorMessage(null)}
                            className="absolute top-0 bottom-0 right-0 px-4 py-3"
                        >
                            <span className="text-2xl">&times;</span>
                        </button>
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="company">{__('common.company')} *</Label>
                            <select
                                id="company"
                                value={selectedCompanyUuid}
                                onChange={(e) => setSelectedCompanyUuid(e.target.value)}
                                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                                <option value="">Select a company</option>
                                {companies && companies.map((comp) => (
                                    <option key={comp.uuid} value={comp.uuid}>
                                        {comp.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="grid gap-2">
                                <Label htmlFor="number">{__('invoices.form.number')} *</Label>
                                <Input
                                    id="number"
                                    value={data.number}
                                    onChange={(e) => setData('number', e.target.value)}
                                    required
                                />
                                {errors.number && <p className="text-sm text-red-500">{errors.number}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="client_id">{__('invoices.form.client')} *</Label>
                                <ClientSelect
                                    value={data.billed_client_id}
                                    onChange={handleClientChange}
                                    placeholder={__('invoices.form.select_client')}
                                />
                                {errors.billed_client_id && <p className="text-sm text-red-500">{errors.billed_client_id}</p>}
                            </div>
                        </div>

                        <div className="grid grid-cols-3 gap-4">
                            <div className="grid gap-2">
                                <Label htmlFor="billed_date">{__('invoices.form.billed_date')} *</Label>
                                <Input
                                    id="billed_date"
                                    type="date"
                                    value={data.billed_date}
                                    onChange={(e) => setData('billed_date', e.target.value)}
                                    required
                                />
                                {errors.billed_date && <p className="text-sm text-red-500">{errors.billed_date}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="due_date">{__('invoices.form.due_date')} *</Label>
                                <Input
                                    id="due_date"
                                    type="date"
                                    value={data.due_date}
                                    onChange={(e) => setData('due_date', e.target.value)}
                                    required
                                />
                                {errors.due_date && <p className="text-sm text-red-500">{errors.due_date}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="send_date">{__('invoices.form.send_date')} *</Label>
                                <Input
                                    id="send_date"
                                    type="date"
                                    value={data.send_date}
                                    onChange={(e) => setData('send_date', e.target.value)}
                                    required
                                />
                                {errors.send_date && <p className="text-sm text-red-500">{errors.send_date}</p>}
                            </div>
                        </div>

                        <div className="grid grid-cols-3 gap-4">
                            <div className="grid gap-2">
                                <Label htmlFor="variable_symbol">{__('invoices.form.variable_symbol')}</Label>
                                <Input
                                    id="variable_symbol"
                                    value={data.variable_symbol}
                                    onChange={(e) => setData('variable_symbol', e.target.value)}
                                />
                                {errors.variable_symbol && <p className="text-sm text-red-500">{errors.variable_symbol}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="constant_symbol">{__('invoices.form.constant_symbol')}</Label>
                                <Input
                                    id="constant_symbol"
                                    value={data.constant_symbol}
                                    onChange={(e) => setData('constant_symbol', e.target.value)}
                                />
                                {errors.constant_symbol && <p className="text-sm text-red-500">{errors.constant_symbol}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="specific_symbol">{__('invoices.form.specific_symbol')}</Label>
                                <Input
                                    id="specific_symbol"
                                    value={data.specific_symbol}
                                    onChange={(e) => setData('specific_symbol', e.target.value)}
                                />
                                {errors.specific_symbol && <p className="text-sm text-red-500">{errors.specific_symbol}</p>}
                            </div>
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="order_id">{__('invoices.form.order_id')}</Label>
                            <Input
                                id="order_id"
                                value={data.order_id}
                                onChange={(e) => setData('order_id', e.target.value)}
                            />
                            {errors.order_id && <p className="text-sm text-red-500">{errors.order_id}</p>}
                        </div>

                        <div className="grid gap-2">
                            <Label>{__('invoices.form.items')} *</Label>
                            <div className="rounded-md border p-4">
                                <div className="grid grid-cols-12 gap-2 mb-2 font-medium">
                                    <div className="col-span-5">{__('invoices.form.item_name')}</div>
                                    <div className="col-span-2">{__('invoices.form.quantity')}</div>
                                    <div className="col-span-2">{__('invoices.form.price')}</div>
                                    <div className="col-span-2">{__('invoices.form.tax')}</div>
                                    <div className="col-span-1"></div>
                                </div>

                                {data.items.map((item, index) => (
                                    <div key={index} className="grid grid-cols-12 gap-2 mb-2">
                                        <div className="col-span-5">
                                            <Input
                                                value={item.name}
                                                onChange={(e) => updateItem(index, 'name', e.target.value)}
                                                placeholder={__('invoices.form.item_name_placeholder')}
                                                required
                                            />
                                        </div>
                                        <div className="col-span-2">
                                            <Input
                                                type="number"
                                                min="1"
                                                step="1"
                                                value={item.quantity}
                                                onChange={(e) => updateItem(index, 'quantity', e.target.value)}
                                                required
                                            />
                                        </div>
                                        <div className="col-span-2">
                                            <Input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                value={item.price}
                                                onChange={(e) => updateItem(index, 'price', e.target.value)}
                                                required
                                            />
                                        </div>
                                        <div className="col-span-2">
                                            <Input
                                                type="number"
                                                min="0"
                                                step="1"
                                                value={item.tax}
                                                onChange={(e) => updateItem(index, 'tax', e.target.value)}
                                                required
                                            />
                                        </div>
                                        <div className="col-span-1">
                                            <Button
                                                type="button"
                                                variant="outline"
                                                size="icon"
                                                onClick={() => removeItem(index)}
                                                disabled={data.items.length <= 1}
                                            >
                                                &times;
                                            </Button>
                                        </div>
                                    </div>
                                ))}

                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={addItem}
                                    className="w-full mt-2"
                                >
                                    {__('invoices.form.add_item')}
                                </Button>

                                <div className="mt-4 border-t pt-4">
                                    <div className="flex justify-between">
                                        <span>{__('invoices.form.total_price')}:</span>
                                        <span>{totals.totalPrice.toFixed(2)} {data.currency_3_code}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span>{__('invoices.form.total_tax')}:</span>
                                        <span>{totals.totalTax.toFixed(2)} {data.currency_3_code}</span>
                                    </div>
                                    <div className="flex justify-between font-bold">
                                        <span>{__('invoices.form.total_with_tax')}:</span>
                                        <span>{totals.totalWithTax.toFixed(2)} {data.currency_3_code}</span>
                                    </div>
                                </div>
                            </div>
                            {errors.items && <p className="text-sm text-red-500">{errors.items}</p>}
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="payment">Payment Method *</Label>
                            <select
                                id="payment"
                                value={data.payment}
                                onChange={(e) => setData('payment', e.target.value)}
                                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                                <option value="BANK">Bank</option>
                                <option value="CASH">Cash</option>
                            </select>
                            {errors.payment && <p className="text-sm text-red-500">{errors.payment}</p>}
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="note">{__('invoices.form.note')}</Label>
                            <Textarea
                                id="note"
                                value={data.note}
                                onChange={(e) => setData('note', e.target.value)}
                                rows={3}
                            />
                            {errors.note && <p className="text-sm text-red-500">{errors.note}</p>}
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="submit" disabled={isSubmitting || processing}>
                            {isSubmitting || processing ? (
                                <>
                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                    {__('common.processing')}
                                </>
                            ) : isEditing ? (
                                __('invoices.form.submit_edit')
                            ) : (
                                __('invoices.form.submit_add')
                            )}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
