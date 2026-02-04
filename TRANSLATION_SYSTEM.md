# Translation System with Laravel Lang Sync Inertia

This document explains how the translation system works in the Fakturo.app application using the `erag/laravel-lang-sync-inertia` package and provides guidance on how to use it.

## Overview

The application uses the `erag/laravel-lang-sync-inertia` package to handle translations between Laravel and Inertia.js. This package provides a streamlined way to load translations from language files and share them with the frontend, making it easier to add new translations and maintain existing ones.

## How It Works

The system consists of several components:

1. **syncLangFiles Function**: This helper function loads translations from specified language files and shares them with Inertia.js.

2. **ShareLangTranslations Middleware**: This middleware automatically shares the loaded translations with Inertia.js.

3. **useLang Hook**: This React hook provides `trans()` and `__()` functions to access translations in React components.

The package is integrated with Laravel's built-in localization system and uses the current locale to determine which language directory to load translations from.

## Adding New Translation Files

To add a new translation file:

1. Create a new PHP file in the `lang/{locale}` directory for each supported language (en, sk, cz, ua).
2. Structure the file as an associative array with keys corresponding to translation identifiers and values containing the translated text.
3. Use the `syncLangFiles()` function in your controller or middleware to load the new file.

Example:

```php
// In a controller method or middleware
syncLangFiles(['auth', 'dashboard']);
```

The function can take a single string or an array of strings, each representing a translation file to load. The translations will be automatically shared with Inertia.js.

## Adding New Translation Keys

To add a new translation key:

1. Add the key and its translation to the appropriate language file in each supported language.
2. No additional configuration is needed - the key will be automatically available to the frontend.

Example:

```php
// In lang/en/auth.php
return [
    'existing_key' => 'Existing translation',
    'new_key' => 'New translation',
];
```

The new key will be accessible in your React components using the `__()` or `trans()` functions:

```tsx
// In a React component
const { __ } = useLang();
const translation = __('auth.new_key');
```

## Accessing Translations in Frontend Components

Translations are shared with the frontend through Inertia.js and can be accessed in React components using the `useLang` hook.

Example:

```tsx
import { useLang } from '@/hooks/useLang';

export default function Login() {
    const { __, trans } = useLang();
    
    return (
        <div>
            <h1>{__('auth.login.title')}</h1>
            <p>{trans('auth.login.welcome', { name: 'John' })}</p>
            {/* other elements... */}
        </div>
    );
}
```

The `useLang` hook provides two functions:

1. `__()`: A simple function for accessing translations without replacements.
2. `trans()`: A function that supports dynamic replacements in translations.

For example, if you have a translation like:

```php
// In lang/en/auth.php
return [
    'welcome' => 'Welcome, {name}!',
];
```

You can use it with:

```tsx
trans('auth.welcome', { name: 'John' }); // "Welcome, John!"
```

## Adding Support for New Languages

To add support for a new language:

1. Create a new directory in the `lang` directory with the language code (e.g., `lang/fr` for French).
2. Copy the translation files from an existing language directory and translate the values.
3. Add the new language to the `$languages` array in the `HandleInertiaRequests` middleware.

Example:

```php
protected $languages = [
    'en' => 'English',
    'sk' => 'Slovenčina',
    'cz' => 'Čeština',
    'ua' => 'Українська',
    'fr' => 'Français'
];
```

## Benefits of the Dynamic Translation Loading System

1. **Reduced Duplication**: No need to specify translation keys twice (once in the language file and once in the middleware).
2. **Easier Maintenance**: Adding new translations is simpler and less error-prone.
3. **Automatic Updates**: New translations are automatically included without requiring changes to the middleware.
4. **Scalability**: The system can easily accommodate additional translation files and languages.
