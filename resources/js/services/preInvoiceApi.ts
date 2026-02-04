const API_BASE_URL = '/api';

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
        if (cookie.startsWith('XSRF-TOKEN') || cookie.startsWith('laravel_token')) {
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
    
    // Add CSRF token - Laravel checks both headers
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
        // Some Laravel configs also check X-XSRF-TOKEN
        headers['X-XSRF-TOKEN'] = csrfToken;
    } else {
        console.warn('[Headers] ⚠️ CSRF token is missing! API request may fail.');
    }
    
    if (includeContentType) {
        headers['Content-Type'] = 'application/json';
    }
    
    return headers;
};

const handleFetchResponse = async (response: Response) => {
    if (!response.ok) {
        const error = await response.json().catch(() => ({}));
        throw error;
    }
    return response.json().catch(() => ({}));
};

// 1. Create Pre-Invoice
export const createPreInvoice = async (companyUuid: string, data: any) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/preinvoice`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(data),
        }
    );
    return handleFetchResponse(response);
};

// 2. Get Pre-Invoice
export const getPreInvoice = async (companyUuid: string, preInvoiceUuid: string) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/preinvoice/${preInvoiceUuid}`,
        { headers: getHeaders() }
    );
    return handleFetchResponse(response);
};

// 3. List All Pre-Invoices
export const listPreInvoices = async (companyUuid: string, params?: any) => {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString 
        ? `${API_BASE_URL}/user/company/${companyUuid}/preinvoices?${queryString}`
        : `${API_BASE_URL}/user/company/${companyUuid}/preinvoices`;
    
    const response = await fetch(url, { headers: getHeaders() });
    return handleFetchResponse(response);
};

// 4. Update Pre-Invoice
export const updatePreInvoice = async (companyUuid: string, preInvoiceUuid: string, data: any) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/preinvoice/${preInvoiceUuid}`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(data),
        }
    );
    return handleFetchResponse(response);
};

// 5. Delete Pre-Invoice
export const deletePreInvoice = async (companyUuid: string, preInvoiceUuid: string) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/preinvoice/${preInvoiceUuid}`,
        {
            method: 'DELETE',
            headers: getHeaders(),
        }
    );
    return handleFetchResponse(response);
};

// 6. Get Pre-Invoice PDF (with retry logic)
export const getPreInvoicePdf = async (
    companyUuid: string,
    preInvoiceUuid: string,
    maxRetries: number = 3
) => {
    let lastError: any = null;

    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            console.log(
                `[Pre-Invoice PDF Download] Attempt ${attempt}/${maxRetries} for pre-invoice: ${preInvoiceUuid}`
            );

            // Step 1: Wait for PDF generation
            const waitTime = 1000 + (attempt - 1) * 1000; // 1s, 2s, 3s
            console.log(`[Pre-Invoice PDF Download] Step 1: Waiting ${waitTime}ms for generation...`);
            await new Promise(resolve => setTimeout(resolve, waitTime));

            // Step 2: Download PDF
            console.log(`[Pre-Invoice PDF Download] Step 2: Downloading PDF...`);
            const url = `${API_BASE_URL}/user/company/${companyUuid}/preinvoice/${preInvoiceUuid}/pdf`;
            
            // Use fetch to check if PDF exists before opening
            try {
                const headResponse = await fetch(url, {
                    method: 'HEAD',
                    headers: getHeaders(),
                });
                
                if (!headResponse.ok) {
                    if (headResponse.status === 404) {
                        lastError = {
                            code: 'PRE_INVOICE_PDF_NOT_FOUND',
                            message: 'PDF not generated yet',
                            status: 404,
                        };
                        console.warn(
                            `[Pre-Invoice PDF Download] PDF not found (404) on attempt ${attempt}, retrying...`
                        );
                        if (attempt < maxRetries) {
                            continue;
                        }
                    } else {
                        throw new Error(`HTTP ${headResponse.status}`);
                    }
                } else {
                    console.log(`[Pre-Invoice PDF Download] PDF found, opening in new tab...`);
                    window.open(url, '_blank');
                    console.log(`[Pre-Invoice PDF Download] ✅ Success on attempt ${attempt}`);
                    return;
                }
            } catch (error: any) {
                console.error(`[Pre-Invoice PDF Download] Fetch error:`, error);
                lastError = error;
                if (attempt < maxRetries) {
                    continue;
                }
            }
        } catch (error: any) {
            console.error(`[Pre-Invoice PDF Download] Attempt ${attempt} failed:`, error);
            lastError = error;

            if (attempt === maxRetries) {
                break;
            }
        }
    }

    // All retries exhausted
    console.error(`[Pre-Invoice PDF Download] ❌ All ${maxRetries} attempts failed`);
    throw {
        code: lastError?.code || 'PRE_INVOICE_PDF_GENERATION_FAILED',
        message:
            lastError?.message ||
            'Failed to generate and download pre-invoice PDF after multiple retries',
        details: lastError,
    };
};

// 7. Change Pre-Invoice Paid Status
export const changePreInvoicePaidStatus = async (companyUuid: string, preInvoiceUuid: string, paid: boolean) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/preinvoice/${preInvoiceUuid}/paid`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ paid }),
        }
    );
    return handleFetchResponse(response);
};

// 8. Change Pre-Invoice Sent Status
export const changePreInvoiceSentStatus = async (companyUuid: string, preInvoiceUuid: string, sent: boolean) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/preinvoice/${preInvoiceUuid}/sent`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ sent }),
        }
    );
    return handleFetchResponse(response);
};

// 9. Get Pre-Invoice History
export const getPreInvoiceHistory = async (companyUuid: string, preInvoiceUuid: string) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/preinvoice/${preInvoiceUuid}/history`,
        { headers: getHeaders() }
    );
    return handleFetchResponse(response);
};

// Helper function to build pre-invoice payload from form data
export const buildPreInvoicePayload = (formData: any, companyUuid: string) => {
    return {
        number: formData.number,
        billed_date: formData.billed_date,
        due_date: formData.due_date,
        send_date: formData.send_date,
        variable_symbol: formData.variable_symbol || '',
        constant_symbol: formData.constant_symbol || '',
        specific_symbol: formData.specific_symbol || '',
        order_id: formData.order_id || null,
        billed_client_id: formData.billed_client_id,
        items: formData.items || [],
        payment: formData.payment || 'CASH',
        cash_payment_rounding: formData.cash_payment_rounding || 0,
        bank_transfer: formData.bank_transfer || {},
        note: formData.note || '',
        totalPrice: formData.totalPrice || 0,
        currency_3_code: formData.currency_3_code || 'EUR',
        language_2_code: formData.language_2_code || 'en',
        template: formData.template || '',
    };
};
