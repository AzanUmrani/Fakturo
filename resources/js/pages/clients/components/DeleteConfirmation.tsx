import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { type Client } from '@/types';
import { router } from '@inertiajs/react';
import { useLang } from '@/hooks/useLang';

interface DeleteConfirmationProps {
    isOpen: boolean;
    onOpenChange: (open: boolean) => void;
    client: Client | null;
    isDeleting: boolean;
    setIsDeleting: (isDeleting: boolean) => void;
}

export default function DeleteConfirmation({
    isOpen,
    onOpenChange,
    client,
    isDeleting,
    setIsDeleting
}: DeleteConfirmationProps) {
    const { __ } = useLang();

    const confirmDelete = () => {
        if (!client) return;

        setIsDeleting(true);
        router.delete(route('clients.destroy', client.id), {
            onSuccess: () => {
                onOpenChange(false);
            },
            onFinish: () => {
                setIsDeleting(false);
            }
        });
    };

    return (
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle className="text-destructive">{__('clients.delete_client')}</DialogTitle>
                    <DialogDescription>
                        {__('clients.delete_confirm_message', { name: client?.name || '' })}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter className="gap-2 sm:gap-0">
                    <Button
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                        disabled={isDeleting}
                    >
                        {__('clients.buttons.cancel')}
                    </Button>
                    <Button
                        variant="destructive"
                        onClick={confirmDelete}
                        disabled={isDeleting}
                    >
                        {isDeleting && (
                            <svg className="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        )}
                        {__('clients.buttons.delete')}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
