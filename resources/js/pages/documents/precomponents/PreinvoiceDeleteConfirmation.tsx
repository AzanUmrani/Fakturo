import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useLang } from '@/hooks/useLang';
import { type PreInvoice } from '../preinvoices';
import { Loader2, AlertCircle } from 'lucide-react';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { deletePreInvoice } from '@/services/preInvoiceApi';
import { router } from '@inertiajs/react';
import { useState, useEffect } from 'react';

interface PreInvoiceDeleteConfirmationProps {
    isOpen: boolean;
    onOpenChange: (open: boolean) => void;
    invoice: PreInvoice | null;
    isDeleting: boolean;
    setIsDeleting: (isDeleting: boolean) => void;
}

export default function PreInvoiceDeleteConfirmation({
    isOpen,
    onOpenChange,
    invoice,
    isDeleting,
    setIsDeleting,
}: PreInvoiceDeleteConfirmationProps) {
    const { __ } = useLang();
    const [companyUuid] = useState(() => {
        // Try to get from localStorage first
        const stored = localStorage.getItem('companyUuid');
        if (stored) return stored;
        
        // Fallback to URL path
        const pathParts = window.location.pathname.split('/');
        return pathParts[3] || '';
    });
    const [error, setError] = useState<string | null>(null);

    const handleDelete = () => {
        if (!invoice) return;

        setIsDeleting(true);
        setError(null);

        deletePreInvoice(companyUuid, invoice.uuid)
            .then(() => {
                setIsDeleting(false);
                onOpenChange(false);
                setError(null);
                // Refresh the page data
                router.reload({ only: ['preInvoices'] });
            })
            .catch((err) => {
                console.error('Failed to delete pre-invoice:', err);
                setError(err.response?.data?.message || 'Failed to delete pre-invoice');
                setIsDeleting(false);
            });
    };

    return (
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Delete Pre Invoice</DialogTitle>
                    <DialogDescription>
                        This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>

                {error && (
                    <Alert variant="destructive">
                        <AlertCircle className="h-4 w-4" />
                        <AlertDescription>{error}</AlertDescription>
                    </Alert>
                )}

                <div className="py-4">
                    <p>
                        Are you sure you want to delete pre invoice {invoice?.number}?
                    </p>
                </div>

                <DialogFooter>
                    <Button
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                        disabled={isDeleting}
                    >
                        Cancel
                    </Button>
                    <Button
                        variant="destructive"
                        onClick={handleDelete}
                        disabled={isDeleting}
                    >
                        {isDeleting ? (
                            <>
                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                Deleting
                            </>
                        ) : (
                            'Delete'
                        )}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
