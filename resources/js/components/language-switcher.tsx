import React from 'react';
import { usePage } from '@inertiajs/react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { GlobeIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';

interface LocaleInfo {
    current: string;
    available: Record<string, string>;
}

interface PageProps {
    locale: LocaleInfo;
}

export function LanguageSwitcher() {
    const { locale } = usePage<PageProps>().props;

    // Get the current language display name
    const currentLanguage = locale.available[locale.current] || 'English';

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="sm" className="flex items-center gap-1">
                    <GlobeIcon className="h-4 w-4" />
                    <span className="hidden sm:inline-block">{currentLanguage}</span>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                {Object.entries(locale.available).map(([code, name]) => (
                    <DropdownMenuItem
                        key={code}
                        className={locale.current === code ? 'bg-accent text-accent-foreground' : ''}
                        asChild
                    >
                        <a href={`/language/${code}`}>
                            {name}
                        </a>
                    </DropdownMenuItem>
                ))}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
