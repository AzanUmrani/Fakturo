import { Head } from '@inertiajs/react';

import HeadingSmall from '@/components/heading-small';
import { LanguageSwitcher } from '@/components/language-switcher';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Language settings',
        href: '/settings/language',
    },
];

export default function Language() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Language settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Language settings" description="Choose your preferred language" />
                    <div className="mt-4">
                        <LanguageSwitcher />
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
