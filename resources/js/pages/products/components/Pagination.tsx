import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { useLang } from '@/hooks/useLang';

interface PaginationProps {
    from: number;
    to: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

export default function Pagination({
    from,
    to,
    total,
    links
}: PaginationProps) {
    const { __ } = useLang();

    const handlePageChange = (url: string | null) => {
        if (!url) return;

        // Extract query parameters from the URL
        const urlObj = new URL(url);
        const params: Record<string, string> = {};
        urlObj.searchParams.forEach((value, key) => {
            params[key] = value;
        });

        // Navigate using Inertia
        router.get(route('products.index'), params, {
            preserveState: true,
            preserveScroll: true,
            only: ['products', 'filters']
        });
    };

    return (
        <div className="flex items-center justify-between">
            <div className="text-sm text-muted-foreground">
                {__('products.pagination.showing')} {from} {__('products.pagination.to')} {to} {__('products.pagination.of')} {total} {__('products.pagination.products')}
            </div>
            <div className="flex gap-1">
                {links.map((link, i) => (
                    <Button
                        key={i}
                        variant={link.active ? 'default' : 'outline'}
                        disabled={!link.url}
                        onClick={() => link.url && handlePageChange(link.url)}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                ))}
            </div>
        </div>
    );
}
