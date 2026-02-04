import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { Users, Building2, Package, FileText } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface DashboardProps {
    totalClients: number;
    totalCompanies: number;
    totalProducts: number;
    totalDocuments: number;
}

export default function Dashboard() {
    const { props } = usePage<DashboardProps>();
    const { totalClients, totalCompanies, totalProducts, totalDocuments } = props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <div className="grid auto-rows-min gap-4 md:grid-cols-4">
                    <div className="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-card p-6 min-h-[200px] flex flex-col items-center justify-center">
                        <Users className="h-12 w-12 text-primary mb-4" />
                        <h3 className="text-lg font-semibold text-muted-foreground mb-2">Total Clients</h3>
                        <p className="text-3xl font-bold">{totalClients}</p>
                    </div>
                    <div className="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-card p-6 min-h-[200px] flex flex-col items-center justify-center">
                        <Building2 className="h-12 w-12 text-primary mb-4" />
                        <h3 className="text-lg font-semibold text-muted-foreground mb-2">Total Companies</h3>
                        <p className="text-3xl font-bold">{totalCompanies}</p>
                    </div>
                    <div className="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-card p-6 min-h-[200px] flex flex-col items-center justify-center">
                        <Package className="h-12 w-12 text-primary mb-4" />
                        <h3 className="text-lg font-semibold text-muted-foreground mb-2">Total Products</h3>
                        <p className="text-3xl font-bold">{totalProducts}</p>
                    </div>
                    <div className="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-card p-6 min-h-[200px] flex flex-col items-center justify-center">
                        <FileText className="h-12 w-12 text-primary mb-4" />
                        <h3 className="text-lg font-semibold text-muted-foreground mb-2">Total Documents</h3>
                        <p className="text-3xl font-bold">{totalDocuments}</p>
                    </div>
                </div>
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </AppLayout>
    );
}
