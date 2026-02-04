/**
 * Invoice Utils - Helper functions for common invoice operations
 * This file contains pre-built flows for common tasks
 */

import {
    createInvoice,
    createInvoicePreview,
    editInvoice,
    getInvoice,
    listInvoices,
    deleteInvoice,
    generatePdf,
    getInvoicePdf,
    getPdfBulk,
    changePaidStatus,
    changeSentStatus,
    createReceipt,
    createPreInvoice,
    createRecurrentInvoice,
    createEstimate,
    listEstimates,
} from './invoiceApi';

/**
 * Flow 1: Create Invoice with Preview
 * Step 1: Preview invoice (visual check)
 * Step 2: Create actual invoice if confirmed
 */
export async function createInvoiceWithPreview(
    companyUuid: string,
    invoiceData: any
): Promise<any> {
    try {
        // Step 1: Generate preview
        console.log('📋 Previewing invoice...');
        await createInvoicePreview(companyUuid, invoiceData);

        // Step 2: Wait for user confirmation (handle in UI)
        // This function returns preview data, UI handles confirmation

        // Step 3: Create actual invoice
        console.log('✅ Creating invoice...');
        const createdInvoice = await createInvoice(companyUuid, invoiceData);

        console.log('🎉 Invoice created successfully:', createdInvoice);
        return createdInvoice;
    } catch (error) {
        console.error('❌ Error creating invoice:', error);
        throw error;
    }
}

/**
 * Flow 2: Download Single Invoice PDF
 * Complete flow: Generate → Wait → Download
 */
export async function downloadInvoicePdf(
    companyUuid: string,
    invoiceUuid: string
): Promise<void> {
    try {
        console.log('📄 Downloading PDF for invoice:', invoiceUuid);
        await getInvoicePdf(companyUuid, invoiceUuid);
        console.log('✅ PDF download initiated');
    } catch (error) {
        console.error('❌ Error downloading PDF:', error);
        throw error;
    }
}

/**
 * Flow 3: Download Multiple Invoice PDFs as ZIP
 * Complete flow: Generate all → Wait → Download ZIP
 */
export async function downloadMultipleInvoicesPdf(
    companyUuid: string,
    invoiceUuids: string[]
): Promise<void> {
    try {
        console.log('📦 Downloading PDFs for invoices:', invoiceUuids);

        if (invoiceUuids.length === 0) {
            throw new Error('No invoices selected');
        }

        if (invoiceUuids.length === 1) {
            // Single invoice - use regular download
            await downloadInvoicePdf(companyUuid, invoiceUuids[0]);
        } else {
            // Multiple invoices - use bulk download
            await getPdfBulk(companyUuid, invoiceUuids);
        }

        console.log('✅ Bulk PDF download initiated');
    } catch (error) {
        console.error('❌ Error bulk downloading PDFs:', error);
        throw error;
    }
}

/**
 * Flow 4: Mark Invoice as Paid
 */
export async function markInvoiceAsPaid(
    companyUuid: string,
    invoiceUuid: string
): Promise<void> {
    try {
        console.log('💰 Marking invoice as paid:', invoiceUuid);
        await changePaidStatus(companyUuid, invoiceUuid, true);
        console.log('✅ Invoice marked as paid');
    } catch (error) {
        console.error('❌ Error marking invoice as paid:', error);
        throw error;
    }
}

/**
 * Flow 5: Mark Invoice as Sent
 */
export async function markInvoiceAsSent(
    companyUuid: string,
    invoiceUuid: string
): Promise<void> {
    try {
        console.log('📤 Marking invoice as sent:', invoiceUuid);
        await changeSentStatus(companyUuid, invoiceUuid, true);
        console.log('✅ Invoice marked as sent');
    } catch (error) {
        console.error('❌ Error marking invoice as sent:', error);
        throw error;
    }
}

/**
 * Flow 6: Create Receipt for Invoice
 */
export async function createReceiptForInvoice(
    companyUuid: string,
    invoiceUuid: string,
    receiptData?: any
): Promise<any> {
    try {
        console.log('🧾 Creating receipt for invoice:', invoiceUuid);
        const receipt = await createReceipt(companyUuid, invoiceUuid, receiptData);
        console.log('✅ Receipt created successfully:', receipt);
        return receipt;
    } catch (error) {
        console.error('❌ Error creating receipt:', error);
        throw error;
    }
}

/**
 * Flow 7: Edit Invoice
 */
export async function updateInvoice(
    companyUuid: string,
    invoiceUuid: string,
    updates: any
): Promise<any> {
    try {
        console.log('📝 Updating invoice:', invoiceUuid);

        // Get current invoice first
        const currentInvoice = await getInvoice(companyUuid, invoiceUuid);

        // Merge updates
        const updatedData = { ...currentInvoice, ...updates };

        // Send to API
        const result = await editInvoice(companyUuid, invoiceUuid, updatedData);
        console.log('✅ Invoice updated successfully:', result);
        return result;
    } catch (error) {
        console.error('❌ Error updating invoice:', error);
        throw error;
    }
}

/**
 * Flow 8: Delete Invoice
 */
export async function removeInvoice(
    companyUuid: string,
    invoiceUuid: string
): Promise<void> {
    try {
        console.log('🗑️ Deleting invoice:', invoiceUuid);

        // Confirm deletion (handle in UI with dialog)
        const confirmed = window.confirm(
            'Are you sure you want to delete this invoice?'
        );

        if (!confirmed) {
            console.log('❌ Deletion cancelled');
            return;
        }

        await deleteInvoice(companyUuid, invoiceUuid);
        console.log('✅ Invoice deleted successfully');
    } catch (error) {
        console.error('❌ Error deleting invoice:', error);
        throw error;
    }
}

/**
 * Flow 9: Fetch All Invoices with Sorting
 */
export async function fetchInvoices(
    companyUuid: string,
    sortField: string = 'billed_date',
    sortDirection: 'asc' | 'desc' = 'desc'
): Promise<any[]> {
    try {
        console.log('📋 Fetching invoices...');
        const invoices = await listInvoices(companyUuid, {
            sort_field: sortField,
            sort_direction: sortDirection,
        });
        console.log(`✅ Fetched ${invoices.length} invoices`);
        return invoices;
    } catch (error) {
        console.error('❌ Error fetching invoices:', error);
        throw error;
    }
}

/**
 * Flow 10: Create Pre-Invoice (Quote/Estimate)
 */
export async function createPreInvoiceDocument(
    companyUuid: string,
    preInvoiceData: any
): Promise<any> {
    try {
        console.log('📊 Creating pre-invoice...');
        const preInvoice = await createPreInvoice(companyUuid, preInvoiceData);
        console.log('✅ Pre-invoice created successfully:', preInvoice);
        return preInvoice;
    } catch (error) {
        console.error('❌ Error creating pre-invoice:', error);
        throw error;
    }
}

/**
 * Flow 11: Create Recurrent Invoice (Subscription)
 */
export async function createRecurringInvoice(
    companyUuid: string,
    recurringData: any
): Promise<any> {
    try {
        console.log('🔄 Creating recurrent invoice...');
        const recurrent = await createRecurrentInvoice(
            companyUuid,
            recurringData
        );
        console.log('✅ Recurrent invoice created successfully:', recurrent);
        return recurrent;
    } catch (error) {
        console.error('❌ Error creating recurrent invoice:', error);
        throw error;
    }
}

/**
 * Flow 12: Create Estimate (Quote)
 */
export async function createEstimateDocument(
    companyUuid: string,
    estimateData: any
): Promise<any> {
    try {
        console.log('📋 Creating estimate...');
        const estimate = await createEstimate(companyUuid, estimateData);
        console.log('✅ Estimate created successfully:', estimate);
        return estimate;
    } catch (error) {
        console.error('❌ Error creating estimate:', error);
        throw error;
    }
}

/**
 * Helper: Build invoice data object
 */
export function buildInvoiceData(formData: any): any {
    return {
        number: formData.number,
        billed_date: formData.billedDate,
        due_date: formData.dueDate,
        send_date: formData.sendDate,

        variable_symbol: formData.variableSymbol || '',
        constant_symbol: formData.constantSymbol || '',
        specific_symbol: formData.specificSymbol || '',

        order_id: formData.orderId || null,

        billed_client_id: formData.billedClientId,

        items: formData.items.map((item: any) => ({
            name: item.name,
            quantity: item.quantity,
            price: item.price,
            unit: item.unit || '',
            taxRate: item.taxRate || null,
            priceType: item.priceType || 'positive',
            uuid: item.uuid || null,
        })),

        payment: formData.paymentMethod,
        cash_payment_rounding: formData.cashRounding || 0,
        bank_transfer: formData.bankTransfer || {},

        note: formData.note || '',

        totalPrice: formData.totalPrice,
        currency_3_code: formData.currencyCode,
        language_2_code: formData.languageCode,

        template: formData.template || '',
    };
}

/**
 * Helper: Validate invoice data
 */
export function validateInvoiceData(data: any): {
    valid: boolean;
    errors: string[];
} {
    const errors: string[] = [];

    if (!data.number) errors.push('Invoice number is required');
    if (!data.billed_date) errors.push('Billed date is required');
    if (!data.due_date) errors.push('Due date is required');
    if (!data.billed_client_id) errors.push('Client is required');
    if (!data.items || data.items.length === 0)
        errors.push('At least one item is required');
    if (!data.totalPrice || data.totalPrice <= 0)
        errors.push('Total price must be greater than 0');
    if (!data.currency_3_code) errors.push('Currency is required');
    if (!data.language_2_code) errors.push('Language is required');

    return {
        valid: errors.length === 0,
        errors,
    };
}
