import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useLang } from '@/hooks/useLang';
import { type Invoice } from '@/types';
import { router } from '@inertiajs/react';
import { Edit, FileDown, Trash, Loader } from 'lucide-react';
import { format } from 'date-fns';
import { changePaidStatus, changeSentStatus, getInvoicePdf } from '@/services/invoiceApi';
import { useState } from 'react';

interface InvoicesTableProps {
    invoices: Invoice[];
    companyUuid: string;
    filters: {
        search?: string;
        sort_field?: string;
        sort_direction?: string;
    };
    onEdit: (invoice: Invoice) => void;
    onDelete: (invoice: Invoice) => void;
    onStatusChange: () => void;
}

export default function InvoicesTable({
    invoices,
    companyUuid,
    filters,
    onEdit,
    onDelete,
    onStatusChange,
}: InvoicesTableProps) {
    const { __ } = useLang();
    const [loadingPaidId, setLoadingPaidId] = useState<string | null>(null);
    const [loadingSentId, setLoadingSentId] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [downloadingId, setDownloadingId] = useState<string | null>(null);
    const [downloadError, setDownloadError] = useState<string | null>(null);

    const invoiceArray = Array.isArray(invoices) ? invoices : Object.values(invoices);

    const handlePaidChange = async (invoice: Invoice) => {
        try {
            setLoadingPaidId(invoice.uuid);
            await changePaidStatus(companyUuid, invoice.uuid, !invoice.paid);
            const statusText = !invoice.paid ? __('invoices.status.paid') : 'Unpaid';
            setSuccessMessage(`✓ Invoice marked as ${statusText}`);
            setTimeout(() => setSuccessMessage(null), 3000);
            onStatusChange();
        } catch (error: any) {
            const errorMsg = error?.message || 'Failed to update status';
            setSuccessMessage(`✗ Error: ${errorMsg}`);
            setTimeout(() => setSuccessMessage(null), 3000);
            console.error('Error changing paid status:', error);
        } finally {
            setLoadingPaidId(null);
        }
    };

    const handleSentChange = async (invoice: Invoice) => {
        try {
            setLoadingSentId(invoice.uuid);
            await changeSentStatus(companyUuid, invoice.uuid, !invoice.sent);
            const statusText = !invoice.sent ? __('invoices.status.sent') : 'Not sent';
            setSuccessMessage(`✓ Invoice marked as ${statusText}`);
            setTimeout(() => setSuccessMessage(null), 3000);
            onStatusChange();
        } catch (error: any) {
            const errorMsg = error?.message || 'Failed to update status';
            setSuccessMessage(`✗ Error: ${errorMsg}`);
            setTimeout(() => setSuccessMessage(null), 3000);
            console.error('Error changing sent status:', error);
        } finally {
            setLoadingSentId(null);
        }
    };

    const handleSort = (field: string) => {
        const direction =
            filters.sort_field === field && filters.sort_direction === 'asc'
                ? 'desc'
                : 'asc';

        router.get(
            route('documents.invoices.index'),
            { ...filters, sort_field: field, sort_direction: direction },
            {
                preserveState: true,
                preserveScroll: true,
                only: ['invoices', 'filters'],
            }
        );
    };

    const getSortIcon = (field: string) => {
        if (filters.sort_field !== field) {
            return null;
        }

        return filters.sort_direction === 'asc' ? '↑' : '↓';
    };

    const formatDate = (dateString: string) => {
        try {
            return format(new Date(dateString), 'dd.MM.yyyy');
        } catch (e) {
            return dateString;
        }
    };

    const formatPrice = (price: number, currencySymbol: string) => {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(price) + ' ' + currencySymbol;
    };

    const downloadInvoice = async (invoice: Invoice) => {
        try {
            setDownloadingId(invoice.uuid);
            setDownloadError(null);
            console.log(`[UI] Starting PDF download for invoice: ${invoice.uuid}`);
            await getInvoicePdf(companyUuid, invoice.uuid);
            console.log(`[UI] PDF download completed for invoice: ${invoice.uuid}`);
        } catch (error: any) {
            console.error(`[UI] PDF download failed:`, error);
            const errorCode = error?.code || error?.response?.data?.error || 'UNKNOWN_ERROR';
            const errorMessage =
                error?.message ||
                error?.response?.data?.message ||
                'Failed to download PDF. Please try again.';
            setDownloadError(`${errorCode}: ${errorMessage}`);
            setTimeout(() => setDownloadError(null), 5000);
        } finally {
            setDownloadingId(null);
        }
    };

    return (
        <div className="space-y-4">
            {successMessage && (
                <div className={`rounded-lg border-l-4 p-4 ${
                    successMessage.startsWith('✓')
                        ? 'border-green-500 bg-green-50'
                        : 'border-red-500 bg-red-50'
                }`}>
                    <p className={`text-sm font-medium ${
                        successMessage.startsWith('✓')
                            ? 'text-green-800'
                            : 'text-red-800'
                    }`}>
                        {successMessage}
                    </p>
                </div>
            )}

            {downloadError && (
                <div className="rounded-lg border-l-4 border-red-500 bg-red-50 p-4">
                    <p className="text-sm font-medium text-red-800">{downloadError}</p>
                    <p className="mt-1 text-xs text-red-700">
                        If this persists, check if the invoice has been saved properly.
                    </p>
                </div>
            )}

            <div className="rounded-md border">
                <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead
                            className="cursor-pointer"
                            onClick={() => handleSort('number')}
                        >
                            {__('invoices.table.number')} {getSortIcon('number')}
                        </TableHead>
                        <TableHead
                            className="cursor-pointer"
                            onClick={() => handleSort('billed_date')}
                        >
                            {__('invoices.table.billed_date')} {getSortIcon('billed_date')}
                        </TableHead>
                        <TableHead
                            className="cursor-pointer"
                            onClick={() => handleSort('due_date')}
                        >
                            {__('invoices.table.due_date')} {getSortIcon('due_date')}
                        </TableHead>
                        <TableHead>
                            {__('invoices.table.client')}
                        </TableHead>
                        <TableHead className="text-right">
                            {__('invoices.table.total')}
                        </TableHead>
                        <TableHead className="text-right">
                            {__('invoices.table.status')}
                        </TableHead>
                        <TableHead className="text-right">
                            {__('invoices.table.actions')}
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {!Array.isArray(invoiceArray) || invoiceArray.length === 0 ? (
                        <TableRow>
                            <TableCell
                                colSpan={7}
                                className="h-24 text-center"
                            >
                                {__('invoices.table.no_invoices')}
                            </TableCell>
                        </TableRow>
                    ) : (
                        invoiceArray.map((invoice) => {
                            // console.log('Mapping invoice:', invoice);
                            return (
                            <TableRow key={invoice.id}>
                                <TableCell className="font-medium">
                                    {invoice.number}
                                </TableCell>
                                <TableCell>
                                    {formatDate(invoice.billed_date)}
                                </TableCell>
                                <TableCell>
                                    {formatDate(invoice.due_date)}
                                </TableCell>
                                <TableCell>
                                    {invoice.billed_to_client?.name || ''}
                                </TableCell>
                                <TableCell className="text-right">
                                    {formatPrice(
                                        invoice.totalPrice_with_tax,
                                        invoice.currency_symbol || ''
                                    )}
                                </TableCell>
                                <TableCell className="text-right">
                                    <div className="flex justify-end gap-2">
                                        <button
                                            onClick={() => handlePaidChange(invoice)}
                                            disabled={loadingPaidId === invoice.uuid}
                                            className={`inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset transition-all ${
                                                loadingPaidId === invoice.uuid
                                                    ? 'opacity-60 cursor-not-allowed'
                                                    : ''
                                            } ${
                                                invoice.paid
                                                    ? 'bg-green-50 text-green-700 ring-green-600/20 hover:bg-green-100'
                                                    : 'bg-gray-50 text-gray-700 ring-gray-600/20 hover:bg-gray-100'
                                            }`}
                                        >
                                            {loadingPaidId === invoice.uuid ? (
                                                <>
                                                    <Loader className="mr-1 h-3 w-3 animate-spin" />
                                                    Processing...
                                                </>
                                            ) : (
                                                __('invoices.status.paid')
                                            )}
                                        </button>
                                        <button
                                            onClick={() => handleSentChange(invoice)}
                                            disabled={loadingSentId === invoice.uuid}
                                            className={`inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset transition-all ${
                                                loadingSentId === invoice.uuid
                                                    ? 'opacity-60 cursor-not-allowed'
                                                    : ''
                                            } ${
                                                invoice.sent
                                                    ? 'bg-blue-50 text-blue-700 ring-blue-600/20 hover:bg-blue-100'
                                                    : 'bg-gray-50 text-gray-700 ring-gray-600/20 hover:bg-gray-100'
                                            }`}
                                        >
                                            {loadingSentId === invoice.uuid ? (
                                                <>
                                                    <Loader className="mr-1 h-3 w-3 animate-spin" />
                                                    Processing...
                                                </>
                                            ) : (
                                                __('invoices.status.sent')
                                            )}
                                        </button>
                                        {!invoice.paid && !invoice.sent && (
                                            <span className="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">
                                                {__('invoices.status.draft')}
                                            </span>
                                        )}
                                    </div>
                                </TableCell>
                                <TableCell className="text-right">
                                    <div className="flex justify-end gap-2">
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            onClick={() => downloadInvoice(invoice)}
                                            disabled={downloadingId === invoice.uuid}
                                            title={__('invoices.actions.download')}
                                        >
                                            {downloadingId === invoice.uuid ? (
                                                <Loader className="h-4 w-4 animate-spin" />
                                            ) : (
                                                <FileDown className="h-4 w-4" />
                                            )}
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            onClick={() => onEdit(invoice)}
                                            title={__('invoices.actions.edit')}
                                        >
                                            <Edit className="h-4 w-4" />
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            onClick={() => onDelete(invoice)}
                                            title={__('invoices.actions.delete')}
                                        >
                                            <Trash className="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        );
                    })
                )}
                </TableBody>
                </Table>
            </div>
        </div>
    );
}
