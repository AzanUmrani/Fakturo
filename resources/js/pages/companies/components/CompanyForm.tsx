import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { StateSelect } from '@/components/state-select';
import { type Company } from '@/types';
import { useForm, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { useLang } from '@/hooks/useLang';
import taxTypeData from '@/../../resources/json/TaxType.json';

interface CompanyFormProps {
    isOpen: boolean;
    onOpenChange: (open: boolean) => void;
    isEditing: boolean;
    editingCompany: Company | null;
    onSuccess?: () => void;
}

interface LocaleInfo {
    current: string;
    available: Record<string, string>;
}

interface PageProps {
    locale: LocaleInfo;
}

// Helper function to get CSRF token from meta tag or cookie
const getCsrfToken = (): string => {
    // Try to get from meta tag first (Laravel's standard approach)
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (metaToken) {
        console.log('[CSRF] Token found in meta tag');
        return metaToken;
    }
    
    // Try to get from XSRF-TOKEN cookie (Set by Laravel's middleware)
    const cookies = document.cookie.split('; ');
    for (const cookie of cookies) {
        if (cookie.startsWith('XSRF-TOKEN=')) {
            const cookieValue = cookie.split('=')[1];
            console.log('[CSRF] Token found in XSRF-TOKEN cookie');
            try {
                return decodeURIComponent(cookieValue);
            } catch (e) {
                return cookieValue;
            }
        }
    }
    
    // Fallback: Try laravel_session or other session-related cookies
    for (const cookie of cookies) {
        if (cookie.startsWith('laravel_token') || cookie.includes('token')) {
            console.log('[CSRF] Token found in fallback cookie:', cookie.split('=')[0]);
            return cookie.split('=')[1] || '';
        }
    }
    
    console.warn('[CSRF] ⚠️ No CSRF token found! Check if meta tag or cookies are set.');
    return '';
};

const getHeaders = (includeContentType = true) => {
    const csrfToken = getCsrfToken();
    const headers: Record<string, string> = {
        'Accept': 'application/json',
    };
    
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
        headers['X-XSRF-TOKEN'] = csrfToken;
    } else {
        console.warn('[Headers] ⚠️ CSRF token is missing! API request may fail.');
    }
    
    if (includeContentType) {
        headers['Content-Type'] = 'application/json';
    }
    
    return headers;
};

const getFetchOptions = (method: 'GET' | 'POST' | 'DELETE' = 'GET', body?: any) => {
    const options: RequestInit = {
        method,
        headers: getHeaders(),
        credentials: 'include', // Include cookies for CSRF token
    };
    
    if (body && (method === 'POST' || method === 'DELETE')) {
        options.body = JSON.stringify(body);
    }
    
    return options;
};

export default function CompanyForm({
    isOpen,
    onOpenChange,
    isEditing,
    editingCompany,
    onSuccess
}: CompanyFormProps) {
    const { __ } = useLang();
    const { locale } = usePage<PageProps>().props;
    const currentLocale = locale.current;

    // Autofill states
    const [isAutofilling, setIsAutofilling] = useState(false);
    const [autofillError, setAutofillError] = useState<string | null>(null);
    
    // Company data loading state
    const [isLoadingCompanyData, setIsLoadingCompanyData] = useState(false);
    
    // Form submission loading state
    const [isSubmitting, setIsSubmitting] = useState(false);
    
    // Signature and logo states
    const [signaturePreview, setSignaturePreview] = useState<string | null>(null);
    const [logoPreview, setLogoPreview] = useState<string | null>(null);
    
    // Default template configuration
    const defaultTemplateConfig = {
        template: 'Kronos',
        primary_color: '#b23a5a',
        currency: 'EUR',
        language: 'sk',
        numbering: {
            prefix: '',
            upcoming: 1,
            format: 'YEAR:4;NUMBER:4',
            due_date_additional_days: 14,
        },
        formats: {
            date: 'DD.MM.YYYY',
            decimal: ',',
            thousands: ' ',
        },
        visibility: {
            send_date: true,
            due_date: true,
            quantity: true,
            payment: true,
            qr_payment: true,
        },
        qr: {
            provider: 'UNIVERSAL',
        },
        preInvoice: {
            template: 'Sango',
            primary_color: '#b23a5a',
            currency: 'EUR',
            language: 'sk',
            enabled: false,
            numbering: {
                prefix: '',
                upcoming: 1,
                format: 'YEAR:4;NUMBER:4',
                due_date_additional_days: 7,
            },
            formats: {
                date: 'DD.MM.YYYY',
                decimal: ',',
                thousands: ' ',
            },
            visibility: {
                send_date: true,
                due_date: true,
                quantity: true,
                payment: true,
                qr_payment: true,
            },
            qr: {
                provider: 'UNIVERSAL',
            },
        },
    };
    
    // Template configuration states
    const [templateConfig, setTemplateConfig] = useState(defaultTemplateConfig);

    const { data, setData, reset, errors } = useForm({
        default: false,
        name: '',
        state: '',
        street: '',
        street_extra: '',
        zip: '',
        city: '',
        tax_type: '',
        identification_number: '',
        vat_identification_number: '',
        vat_identification_number_sk: '',
        registry_info: '',
        contact_name: '',
        contact_phone: '',
        contact_email: '',
        contact_web: '',
        signature_base64: '',
        logo_base64: '',
        payment_methods: {
            bank_transfer: {
                name: '',
                code: '',
                iban: '',
                swift: '',
            },
            paypal: {
                email: '',
            },
        },
    });

    // Fetch company data if editing and company UUID is available
    useEffect(() => {
        if (isEditing && editingCompany?.uuid && isOpen) {
            setIsLoadingCompanyData(true);
            const fetchCompanyData = async () => {
                try {
                    const response = await fetch(`/api/user/company/${editingCompany.uuid}`, {
                        method: 'GET',
                        headers: getHeaders(false),
                        credentials: 'include'
                    });
                    const responseData = await response.json();
                    if (responseData && responseData.data) {
                        // Use fetched data to populate the component state
                        const company = responseData.data;
                        
                        // Merge payment methods
                        const paymentMethods = {
                            bank_transfer: {
                                name: '',
                                code: '',
                                iban: '',
                                swift: '',
                            },
                            paypal: {
                                email: '',
                            },
                        };

                        if (company.payment_methods) {
                            if (company.payment_methods.bank_transfer) {
                                paymentMethods.bank_transfer = {
                                    name: company.payment_methods.bank_transfer.name || '',
                                    code: company.payment_methods.bank_transfer.code || '',
                                    iban: company.payment_methods.bank_transfer.iban || '',
                                    swift: company.payment_methods.bank_transfer.swift || '',
                                };
                            }
                            if (company.payment_methods.paypal) {
                                paymentMethods.paypal = {
                                    email: company.payment_methods.paypal.email || '',
                                };
                            }
                        }

                        setData({
                            default: company.default || false,
                            name: company.name,
                            state: company.state || '',
                            street: company.street || '',
                            street_extra: company.street_extra || '',
                            zip: company.zip || '',
                            city: company.city || '',
                            tax_type: company.tax_type || 'NO',
                            identification_number: company.identification_number || '',
                            vat_identification_number: company.vat_identification_number || '',
                            vat_identification_number_sk: company.vat_identification_number_sk || '',
                            registry_info: company.registry_info || '',
                            contact_name: company.contact_name || '',
                            contact_phone: company.contact_phone || '',
                            contact_email: company.contact_email || '',
                            contact_web: company.contact_web || '',
                            signature_base64: '',
                            logo_base64: '',
                            payment_methods: paymentMethods,
                        });

                        // Load template data if available
                        if (company.template) {
                            const parsedTemplate = typeof company.template === 'string' 
                                ? JSON.parse(company.template) 
                                : company.template;
                            
                            // Extract invoice template (main template)
                            const invoiceTemplate = parsedTemplate?.invoice || parsedTemplate;
                            const preInvoiceTemplate = parsedTemplate?.preInvoice || {};
                            
                            const mergedTemplate = {
                                template: invoiceTemplate?.template || 'Kronos',
                                primary_color: invoiceTemplate?.primary_color || '#b23a5a',
                                currency: invoiceTemplate?.currency || 'EUR',
                                language: invoiceTemplate?.language || 'sk',
                                numbering: {
                                    prefix: invoiceTemplate?.numbering?.prefix || '',
                                    upcoming: invoiceTemplate?.numbering?.upcoming || 1,
                                    format: invoiceTemplate?.numbering?.format || 'YEAR:4;NUMBER:4',
                                    due_date_additional_days: invoiceTemplate?.numbering?.due_date_additional_days || 14,
                                },
                                formats: {
                                    date: invoiceTemplate?.formats?.date || 'DD.MM.YYYY',
                                    decimal: invoiceTemplate?.formats?.decimal || ',',
                                    thousands: invoiceTemplate?.formats?.thousands || ' ',
                                },
                                visibility: {
                                    send_date: invoiceTemplate?.visibility?.send_date !== false,
                                    due_date: invoiceTemplate?.visibility?.due_date !== false,
                                    quantity: invoiceTemplate?.visibility?.quantity !== false,
                                    payment: invoiceTemplate?.visibility?.payment !== false,
                                    qr_payment: invoiceTemplate?.visibility?.qr_payment !== false,
                                },
                                qr: {
                                    provider: invoiceTemplate?.qr?.provider || 'UNIVERSAL',
                                },
                                preInvoice: {
                                    template: preInvoiceTemplate?.template || 'Sango',
                                    primary_color: preInvoiceTemplate?.primary_color || '#b23a5a',
                                    currency: preInvoiceTemplate?.currency || 'EUR',
                                    language: preInvoiceTemplate?.language || 'sk',
                                    enabled: !!preInvoiceTemplate?.template,
                                    numbering: {
                                        prefix: preInvoiceTemplate?.numbering?.prefix || '',
                                        upcoming: preInvoiceTemplate?.numbering?.upcoming || 1,
                                        format: preInvoiceTemplate?.numbering?.format || 'YEAR:4;NUMBER:4',
                                        due_date_additional_days: preInvoiceTemplate?.numbering?.due_date_additional_days || 7,
                                    },
                                    formats: {
                                        date: preInvoiceTemplate?.formats?.date || 'DD.MM.YYYY',
                                        decimal: preInvoiceTemplate?.formats?.decimal || ',',
                                        thousands: preInvoiceTemplate?.formats?.thousands || ' ',
                                    },
                                    visibility: {
                                        send_date: preInvoiceTemplate?.visibility?.send_date !== false,
                                        due_date: preInvoiceTemplate?.visibility?.due_date !== false,
                                        quantity: preInvoiceTemplate?.visibility?.quantity !== false,
                                        payment: preInvoiceTemplate?.visibility?.payment !== false,
                                        qr_payment: preInvoiceTemplate?.visibility?.qr_payment !== false,
                                    },
                                    qr: {
                                        provider: preInvoiceTemplate?.qr?.provider || 'UNIVERSAL',
                                    },
                                },
                            };
                            
                            setTemplateConfig(mergedTemplate);
                        }
                    }
                } catch (error) {
                    console.error('Error fetching company data:', error);
                    setAutofillError(__('failed_to_fetch_company_data'));
                } finally {
                    setIsLoadingCompanyData(false);
                }
            };

            fetchCompanyData();
        }
    }, [isEditing, editingCompany?.uuid, isOpen]);

    // Reset form when dialog is closed
    useEffect(() => {
        if (!isOpen) {
            reset();
            setTemplateConfig(defaultTemplateConfig);
            setAutofillError(null);
            setSignaturePreview(null);
            setLogoPreview(null);
        }
    }, [isOpen, reset]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (isEditing && editingCompany) {
            // For editing: make separate API calls for different data types
            handleEditCompany();
        } else {
            // For creating: send all data in one request
            handleCreateCompany();
        }
    };

    const handleEditCompany = async () => {
        try {
            setIsSubmitting(true);
            // 1. Update basic company information
            const basicData = {
                default: data.default,
                name: data.name,
                state: data.state,
                street: data.street,
                street_extra: data.street_extra,
                zip: data.zip,
                city: data.city,
                tax_type: data.tax_type,
                identification_number: data.identification_number,
                vat_identification_number: data.vat_identification_number,
                vat_identification_number_sk: data.vat_identification_number_sk,
                registry_info: data.registry_info,
                contact_name: data.contact_name,
                contact_phone: data.contact_phone,
                contact_email: data.contact_email,
                contact_web: data.contact_web,
            };

            await fetch(`/api/user/company/${editingCompany.uuid}/basic`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(basicData),
                credentials: 'include'
            });

            // 2. Update bank account if provided
            if (data.payment_methods?.bank_transfer?.iban || data.payment_methods?.bank_transfer?.name) {
                await fetch(`/api/user/company/${editingCompany.uuid}/bankAccount`, {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({
                        bank_transfer: data.payment_methods.bank_transfer,
                    }),
                    credentials: 'include'
                });
            }

            // 3. Update signature if provided
            if (data.signature_base64) {
                await fetch(`/api/user/company/${editingCompany.uuid}/signature`, {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({
                        signature_base64: data.signature_base64,
                    }),
                    credentials: 'include'
                });
            }

            // 4. Update template configuration
            const templateData = {
                template: templateConfig.template,
                primary_color: templateConfig.primary_color,
                currency: templateConfig.currency,
                language: templateConfig.language,
                numbering: {
                    prefix: templateConfig.numbering.prefix || '',
                    upcoming: templateConfig.numbering.upcoming,
                    format: templateConfig.numbering.format,
                    due_date_additional_days: templateConfig.numbering.due_date_additional_days,
                },
                formats: templateConfig.formats,
                visibility: templateConfig.visibility,
                qr: templateConfig.qr,
                preInvoice: templateConfig.preInvoice.enabled ? {
                    template: templateConfig.preInvoice.template,
                    primary_color: templateConfig.preInvoice.primary_color,
                    currency: templateConfig.preInvoice.currency,
                    language: templateConfig.preInvoice.language,
                    numbering: {
                        prefix: templateConfig.preInvoice.numbering.prefix || '',
                        upcoming: templateConfig.preInvoice.numbering.upcoming,
                        format: templateConfig.preInvoice.numbering.format,
                        due_date_additional_days: templateConfig.preInvoice.numbering.due_date_additional_days,
                    },
                    formats: templateConfig.preInvoice.formats,
                    visibility: templateConfig.preInvoice.visibility,
                    qr: templateConfig.preInvoice.qr,
                } : undefined,
            };

            await fetch(`/api/user/company/${editingCompany.uuid}/template`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(templateData),
                credentials: 'include'
            });

            // Success - close dialog and call callback
            onOpenChange(false);
            if (onSuccess) {
                onSuccess();
            }
        } catch (error) {
            console.error('Error updating company:', error);
            alert('Failed to update company. Please try again.');
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleCreateCompany = async () => {
        try {
            setIsSubmitting(true);
            
            // 1. Create company with basic data
            const basicData = {
                default: data.default,
                name: data.name,
                state: data.state,
                street: data.street,
                street_extra: data.street_extra,
                zip: data.zip,
                city: data.city,
                tax_type: data.tax_type,
                identification_number: data.identification_number,
                vat_identification_number: data.vat_identification_number,
                vat_identification_number_sk: data.vat_identification_number_sk,
                registry_info: data.registry_info,
                contact_name: data.contact_name,
                contact_phone: data.contact_phone,
                contact_email: data.contact_email,
                contact_web: data.contact_web,
                payment_methods: data.payment_methods,
                signature_base64: data.signature_base64,
                logo_base64: data.logo_base64,
            };

            const createResponse = await fetch('/api/user/company', {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(basicData),
                credentials: 'include'
            });
            const createResponseData = await createResponse.json();
            const companyUuid = createResponseData.data.uuid;

            // 2. Update bank account if provided
            if (data.payment_methods?.bank_transfer?.iban || data.payment_methods?.bank_transfer?.name) {
                await fetch(`/api/user/company/${companyUuid}/bankAccount`, {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({
                        bank_transfer: data.payment_methods.bank_transfer,
                    }),
                    credentials: 'include'
                });
            }

            // 3. Update signature if provided
            if (data.signature_base64) {
                await fetch(`/api/user/company/${companyUuid}/signature`, {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({
                        signature_base64: data.signature_base64,
                    }),
                    credentials: 'include'
                });
            }

            // 4. Update template configuration
            const templateData = {
                template: templateConfig.template,
                primary_color: templateConfig.primary_color,
                currency: templateConfig.currency,
                language: templateConfig.language,
                numbering: {
                    prefix: templateConfig.numbering.prefix || '',
                    upcoming: templateConfig.numbering.upcoming,
                    format: templateConfig.numbering.format,
                    due_date_additional_days: templateConfig.numbering.due_date_additional_days,
                },
                formats: templateConfig.formats,
                visibility: templateConfig.visibility,
                qr: templateConfig.qr,
                preInvoice: templateConfig.preInvoice.enabled ? {
                    template: templateConfig.preInvoice.template,
                    primary_color: templateConfig.preInvoice.primary_color,
                    currency: templateConfig.preInvoice.currency,
                    language: templateConfig.preInvoice.language,
                    numbering: {
                        prefix: templateConfig.preInvoice.numbering.prefix || '',
                        upcoming: templateConfig.preInvoice.numbering.upcoming,
                        format: templateConfig.preInvoice.numbering.format,
                        due_date_additional_days: templateConfig.preInvoice.numbering.due_date_additional_days,
                    },
                    formats: templateConfig.preInvoice.formats,
                    visibility: templateConfig.preInvoice.visibility,
                    qr: templateConfig.preInvoice.qr,
                } : undefined,
            };

            await fetch(`/api/user/company/${companyUuid}/template`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(templateData),
                credentials: 'include'
            });
            
            // Success - close dialog and call callback
            onOpenChange(false);
            if (onSuccess) {
                onSuccess();
            }
        } catch (error) {
            console.error('Error creating company:', error);
            alert('Failed to create company. Please try again.');
        } finally {
            setIsSubmitting(false);
        }
    };

    // Handle file upload and convert to base64
    const handleFileUpload = async (file: File, type: 'signature' | 'logo') => {
        // Validate file type
        if (file.type !== 'image/png') {
            alert('Only PNG files are allowed');
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            const base64String = e.target?.result as string;
            
            // Remove data URL prefix (data:image/png;base64,) - keep only the base64 content
            const cleanBase64 = base64String.includes(',') 
                ? base64String.split(',')[1] 
                : base64String;
            
            if (type === 'signature') {
                setSignaturePreview(base64String);
                setData('signature_base64', cleanBase64);
            } else if (type === 'logo') {
                setLogoPreview(base64String);
                setData('logo_base64', cleanBase64);
            }
        };
        
        reader.onerror = () => {
            alert('Error reading file');
        };
        
        reader.readAsDataURL(file);
    };

    // Handle file input change
    const handleSignatureChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            handleFileUpload(file, 'signature');
        }
    };

    const handleLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            handleFileUpload(file, 'logo');
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
            const params = new URLSearchParams({
                country2code: data.state,
                registrationNumber: data.identification_number
            });
            const response = await fetch(`/api/autocomplete/company?${params}`, {
                method: 'GET',
                headers: getHeaders(false),
                credentials: 'include'
            });
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
                error.response?.data?.error ||
                'Failed to fetch company data. Please try again.'
            );
        } finally {
            setIsAutofilling(false);
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>{isEditing ? __('companies.edit_company') : __('companies.form.title_add')}</DialogTitle>
                    <DialogDescription>
                        {isEditing
                            ? __('companies.form.description_edit')
                            : __('companies.form.description_add')}
                    </DialogDescription>
                </DialogHeader>
                {isLoadingCompanyData ? (
                    <div className="flex flex-col items-center justify-center py-12">
                        <svg className="h-8 w-8 animate-spin text-gray-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p className="text-gray-600 font-medium">Loading company data...</p>
                    </div>
                ) : (
                <div className="max-h-[60vh] overflow-y-auto pr-4">
                    <form onSubmit={handleSubmit}>
                        <div className="grid gap-4 py-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="name">{__('companies.form.name')} *</Label>
                                    <Input
                                        id="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        required
                                    />
                                    {errors.name && <p className="text-sm text-red-500">{errors.name}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="state">{__('companies.form.state')}</Label>
                                    <StateSelect
                                        value={data.state}
                                        onChange={(value) => setData('state', value)}
                                        placeholder={__('companies.form.state_placeholder') || 'Select a country'}
                                    />
                                    {errors.state && <p className="text-sm text-red-500">{errors.state}</p>}
                                </div>
                            </div>

                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="default"
                                    checked={data.default}
                                    onCheckedChange={(checked) => setData('default', checked === true)}
                                />
                                <Label htmlFor="default" className="font-normal cursor-pointer">
                                    Set as default company
                                </Label>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="street">Street Address</Label>
                                <Input
                                    id="street"
                                    value={data.street}
                                    onChange={(e) => setData('street', e.target.value)}
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
                                    <Label htmlFor="zip">ZIP/Postal Code</Label>
                                    <Input
                                        id="zip"
                                        value={data.zip}
                                        onChange={(e) => setData('zip', e.target.value)}
                                    />
                                    {errors.zip && <p className="text-sm text-red-500">{errors.zip}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="city">City</Label>
                                    <Input
                                        id="city"
                                        value={data.city}
                                        onChange={(e) => setData('city', e.target.value)}
                                    />
                                    {errors.city && <p className="text-sm text-red-500">{errors.city}</p>}
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="tax_type">{__('companies.form.tax_type')}</Label>
                                <Select
                                    value={data.tax_type}
                                    onValueChange={(value) => setData('tax_type', value)}
                                    required
                                >
                                    <SelectTrigger id="tax_type" className="w-full">
                                        <SelectValue placeholder={__('companies.form.tax_type')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(taxTypeData).map(([code, data]) => (
                                            <SelectItem key={code} value={code}>
                                                {/* @ts-ignore - we know the structure of the data */}
                                                {data.name[currentLocale] || data.name.en}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.tax_type && <p className="text-sm text-red-500">{errors.tax_type}</p>}
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="identification_number">{__('companies.form.identification_number')}</Label>
                                    <Input
                                        id="identification_number"
                                        value={data.identification_number}
                                        onChange={(e) => setData('identification_number', e.target.value)}
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
                                    <Label htmlFor="vat_identification_number">VAT ID (DIC)</Label>
                                    <Input
                                        id="vat_identification_number"
                                        value={data.vat_identification_number}
                                        onChange={(e) => setData('vat_identification_number', e.target.value)}
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

                            {/* Bank Transfer Section */}
                            <div className="border-t pt-4 mt-4">
                                <h3 className="text-lg font-semibold mb-4">Bank Account Details</h3>
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="bank_name">Bank Name</Label>
                                        <Input
                                            id="bank_name"
                                            value={data.payment_methods?.bank_transfer?.name || ''}
                                            onChange={(e) => setData('payment_methods', {
                                                ...data.payment_methods,
                                                bank_transfer: {
                                                    ...data.payment_methods?.bank_transfer,
                                                    name: e.target.value,
                                                }
                                            })}
                                        />
                                        {errors['payment_methods.bank_transfer.name'] && <p className="text-sm text-red-500">{errors['payment_methods.bank_transfer.name']}</p>}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="bank_code">Bank Code</Label>
                                        <Input
                                            id="bank_code"
                                            value={data.payment_methods?.bank_transfer?.code || ''}
                                            onChange={(e) => setData('payment_methods', {
                                                ...data.payment_methods,
                                                bank_transfer: {
                                                    ...data.payment_methods?.bank_transfer,
                                                    code: e.target.value,
                                                }
                                            })}
                                        />
                                        {errors['payment_methods.bank_transfer.code'] && <p className="text-sm text-red-500">{errors['payment_methods.bank_transfer.code']}</p>}
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-4 mt-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="bank_iban">IBAN</Label>
                                        <Input
                                            id="bank_iban"
                                            value={data.payment_methods?.bank_transfer?.iban || ''}
                                            onChange={(e) => setData('payment_methods', {
                                                ...data.payment_methods,
                                                bank_transfer: {
                                                    ...data.payment_methods?.bank_transfer,
                                                    iban: e.target.value,
                                                }
                                            })}
                                        />
                                        {errors['payment_methods.bank_transfer.iban'] && <p className="text-sm text-red-500">{errors['payment_methods.bank_transfer.iban']}</p>}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="bank_swift">SWIFT</Label>
                                        <Input
                                            id="bank_swift"
                                            value={data.payment_methods?.bank_transfer?.swift || ''}
                                            onChange={(e) => setData('payment_methods', {
                                                ...data.payment_methods,
                                                bank_transfer: {
                                                    ...data.payment_methods?.bank_transfer,
                                                    swift: e.target.value,
                                                }
                                            })}
                                        />
                                        {errors['payment_methods.bank_transfer.swift'] && <p className="text-sm text-red-500">{errors['payment_methods.bank_transfer.swift']}</p>}
                                    </div>
                                </div>
                            </div>

                            <div className="grid gap-2">
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
                                        type="url"
                                        value={data.contact_web}
                                        onChange={(e) => setData('contact_web', e.target.value)}
                                        placeholder="https://example.com"
                                    />
                                    {errors.contact_web && <p className="text-sm text-red-500">{errors.contact_web}</p>}
                                </div>
                            </div>

                            {/* Signature and Logo Section */}
                            <div className="border-t pt-4 mt-4">
                                <h3 className="text-lg font-semibold mb-4">Signature & Logo</h3>
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="signature_upload">Signature</Label>
                                        <Input
                                            id="signature_upload"
                                            type="file"
                                            accept="image/*"
                                            onChange={handleSignatureChange}
                                            className="cursor-pointer"
                                        />
                                        {signaturePreview && (
                                            <div className="mt-2 p-2 border rounded">
                                                <img src={signaturePreview} alt="Signature Preview" className="max-h-[100px] max-w-full" />
                                            </div>
                                        )}
                                        {errors['signature_base64'] && <p className="text-sm text-red-500">{errors['signature_base64']}</p>}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="logo_upload">Logo</Label>
                                        <Input
                                            id="logo_upload"
                                            type="file"
                                            accept="image/*"
                                            onChange={handleLogoChange}
                                            className="cursor-pointer"
                                        />
                                        {logoPreview && (
                                            <div className="mt-2 p-2 border rounded">
                                                <img src={logoPreview} alt="Logo Preview" className="max-h-[100px] max-w-full" />
                                            </div>
                                        )}
                                        {errors['logo_base64'] && <p className="text-sm text-red-500">{errors['logo_base64']}</p>}
                                    </div>
                                </div>
                            </div>

                            {/* Template Configuration Section */}
                            <div className="border-t pt-4 mt-4">
                                <h3 className="text-lg font-semibold mb-4">Template Configuration</h3>
                                
                                {/* Invoice Template */}
                                <div className="mb-6">
                                    <h4 className="text-md font-medium mb-3">Invoice Template</h4>

                                    {/* Template Selection */}
                                    <div className="mb-4 grid grid-cols-3 gap-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="template_name">Template Design</Label>
                                            <Select
                                                value={templateConfig.template}
                                                onValueChange={(value) => setTemplateConfig({
                                                    ...templateConfig,
                                                    template: value
                                                })}
                                            >
                                                <SelectTrigger id="template_name" className="w-full">
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Sarif">Sarif</SelectItem>
                                                    <SelectItem value="Sango">Sango</SelectItem>
                                                    <SelectItem value="Kronos">Kronos</SelectItem>
                                                    <SelectItem value="Kros">Kros</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <div className="grid gap-2">
                                            <Label htmlFor="primary_color">Primary Color</Label>
                                            <Input
                                                id="primary_color"
                                                type="color"
                                                value={templateConfig.primary_color}
                                                onChange={(e) => setTemplateConfig({
                                                    ...templateConfig,
                                                    primary_color: e.target.value
                                                })}
                                            />
                                        </div>
                                        <div className="grid gap-2">
                                            <Label htmlFor="template_currency">Currency</Label>
                                            <Input
                                                id="template_currency"
                                                value={templateConfig.currency}
                                                onChange={(e) => setTemplateConfig({
                                                    ...templateConfig,
                                                    currency: e.target.value.toUpperCase()
                                                })}
                                                placeholder="EUR"
                                                maxLength={3}
                                            />
                                        </div>
                                    </div>

                                    {/* Language Setting */}
                                    <div className="mb-4 grid gap-2 max-w-xs">
                                        <Label htmlFor="template_language">Language</Label>
                                        <Input
                                            id="template_language"
                                            value={templateConfig.language}
                                            onChange={(e) => setTemplateConfig({
                                                ...templateConfig,
                                                language: e.target.value.toLowerCase()
                                            })}
                                            placeholder="sk"
                                            maxLength={2}
                                        />
                                    </div>
                                    
                                    {/* Numbering Settings */}
                                    <div className="mb-4 p-3 bg-gray-50 rounded">
                                        <p className="text-sm font-medium mb-2">Numbering</p>
                                        <div className="grid grid-cols-2 gap-2">
                                            <div className="grid gap-2">
                                                <Label htmlFor="inv_upcoming" className="text-xs">Next Number</Label>
                                                <Input
                                                    id="inv_upcoming"
                                                    type="number"
                                                    min="0"
                                                    value={templateConfig.numbering.upcoming}
                                                    onChange={(e) => setTemplateConfig({
                                                        ...templateConfig,
                                                        numbering: { ...templateConfig.numbering, upcoming: parseInt(e.target.value) || 0 }
                                                    })}
                                                />
                                            </div>
                                            <div className="grid gap-2">
                                                <Label htmlFor="inv_format" className="text-xs">Format</Label>
                                                <Input
                                                    id="inv_format"
                                                    value={templateConfig.numbering.format}
                                                    onChange={(e) => setTemplateConfig({
                                                        ...templateConfig,
                                                        numbering: { ...templateConfig.numbering, format: e.target.value }
                                                    })}
                                                    placeholder="e.g., YEAR:4;NUMBER:4"
                                                />
                                            </div>
                                            <div className="grid gap-2 col-span-2">
                                                <Label htmlFor="inv_duedate_days" className="text-xs">Due Date Additional Days</Label>
                                                <Input
                                                    id="inv_duedate_days"
                                                    type="number"
                                                    min="0"
                                                    value={templateConfig.numbering.due_date_additional_days}
                                                    onChange={(e) => setTemplateConfig({
                                                        ...templateConfig,
                                                        numbering: { ...templateConfig.numbering, due_date_additional_days: parseInt(e.target.value) || 0 }
                                                    })}
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Format Settings */}
                                    <div className="mb-4 p-3 bg-gray-50 rounded">
                                        <p className="text-sm font-medium mb-2">Formats</p>
                                        <div className="grid grid-cols-3 gap-2">
                                            <div className="grid gap-2">
                                                <Label htmlFor="inv_date_format" className="text-xs">Date Format</Label>
                                                <Input
                                                    id="inv_date_format"
                                                    value={templateConfig.formats.date}
                                                    onChange={(e) => setTemplateConfig({
                                                        ...templateConfig,
                                                        formats: { ...templateConfig.formats, date: e.target.value }
                                                    })}
                                                    placeholder="e.g., DD.MM.YYYY"
                                                />
                                            </div>
                                            <div className="grid gap-2">
                                                <Label htmlFor="inv_decimal_sep" className="text-xs">Decimal Separator</Label>
                                                <Input
                                                    id="inv_decimal_sep"
                                                    value={templateConfig.formats.decimal}
                                                    onChange={(e) => setTemplateConfig({
                                                        ...templateConfig,
                                                        formats: { ...templateConfig.formats, decimal: e.target.value }
                                                    })}
                                                    maxLength={1}
                                                />
                                            </div>
                                            <div className="grid gap-2">
                                                <Label htmlFor="inv_thousands_sep" className="text-xs">Thousands Separator</Label>
                                                <Input
                                                    id="inv_thousands_sep"
                                                    value={templateConfig.formats.thousands}
                                                    onChange={(e) => setTemplateConfig({
                                                        ...templateConfig,
                                                        formats: { ...templateConfig.formats, thousands: e.target.value }
                                                    })}
                                                    maxLength={1}
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Visibility Settings */}
                                    <div className="p-3 bg-gray-50 rounded">
                                        <p className="text-sm font-medium mb-2">Visibility</p>
                                        <div className="grid grid-cols-2 gap-3">
                                            {Object.entries(templateConfig.visibility).map(([key, value]) => (
                                                <div key={key} className="flex items-center gap-2">
                                                    <Checkbox
                                                        id={`inv_vis_${key}`}
                                                        checked={value}
                                                        onCheckedChange={(checked) => setTemplateConfig({
                                                            ...templateConfig,
                                                            visibility: { ...templateConfig.visibility, [key]: checked === true }
                                                        })}
                                                    />
                                                    <Label htmlFor={`inv_vis_${key}`} className="text-xs font-normal cursor-pointer capitalize">
                                                        {key.replace(/_/g, ' ')}
                                                    </Label>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>

                                {/* QR Provider Setting */}
                                <div className="mb-4 p-3 bg-gray-50 rounded">
                                    <Label htmlFor="qr_provider">QR Provider</Label>
                                    <Select
                                        value={templateConfig.qr.provider}
                                        onValueChange={(value) => setTemplateConfig({
                                            ...templateConfig,
                                            qr: { provider: value }
                                        })}
                                    >
                                        <SelectTrigger id="qr_provider" className="w-full mt-1">
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="UNIVERSAL">Universal QR</SelectItem>
                                            <SelectItem value="PAY_BY_SQUARE">Pay by Square (SK)</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                {/* Pre-Invoice Template */}
                                <div className="border-t pt-4">
                                    <div className="flex items-center gap-2 mb-4">
                                        <Checkbox
                                            id="pre_invoice_enabled"
                                            checked={templateConfig.preInvoice.enabled}
                                            onCheckedChange={(checked) => setTemplateConfig({
                                                ...templateConfig,
                                                preInvoice: { ...templateConfig.preInvoice, enabled: checked === true }
                                            })}
                                        />
                                        <Label htmlFor="pre_invoice_enabled" className="font-normal cursor-pointer">
                                            Enable Pre-Invoice (Proforma)
                                        </Label>
                                    </div>

                                    {templateConfig.preInvoice.enabled && (
                                        <>
                                            {/* Pre-Invoice Template Selection */}
                                            <div className="mb-4 grid grid-cols-3 gap-4">
                                                <div className="grid gap-2">
                                                    <Label htmlFor="pre_template_name">Template Design</Label>
                                                    <Select
                                                        value={templateConfig.preInvoice.template}
                                                        onValueChange={(value) => setTemplateConfig({
                                                            ...templateConfig,
                                                            preInvoice: { ...templateConfig.preInvoice, template: value }
                                                        })}
                                                    >
                                                        <SelectTrigger id="pre_template_name" className="w-full">
                                                            <SelectValue />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem value="Sarif">Sarif</SelectItem>
                                                            <SelectItem value="Sango">Sango</SelectItem>
                                                            <SelectItem value="Kronos">Kronos</SelectItem>
                                                            <SelectItem value="Kros">Kros</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                                <div className="grid gap-2">
                                                    <Label htmlFor="pre_primary_color">Primary Color</Label>
                                                    <Input
                                                        id="pre_primary_color"
                                                        type="color"
                                                        value={templateConfig.preInvoice.primary_color}
                                                        onChange={(e) => setTemplateConfig({
                                                            ...templateConfig,
                                                            preInvoice: { ...templateConfig.preInvoice, primary_color: e.target.value }
                                                        })}
                                                    />
                                                </div>
                                                <div className="grid gap-2">
                                                    <Label htmlFor="pre_currency">Currency</Label>
                                                    <Input
                                                        id="pre_currency"
                                                        value={templateConfig.preInvoice.currency}
                                                        onChange={(e) => setTemplateConfig({
                                                            ...templateConfig,
                                                            preInvoice: { ...templateConfig.preInvoice, currency: e.target.value.toUpperCase() }
                                                        })}
                                                        placeholder="EUR"
                                                        maxLength={3}
                                                    />
                                                </div>
                                            </div>

                                            {/* Pre-Invoice Language */}
                                            <div className="mb-4 grid gap-2 max-w-xs">
                                                <Label htmlFor="pre_language">Language</Label>
                                                <Input
                                                    id="pre_language"
                                                    value={templateConfig.preInvoice.language}
                                                    onChange={(e) => setTemplateConfig({
                                                        ...templateConfig,
                                                        preInvoice: { ...templateConfig.preInvoice, language: e.target.value.toLowerCase() }
                                                    })}
                                                    placeholder="sk"
                                                    maxLength={2}
                                                />
                                            </div>

                                            {/* Pre-Invoice Numbering */}
                                            <div className="mb-4 p-3 bg-gray-50 rounded">
                                                <p className="text-sm font-medium mb-2">Pre-Invoice Numbering</p>
                                                <div className="grid grid-cols-2 gap-2">
                                                    <div className="grid gap-2">
                                                        <Label htmlFor="pre_upcoming" className="text-xs">Next Number</Label>
                                                        <Input
                                                            id="pre_upcoming"
                                                            type="number"
                                                            min="0"
                                                            value={templateConfig.preInvoice.numbering.upcoming}
                                                            onChange={(e) => setTemplateConfig({
                                                                ...templateConfig,
                                                                preInvoice: { 
                                                                    ...templateConfig.preInvoice, 
                                                                    numbering: { ...templateConfig.preInvoice.numbering, upcoming: parseInt(e.target.value) || 0 }
                                                                }
                                                            })}
                                                        />
                                                    </div>
                                                    <div className="grid gap-2">
                                                        <Label htmlFor="pre_format" className="text-xs">Format</Label>
                                                        <Input
                                                            id="pre_format"
                                                            value={templateConfig.preInvoice.numbering.format}
                                                            onChange={(e) => setTemplateConfig({
                                                                ...templateConfig,
                                                                preInvoice: { 
                                                                    ...templateConfig.preInvoice, 
                                                                    numbering: { ...templateConfig.preInvoice.numbering, format: e.target.value }
                                                                }
                                                            })}
                                                            placeholder="e.g., YEAR:4;NUMBER:4"
                                                        />
                                                    </div>
                                                    <div className="grid gap-2 col-span-2">
                                                        <Label htmlFor="pre_duedate_days" className="text-xs">Due Date Additional Days</Label>
                                                        <Input
                                                            id="pre_duedate_days"
                                                            type="number"
                                                            min="0"
                                                            value={templateConfig.preInvoice.numbering.due_date_additional_days}
                                                            onChange={(e) => setTemplateConfig({
                                                                ...templateConfig,
                                                                preInvoice: { 
                                                                    ...templateConfig.preInvoice, 
                                                                    numbering: { ...templateConfig.preInvoice.numbering, due_date_additional_days: parseInt(e.target.value) || 0 }
                                                                }
                                                            })}
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Pre-Invoice Format Settings */}
                                            <div className="mb-4 p-3 bg-gray-50 rounded">
                                                <p className="text-sm font-medium mb-2">Pre-Invoice Formats</p>
                                                <div className="grid grid-cols-3 gap-2">
                                                    <div className="grid gap-2">
                                                        <Label htmlFor="pre_date_format" className="text-xs">Date Format</Label>
                                                        <Input
                                                            id="pre_date_format"
                                                            value={templateConfig.preInvoice.formats.date}
                                                            onChange={(e) => setTemplateConfig({
                                                                ...templateConfig,
                                                                preInvoice: { 
                                                                    ...templateConfig.preInvoice, 
                                                                    formats: { ...templateConfig.preInvoice.formats, date: e.target.value }
                                                                }
                                                            })}
                                                            placeholder="e.g., DD.MM.YYYY"
                                                        />
                                                    </div>
                                                    <div className="grid gap-2">
                                                        <Label htmlFor="pre_decimal_sep" className="text-xs">Decimal Separator</Label>
                                                        <Input
                                                            id="pre_decimal_sep"
                                                            value={templateConfig.preInvoice.formats.decimal}
                                                            onChange={(e) => setTemplateConfig({
                                                                ...templateConfig,
                                                                preInvoice: { 
                                                                    ...templateConfig.preInvoice, 
                                                                    formats: { ...templateConfig.preInvoice.formats, decimal: e.target.value }
                                                                }
                                                            })}
                                                            maxLength={1}
                                                        />
                                                    </div>
                                                    <div className="grid gap-2">
                                                        <Label htmlFor="pre_thousands_sep" className="text-xs">Thousands Separator</Label>
                                                        <Input
                                                            id="pre_thousands_sep"
                                                            value={templateConfig.preInvoice.formats.thousands}
                                                            onChange={(e) => setTemplateConfig({
                                                                ...templateConfig,
                                                                preInvoice: { 
                                                                    ...templateConfig.preInvoice, 
                                                                    formats: { ...templateConfig.preInvoice.formats, thousands: e.target.value }
                                                                }
                                                            })}
                                                            maxLength={1}
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Pre-Invoice Visibility Settings */}
                                            <div className="mb-4 p-3 bg-gray-50 rounded">
                                                <p className="text-sm font-medium mb-2">Pre-Invoice Visibility</p>
                                                <div className="grid grid-cols-2 gap-3">
                                                    {Object.entries(templateConfig.preInvoice.visibility).map(([key, value]) => (
                                                        <div key={key} className="flex items-center gap-2">
                                                            <Checkbox
                                                                id={`pre_vis_${key}`}
                                                                checked={value}
                                                                onCheckedChange={(checked) => setTemplateConfig({
                                                                    ...templateConfig,
                                                                    preInvoice: {
                                                                        ...templateConfig.preInvoice,
                                                                        visibility: { ...templateConfig.preInvoice.visibility, [key]: checked === true }
                                                                    }
                                                                })}
                                                            />
                                                            <Label htmlFor={`pre_vis_${key}`} className="text-xs font-normal cursor-pointer capitalize">
                                                                {key.replace(/_/g, ' ')}
                                                            </Label>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>

                                            {/* Pre-Invoice QR Provider */}
                                            <div className="p-3 bg-gray-50 rounded">
                                                <Label htmlFor="pre_qr_provider">QR Provider</Label>
                                                <Select
                                                    value={templateConfig.preInvoice.qr.provider}
                                                    onValueChange={(value) => setTemplateConfig({
                                                        ...templateConfig,
                                                        preInvoice: {
                                                            ...templateConfig.preInvoice,
                                                            qr: { provider: value }
                                                        }
                                                    })}
                                                >
                                                    <SelectTrigger id="pre_qr_provider" className="w-full mt-1">
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="UNIVERSAL">Universal QR</SelectItem>
                                                        <SelectItem value="PAY_BY_SQUARE">Pay by Square (SK)</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        </>
                                    )}
                                </div>
                            </div>
                        </div>
                        <DialogFooter>
                            <Button type="submit" disabled={isSubmitting}>
                                {isEditing ? __('companies.form.submit_edit') : __('companies.form.submit_add')}
                            </Button>
                        </DialogFooter>
                    </form>
                </div>
                )}
            </DialogContent>
        </Dialog>
    );
}
