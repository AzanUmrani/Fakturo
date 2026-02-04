import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type PreInvoice, type Client, type Company } from '../preinvoices';
import { useForm, usePage, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { Textarea } from '@/components/ui/textarea';
import { format } from 'date-fns';
import { Loader2 } from 'lucide-react';
import ClientSelect from '../components/ClientSelect';

interface PreInvoiceFormProps {
    isOpen: boolean;
    onOpenChange: (open: boolean) => void;
    isEditing: boolean;
    editingInvoice: PreInvoice | null;
    companyUuid: string;
    companies: Company[];
}

export default function PreInvoiceForm({
    isOpen,
    onOpenChange,
    isEditing,
    editingInvoice,
    companyUuid: initialCompanyUuid,
    companies,
}: PreInvoiceFormProps) {
    const { props } = usePage();
    const [selectedClient, setSelectedClient] = useState<Client | null>(null);
    const [selectedCompanyUuid, setSelectedCompanyUuid] = useState<string>(initialCompanyUuid);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Get CSRF token from multiple sources
    const getCsrfToken = (): string => {
        // Try from meta tag first
        let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) return token;
        
        // Try from Inertia props
        if (props && typeof props === 'object' && 'csrf_token' in props) {
            return (props as any).csrf_token as string;
        }
        
        // Try from cookie
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.split('=');
            if (name.trim() === 'XSRF-TOKEN') {
                return decodeURIComponent(value);
            }
        }
        
        return '';
    };

    // Check for flash error message from backend
    useEffect(() => {
        if (props.flash?.error) {
            setErrorMessage(props.flash.error);
        }
    }, [props.flash?.error]);

    // Form state - ensure all fields have default values
    const { data, setData, post, put, processing, errors, reset } = useForm({
        number: '',
        billed_date: format(new Date(), 'yyyy-MM-dd'),
        due_date: format(new Date(Date.now() + 14 * 24 * 60 * 60 * 1000), 'yyyy-MM-dd'),
        send_date: format(new Date(), 'yyyy-MM-dd'),
        variable_symbol: '',
        constant_symbol: '',
        specific_symbol: '',
        order_id: '',
        billed_client_id: '',
        payment: 'CASH',
        note: '',
        items: [{ name: '', quantity: 1, price: 0, tax: 20, unit: '' }],
        totalPrice: 0,
        currency_3_code: 'EUR',
        language_2_code: 'en',
        cash_payment_rounding: 0,
        bank_transfer: {},
        template: '',
    });

    // Reset form when dialog opens/closes or editing state changes
    useEffect(() => {
        setSelectedCompanyUuid(initialCompanyUuid);
    }, [initialCompanyUuid]);

    useEffect(() => {
        if (!isOpen) {
            reset();
            setSelectedClient(null);
        } else if (isEditing && editingInvoice) {
            // Ensure items array is properly formatted
            const editItems = Array.isArray(editingInvoice.items) && editingInvoice.items.length > 0
                ? editingInvoice.items.map(item => ({
                    name: item.name || '',
                    quantity: item.quantity || 1,
                    price: item.price || 0,
                    tax: item.taxRate || item.tax || 20,
                    unit: item.unit || '',
                }))
                : [{ name: '', quantity: 1, price: 0, tax: 20, unit: '' }];
            
            setData({
                number: editingInvoice.number || '',
                billed_date: editingInvoice.billed_date ? format(new Date(editingInvoice.billed_date), 'yyyy-MM-dd') : format(new Date(), 'yyyy-MM-dd'),
                due_date: editingInvoice.due_date ? format(new Date(editingInvoice.due_date), 'yyyy-MM-dd') : format(new Date(), 'yyyy-MM-dd'),
                send_date: editingInvoice.send_date ? format(new Date(editingInvoice.send_date), 'yyyy-MM-dd') : format(new Date(), 'yyyy-MM-dd'),
                variable_symbol: editingInvoice.variable_symbol || '',
                constant_symbol: editingInvoice.constant_symbol || '',
                specific_symbol: editingInvoice.specific_symbol || '',
                order_id: editingInvoice.order_id || '',
                billed_client_id: editingInvoice.billed_to_client?.id?.toString() || '',
                payment: editingInvoice.payment || 'CASH',
                note: editingInvoice.note || '',
                items: editItems,
                totalPrice: editingInvoice.totalPrice || 0,
                currency_3_code: editingInvoice.currency_3_code || 'EUR',
                language_2_code: editingInvoice.language_2_code || 'en',
                cash_payment_rounding: editingInvoice.cash_payment_rounding || 0,
                bank_transfer: editingInvoice.bank_transfer || {},
                template: editingInvoice.template || '',
            });
            setSelectedClient(editingInvoice.billed_to_client || null);
        } else {
            reset();
            setData({
                number: `PI-${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}-001`,
                billed_date: format(new Date(), 'yyyy-MM-dd'),
                due_date: format(new Date(Date.now() + 14 * 24 * 60 * 60 * 1000), 'yyyy-MM-dd'),
                send_date: format(new Date(), 'yyyy-MM-dd'),
                variable_symbol: '',
                constant_symbol: '',
                specific_symbol: '',
                order_id: '',
                billed_client_id: '',
                payment: 'CASH',
                note: '',
                items: [{ name: '', quantity: 1, price: 0, tax: 20, unit: '' }],
                totalPrice: 0,
                currency_3_code: 'EUR',
                language_2_code: 'en',
                cash_payment_rounding: 0,
                bank_transfer: {},
                template: '',
            });
            setSelectedClient(null);
        }
    }, [isOpen, isEditing, editingInvoice, reset, setData]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        setIsSubmitting(true);

        // Format items correctly - convert to proper types and add taxRate field
        const formattedItems = data.items.map(item => ({
            name: item.name,
            quantity: Number(item.quantity) || 1,
            price: Number(item.price) || 0,
            unit: item.unit || '',
            taxRate: Number(item.tax) || 0,
        }));

        const submitData = {
            number: data.number,
            billed_date: data.billed_date,
            due_date: data.due_date,
            send_date: data.send_date,
            variable_symbol: data.variable_symbol,
            constant_symbol: data.constant_symbol,
            specific_symbol: data.specific_symbol,
            order_id: data.order_id,
            billed_client_id: Number(data.billed_client_id),
            items: formattedItems,
            payment: data.payment,
            note: data.note,
            totalPrice: totals.totalPrice,
            currency_3_code: data.currency_3_code,
            language_2_code: data.language_2_code,
            cash_payment_rounding: Number(data.cash_payment_rounding) || 0,
            bank_transfer: data.bank_transfer,
            template: data.template,
        };

        if (isEditing && editingInvoice) {
            const csrfToken = getCsrfToken();
            const headers: HeadersInit = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            };
            
            // Add CSRF token if available
            if (csrfToken) {
                (headers as any)['X-CSRF-TOKEN'] = csrfToken;
            }

            fetch(`/api/user/company/${selectedCompanyUuid}/preinvoice/${editingInvoice.uuid}`, {
                method: 'POST',
                headers,
                credentials: 'include',
                body: JSON.stringify(submitData),
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(() => {
                    setIsSubmitting(false);
                    onOpenChange(false);
                    // Refresh only the preInvoices data without full page reload
                    router.reload({ only: ['preInvoices'] });
                })
                .catch((error) => {
                    setIsSubmitting(false);
                    let errorMsg = 'An error occurred';
                    
                    if (error?.message) {
                        errorMsg = error.message;
                    } else if (error?.error) {
                        errorMsg = error.error;
                    } else if (typeof error === 'string') {
                        errorMsg = error;
                    } else if (error && typeof error === 'object') {
                        errorMsg = JSON.stringify(error);
                    }
                    
                    setErrorMessage(errorMsg);
                });
        } else {
            const csrfToken = getCsrfToken();
            const headers: HeadersInit = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            };
            
            // Add CSRF token if available
            if (csrfToken) {
                (headers as any)['X-CSRF-TOKEN'] = csrfToken;
            }

            fetch(`/api/user/company/${selectedCompanyUuid}/preinvoice`, {
                method: 'POST',
                headers,
                credentials: 'include',
                body: JSON.stringify(submitData),
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(() => {
                    setIsSubmitting(false);
                    onOpenChange(false);
                    // Refresh only the preInvoices data without full page reload
                    router.reload({ only: ['preInvoices'] });
                })
                .catch((error) => {
                    setIsSubmitting(false);
                    let errorMsg = 'An error occurred';
                    
                    if (error?.message) {
                        errorMsg = error.message;
                    } else if (error?.error) {
                        errorMsg = error.error;
                    } else if (typeof error === 'string') {
                        errorMsg = error;
                    } else if (error && typeof error === 'object') {
                        errorMsg = JSON.stringify(error);
                    }
                    
                    setErrorMessage(errorMsg);
                });
        }
    };

    const handleClientChange = (clientId: string, client: Client | null) => {
        setData('billed_client_id', clientId);
        setSelectedClient(client);
        // Auto-set currency from client if available
        if (client && client.currency_3_code) {
            setData('currency_3_code', client.currency_3_code);
        } else {
            setData('currency_3_code', 'EUR'); // Default to EUR
        }
    };

    // Add a new item to the pre-invoice
    const addItem = () => {
        setData('items', [
            ...data.items,
            { name: '', quantity: 1, price: 0, tax: 20, unit: '' }
        ]);
    };

    // Remove an item from the pre-invoice
    const removeItem = (index: number) => {
        if (data.items.length <= 1) return;

        const newItems = [...data.items];
        newItems.splice(index, 1);
        setData('items', newItems);
    };

    // Update an item in the pre-invoice
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
            totalWithTax: totalPrice + totalTax,
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
                    <DialogTitle>{isEditing ? 'Edit Pre-Invoice' : 'Add New Pre-Invoice'}</DialogTitle>
                    <DialogDescription>
                        {isEditing
                            ? 'Update the pre-invoice information below.'
                            : 'Fill in the pre-invoice information below to create a new pre-invoice.'}
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
                            <Label htmlFor="company">Company *</Label>
                            <select
                                id="company"
                                value={selectedCompanyUuid}
                                onChange={(e) => setSelectedCompanyUuid(e.target.value)}
                                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                                <option value="">Select a company</option>
                                {companies.map((company) => (
                                    <option key={company.uuid} value={company.uuid}>
                                        {company.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="grid gap-2">
                                <Label htmlFor="number">Pre-Invoice Number *</Label>
                                <Input
                                    id="number"
                                    value={data.number}
                                    onChange={(e) => setData('number', e.target.value)}
                                    required
                                />
                                {errors.number && <p className="text-sm text-red-500">{errors.number}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="client_id">Client *</Label>
                                <ClientSelect
                                    value={data.billed_client_id}
                                    onChange={handleClientChange}
                                    placeholder="Select Client"
                                />
                                {errors.billed_client_id && <p className="text-sm text-red-500">{errors.billed_client_id}</p>}
                            </div>
                        </div>

                        <div className="grid grid-cols-3 gap-4">
                            <div className="grid gap-2">
                                <Label htmlFor="billed_date">Billed Date *</Label>
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
                                <Label htmlFor="due_date">Due Date *</Label>
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
                                <Label htmlFor="send_date">Send Date *</Label>
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
                                <Label htmlFor="variable_symbol">Variable Symbol</Label>
                                <Input
                                    id="variable_symbol"
                                    value={data.variable_symbol}
                                    onChange={(e) => setData('variable_symbol', e.target.value)}
                                />
                                {errors.variable_symbol && <p className="text-sm text-red-500">{errors.variable_symbol}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="constant_symbol">Constant Symbol</Label>
                                <Input
                                    id="constant_symbol"
                                    value={data.constant_symbol}
                                    onChange={(e) => setData('constant_symbol', e.target.value)}
                                />
                                {errors.constant_symbol && <p className="text-sm text-red-500">{errors.constant_symbol}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="specific_symbol">Specific Symbol</Label>
                                <Input
                                    id="specific_symbol"
                                    value={data.specific_symbol}
                                    onChange={(e) => setData('specific_symbol', e.target.value)}
                                />
                                {errors.specific_symbol && <p className="text-sm text-red-500">{errors.specific_symbol}</p>}
                            </div>
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="order_id">Order ID</Label>
                            <Input
                                id="order_id"
                                value={data.order_id}
                                onChange={(e) => setData('order_id', e.target.value)}
                            />
                            {errors.order_id && <p className="text-sm text-red-500">{errors.order_id}</p>}
                        </div>

                        <div className="grid gap-2">
                            <Label>Pre-Invoice Items *</Label>
                            <div className="rounded-md border p-4">
                                <div className="grid grid-cols-12 gap-2 mb-2 font-medium">
                                    <div className="col-span-5">Item Name</div>
                                    <div className="col-span-2">Quantity</div>
                                    <div className="col-span-2">Price</div>
                                    <div className="col-span-2">Tax (%)</div>
                                    <div className="col-span-1"></div>
                                </div>

                                {data.items.map((item, index) => (
                                    <div key={index} className="grid grid-cols-12 gap-2 mb-2">
                                        <div className="col-span-5">
                                            <Input
                                                value={item.name}
                                                onChange={(e) => updateItem(index, 'name', e.target.value)}
                                                placeholder="Enter item name"
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
                                    Add Item
                                </Button>

                                <div className="mt-4 border-t pt-4 space-y-2">
                                    <div className="flex justify-between">
                                        <span>Total Price:</span>
                                        <span>{totals.totalPrice.toFixed(2)} {data.currency_3_code}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span>Total Tax:</span>
                                        <span>{totals.totalTax.toFixed(2)} {data.currency_3_code}</span>
                                    </div>
                                    <div className="flex justify-between font-bold border-t pt-2">
                                        <span>Total with Tax:</span>
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
                                <option value="CASH">Cash</option>
                                <option value="BANK">Bank Transfer</option>
                            </select>
                            {errors.payment && <p className="text-sm text-red-500">{errors.payment}</p>}
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="note">Note</Label>
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
                                    Processing
                                </>
                            ) : isEditing ? (
                                'Update Pre-Invoice'
                            ) : (
                                'Create Pre-Invoice'
                            )}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
