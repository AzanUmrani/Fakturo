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

// Helper function to check if current URL is for pre-invoices
const isPreInvoiceUrl = (): boolean => {
    return window.location.pathname.includes('pre-invoices');
};

// ==================== INVOICE APIs ====================

// 1. Create Invoice
export const createInvoice = async (companyUuid: string, data: any) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/invoice`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(data),
        }
    );
    return handleFetchResponse(response);
};

// 2. Create Invoice Preview (POST with data to preview before creating)
export const createInvoicePreview = async (companyUuid: string, data: any) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/invoice/createFutureInvoicePreview`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(data),
        }
    );
    return handleFetchResponse(response);
};

// 3. Get Invoice Preview PDF (GET - displays preview without creating)
export const getInvoicePdfPreview = (companyUuid: string) => {
    const url = `${API_BASE_URL}/user/company/${companyUuid}/invoice/getFutureInvoicePreview`;
    window.open(url, '_blank');
};

// 4. Edit Invoice
export const editInvoice = async (companyUuid: string, invoiceUuid: string, data: any) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/invoice/${invoiceUuid}`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(data),
        }
    );
    return handleFetchResponse(response);
};

// 5. Get Invoice
export const getInvoice = async (companyUuid: string, invoiceUuid: string) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/invoice/${invoiceUuid}`,
        { headers: getHeaders() }
    );
    return handleFetchResponse(response);
};

// 6. List All Invoices
export const listInvoices = async (companyUuid: string, params?: any) => {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString 
        ? `${API_BASE_URL}/user/company/${companyUuid}/invoices?${queryString}`
        : `${API_BASE_URL}/user/company/${companyUuid}/invoices`;
    
    const response = await fetch(url, { headers: getHeaders() });
    const data = await handleFetchResponse(response);
    
    // If URL contains 'pre-invoices', filter only pre-invoices
    if (isPreInvoiceUrl()) {
        if (data?.data) {
            data.data = data.data.filter((invoice: any) => 
                invoice.pre_invoice === true || invoice.pre_invoice == 1
            );
        }
    } else {
        // Otherwise filter only regular invoices (pre_invoice is false or not set)
        if (data?.data) {
            data.data = data.data.filter((invoice: any) => 
                !invoice.pre_invoice || invoice.pre_invoice === false || invoice.pre_invoice == 0
            );
        }
    }
    
    return data;
};

// 7. Delete Invoice
export const deleteInvoice = async (companyUuid: string, invoiceUuid: string) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/invoice/${invoiceUuid}`,
        {
            method: 'DELETE',
            headers: getHeaders(),
        }
    );
    return handleFetchResponse(response);
};

// 8. Generate PDF (Background task - generates PDF file on server)
export const generatePdf = async (companyUuid: string, invoiceUuid: string) => {
    try {
        console.log(`[PDF] Generating PDF for invoice: ${invoiceUuid}`);
        const response = await fetch(
            `${API_BASE_URL}/user/company/${companyUuid}/invoice/${invoiceUuid}/pdfGenerate`,
            {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify({}),
            }
        );
        const data = await handleFetchResponse(response);
        console.log(`[PDF] Generation response:`, data);
        return data;
    } catch (error: any) {
        console.error(`[PDF] Generation failed:`, error);
        throw error;
    }
};

// 9. Get Invoice PDF (Download generated PDF with retry)
export const getInvoicePdf = async (
    companyUuid: string,
    invoiceUuid: string,
    maxRetries: number = 3
) => {
    let lastError: any = null;

    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            console.log(
                `[PDF Download] Attempt ${attempt}/${maxRetries} for invoice: ${invoiceUuid}`
            );

            // Step 1: Generate PDF
            console.log(`[PDF Download] Step 1: Generating PDF...`);
            try {
                await generatePdf(companyUuid, invoiceUuid);
            } catch (genError: any) {
                console.warn(
                    `[PDF Download] Generation warning (continuing anyway):`,
                    genError?.message || 'Unknown error'
                );
                // Don't throw - PDF might already exist
            }

            // Step 2: Wait for PDF generation (increase wait time on retries)
            const waitTime = 1000 + (attempt - 1) * 1000; // 1s, 2s, 3s
            console.log(`[PDF Download] Step 2: Waiting ${waitTime}ms for generation...`);
            await new Promise(resolve => setTimeout(resolve, waitTime));

            // Step 3: Download PDF
            console.log(`[PDF Download] Step 3: Downloading PDF...`);
            const url = `${API_BASE_URL}/user/company/${companyUuid}/invoice/${invoiceUuid}/pdf`;
            
            // Use fetch to check if PDF exists before opening
            try {
                const headResponse = await fetch(url, {
                    method: 'HEAD',
                    headers: getHeaders(),
                });
                
                if (!headResponse.ok) {
                    if (headResponse.status === 404) {
                        lastError = {
                            code: 'INVOICE_PDF_NOT_FOUND',
                            message: 'PDF not generated yet',
                            status: 404,
                        };
                        console.warn(
                            `[PDF Download] PDF not found (404) on attempt ${attempt}, retrying...`
                        );
                        if (attempt < maxRetries) {
                            continue;
                        }
                    } else {
                        throw new Error(`HTTP ${headResponse.status}`);
                    }
                } else {
                    console.log(`[PDF Download] PDF found, opening in new tab...`);
                    window.open(url, '_blank');
                    console.log(`[PDF Download] ✅ Success on attempt ${attempt}`);
                    return;
                }
            } catch (error: any) {
                console.error(`[PDF Download] Fetch error:`, error);
                lastError = error;
                if (attempt < maxRetries) {
                    continue;
                }
            }
        } catch (error: any) {
            console.error(`[PDF Download] Attempt ${attempt} failed:`, error);
            lastError = error;

            if (attempt === maxRetries) {
                break;
            }
        }
    }

    // All retries exhausted
    console.error(`[PDF Download] ❌ All ${maxRetries} attempts failed`);
    throw {
        code: lastError?.code || 'PDF_GENERATION_FAILED',
        message:
            lastError?.message ||
            'Failed to generate and download PDF after multiple retries',
        details: lastError,
    };
};

// 10. Get Invoice Receipt PDF
export const getInvoiceReceiptPdf = (companyUuid: string, invoiceUuid: string) => {
    const url = `${API_BASE_URL}/user/company/${companyUuid}/invoice/${invoiceUuid}/receipt/pdf`;
    window.open(url, '_blank');
};

// 11. Get PDF Bulk (Download multiple invoice PDFs as ZIP)
export const getPdfBulk = async (
    companyUuid: string,
    invoiceUuidList: string[],
    maxRetries: number = 3
) => {
    let lastError: any = null;

    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            console.log(
                `[PDF Bulk] Attempt ${attempt}/${maxRetries} for ${invoiceUuidList.length} invoices`
            );

            console.log(`[PDF Bulk] Step 1: Generating PDFs in parallel...`);
            // Generate all PDFs in parallel
            const generatePromises = invoiceUuidList.map(invoiceUuid =>
                generatePdf(companyUuid, invoiceUuid)
                    .then(() => {
                        console.log(`[PDF Bulk] ✓ Generated: ${invoiceUuid}`);
                        return { uuid: invoiceUuid, success: true };
                    })
                    .catch(error => {
                        console.warn(`[PDF Bulk] ⚠ Failed to generate ${invoiceUuid}:`, error);
                        return { uuid: invoiceUuid, success: false };
                    })
            );

            const results = await Promise.all(generatePromises);
            const successCount = results.filter(r => r.success).length;
            console.log(
                `[PDF Bulk] Generation complete: ${successCount}/${invoiceUuidList.length} successful`
            );

            if (successCount === 0) {
                throw new Error('No PDFs generated successfully');
            }

            // Step 2: Wait for all PDFs to be written
            const waitTime = 2000 + (attempt - 1) * 1000; // 2s, 3s, 4s
            console.log(`[PDF Bulk] Step 2: Waiting ${waitTime}ms for all files...`);
            await new Promise(resolve => setTimeout(resolve, waitTime));

            // Step 3: Download bulk ZIP
            console.log(`[PDF Bulk] Step 3: Downloading bulk ZIP...`);
            const url = `${API_BASE_URL}/user/company/${companyUuid}/invoices/pdfBulk?invoiceUuidList=${invoiceUuidList.join(',')}`;

            // Check if ZIP is accessible
            try {
                const headResponse = await fetch(url, {
                    method: 'HEAD',
                    headers: getHeaders(),
                });
                
                if (!headResponse.ok) {
                    if (headResponse.status === 404) {
                        lastError = {
                            code: 'INVOICE_PDF_BULK_NOT_FOUND',
                            message: 'Bulk ZIP not generated yet',
                            status: 404,
                        };
                        console.warn(
                            `[PDF Bulk] ZIP not found (404) on attempt ${attempt}, retrying...`
                        );
                        if (attempt < maxRetries) {
                            continue;
                        }
                    } else {
                        throw new Error(`HTTP ${headResponse.status}`);
                    }
                } else {
                    console.log(`[PDF Bulk] ZIP found, opening download...`);
                    window.open(url, '_blank');
                    console.log(`[PDF Bulk] ✅ Success on attempt ${attempt}`);
                    return;
                }
            } catch (error: any) {
                console.error(`[PDF Bulk] Fetch error:`, error);
                lastError = error;
                if (attempt < maxRetries) {
                    continue;
                }
            }
        } catch (error: any) {
            console.error(`[PDF Bulk] Attempt ${attempt} failed:`, error);
            lastError = error;

            if (attempt === maxRetries) {
                break;
            }
        }
    }

    console.error(`[PDF Bulk] ❌ All ${maxRetries} attempts failed`);
    throw {
        code: lastError?.code || 'PDF_BULK_GENERATION_FAILED',
        message:
            lastError?.message ||
            'Failed to generate bulk PDF after multiple retries',
        details: lastError,
    };
};

// 12. Change Invoice Paid Status
export const changePaidStatus = async (companyUuid: string, invoiceUuid: string, paid: boolean) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/invoice/${invoiceUuid}/paid`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ paid }),
        }
    );
    return handleFetchResponse(response);
};

// 13. Change Invoice Sent Status
export const changeSentStatus = async (companyUuid: string, invoiceUuid: string, sent: boolean) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/invoice/${invoiceUuid}/sent`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ sent }),
        }
    );
    return handleFetchResponse(response);
};

// 14. Create Receipt for Invoice
export const createReceipt = async (companyUuid: string, invoiceUuid: string, data?: any) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/invoice/${invoiceUuid}/receipt`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(data || {}),
        }
    );
    return handleFetchResponse(response);
};

// 15. Get Receipt PDF
export const getReceiptPdf = (companyUuid: string, invoiceUuid: string, receiptUuid: string) => {
    const url = `${API_BASE_URL}/user/company/${companyUuid}/invoice/${invoiceUuid}/receipt/${receiptUuid}`;
    window.open(url, '_blank');
};

// ==================== PRE-INVOICE APIs ====================

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
    const data = await handleFetchResponse(response);
    
    // Filter only pre-invoices (pre_invoice is true)
    if (data?.data) {
        data.data = data.data.filter((invoice: any) => 
            invoice.pre_invoice === true || invoice.pre_invoice == 1
        );
    }
    
    return data;
};

// 4. Get Pre-Invoice PDF
export const getPreInvoicePdf = (companyUuid: string, preInvoiceUuid: string) => {
    const url = `${API_BASE_URL}/user/company/${companyUuid}/preinvoice/${preInvoiceUuid}/pdf`;
    window.open(url, '_blank');
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

// ==================== RECURRENT INVOICE APIs ====================

// 1. Create Recurrent Invoice
export const createRecurrentInvoice = async (companyUuid: string, data: any) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/recurrent/invoice`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(data),
        }
    );
    return handleFetchResponse(response);
};

// 2. Delete Recurrent Invoice
export const deleteRecurrentInvoice = async (companyUuid: string, recurrentInvoiceUuid: string) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/recurrent/invoice/${recurrentInvoiceUuid}`,
        {
            method: 'DELETE',
            headers: getHeaders(),
        }
    );
    return handleFetchResponse(response);
};

// ==================== ESTIMATE/QUOTE APIs ====================

// 1. Create Estimate
export const createEstimate = async (companyUuid: string, data: any) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/estimate`,
        {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(data),
        }
    );
    return handleFetchResponse(response);
};

// 2. List All Estimates
export const listEstimates = async (companyUuid: string, params?: any) => {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString 
        ? `${API_BASE_URL}/user/company/${companyUuid}/estimates?${queryString}`
        : `${API_BASE_URL}/user/company/${companyUuid}/estimates`;
    
    const response = await fetch(url, { headers: getHeaders() });
    return handleFetchResponse(response);
};

// 3. Delete Estimate
export const deleteEstimate = async (companyUuid: string, estimateUuid: string) => {
    const response = await fetch(
        `${API_BASE_URL}/user/company/${companyUuid}/estimate/${estimateUuid}`,
        {
            method: 'DELETE',
            headers: getHeaders(),
        }
    );
    return handleFetchResponse(response);
};