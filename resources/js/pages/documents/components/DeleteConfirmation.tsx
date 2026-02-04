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
import { type Invoice } from '@/types';
import { router } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';

interface DeleteConfirmationProps {
    isOpen: boolean;
    onOpenChange: (open: boolean) => void;
    invoice: Invoice | null;
    isDeleting: boolean;
    setIsDeleting: (isDeleting: boolean) => void;
}

export default function DeleteConfirmation({
    isOpen,
    onOpenChange,
    invoice,
    isDeleting,
    setIsDeleting,
}: DeleteConfirmationProps) {
    const { __ } = useLang();

    const handleDelete = () => {
        if (!invoice) return;

        setIsDeleting(true);

        router.delete(route('documents.invoices.destroy', invoice.uuid), {
            onSuccess: () => {
                onOpenChange(false);
                setIsDeleting(false);
            },
            onError: () => {
                setIsDeleting(false);
            },
        });
    };

    return (
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{__('invoices.delete_confirmation_title')}</DialogTitle>
                    <DialogDescription>
                        {__('invoices.delete_confirmation_description')}
                    </DialogDescription>
                </DialogHeader>

                <div className="py-4">
                    <p>
                        {__('invoices.delete_confirmation_message', {
                            number: invoice?.number || '',
                        })}
                    </p>
                </div>

                <DialogFooter>
                    <Button
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                        disabled={isDeleting}
                    >
                        {__('common.cancel')}
                    </Button>
                    <Button
                        variant="destructive"
                        onClick={handleDelete}
                        disabled={isDeleting}
                    >
                        {isDeleting ? (
                            <>
                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                {__('common.deleting')}
                            </>
                        ) : (
                            __('common.delete')
                        )}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
