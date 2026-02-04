import React from 'react';
import { usePage } from '@inertiajs/react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select';
import statesData from '@/../../resources/json/States.json';

interface StateSelectProps {
    value: string;
    onChange: (value: string) => void;
    required?: boolean;
    disabled?: boolean;
    placeholder?: string;
    className?: string;
}

interface LocaleInfo {
    current: string;
    available: Record<string, string>;
}

interface PageProps {
    locale: LocaleInfo;
}

export function StateSelect({
    value,
    onChange,
    required = false,
    disabled = false,
    placeholder = 'Select a country',
    className = ''
}: StateSelectProps) {
    const { locale } = usePage<PageProps>().props;
    const currentLocale = locale.current;

    // Convert the states data to an array and sort by name
    const states = Object.entries(statesData).map(([code, data]) => ({
        code,
        // @ts-ignore - we know the structure of the data
        name: data.name[currentLocale] || data.name.en // Fallback to English if translation not available
    })).sort((a, b) => a.name.localeCompare(b.name));

    return (
        <Select
            value={value}
            onValueChange={onChange}
            disabled={disabled}
            required={required}
        >
            <SelectTrigger className={className}>
                <SelectValue placeholder={placeholder} />
            </SelectTrigger>
            <SelectContent>
                {states.map((state) => (
                    <SelectItem key={state.code} value={state.code}>
                        {state.name}
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );
}
