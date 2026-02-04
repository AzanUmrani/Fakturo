import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { useLang } from '@/hooks/useLang';

interface PaginationProps {
    from: number;
    to: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
    routeName?: string;
    entityName?: string;
}

export default function Pagination({
    from,
    to,
    total,
    links,
    routeName,
    entityName = 'items'
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

        // Get the current route from the URL or use the provided routeName
        let currentRoute = routeName;
        if (!currentRoute) {
            // Extract the route from the current URL if not provided
            const pathSegments = window.location.pathname.split('/').filter(Boolean);
            if (pathSegments.length >= 2) {
                // Assuming format like /documents/invoices or /clients
                if (pathSegments.length === 1) {
                    currentRoute = `${pathSegments[0]}.index`;
                } else {
                    currentRoute = `${pathSegments[0]}.${pathSegments[1]}.index`;
                }
            } else {
                console.error('Could not determine current route for pagination');
                return;
            }
        }

        // Navigate using Inertia
        router.get(route(currentRoute), params, {
            preserveState: true,
            preserveScroll: true,
            only: ['invoices', 'clients', 'filters'] // Support both invoices and clients
        });
    };

    return (
        <div className="flex items-center justify-between">
            <div className="text-sm text-muted-foreground">
                {__('pagination.showing', { from, to, total, entity: __(entityName) })}
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
