- Project uses Laravel 12 with Inertia and react
- Project uses sail so every command have to be wrapped in `sail exec -u sail laravel.test <command>` where `<command>` will be replaced with your command
- do not run command just tell me what and i will run them
- do not use `axios` always use Inertia
- always use Inertia for everything
- in frontend never create `index.tsx` files always use real names like `users.tsx` to make searching files easyier
- if component is getting too large separate logic to multiple files (smaller components)

Languages instructions
- if new language file is added add it also to `HandleInertiaRequests` and to `syncLangFiles` to sync languages to frontend
- in frontend for languages in JS/TS always use `__` function from `useLang` hook, also u cant use it outside of components because its hook

General Code instructions:
- Do not generate code comments above the methods or code blocks if they are obvious. Generate comments only for something that needs extra explanation for the reasons why that code was written
- When changing the code, do not comment it out, unless specifically instructed. Assume the old code will stay in Git history

General Laravel instructions:
- If you need to generate a Laravel file, do not create the folder with `mkdir`, instead run command `php artisan make` whenever possible, and then that Artisan command will create the folder itself
- When generating migrations for pivot table, use correct alphabetical order, like "create_project_role_table" instead of "create_role_project_table"
- always create Request classes for controllers in `/app/Http/Requests/`
- for validation rules use array syntax, do not use `nullable|string` use `['nullable, 'string']`

Use Laravel 11+ skeleton structure.

- **Service Providers**: there are no other service providers except AppServiceProvider. Don't create new service providers unless absolutely necessary. Use Laravel 11+ new features, instead. Or, if you really need to create a new service provider, register it in `bootstrap/providers.php` and not `config/app.php` like it used to be before Laravel 11.
- **Event Listeners**: since Laravel 11, Listeners auto-listen for the events if they are type-hinted correctly.
- **Console Scheduler**: scheduled commands should be in `routes/console.php` and not `app/Console/Kernel.php` which doesn't exist since Laravel 11.
- **Middleware**: should be registered in `bootstrap/app.php` and not `app/Http/Kernel.php` which doesn't exist since Laravel 11.
- **Tailwind**: in new Blade pages, use Tailwind and not Bootstrap. Tailwind is already pre-configured since Laravel 11, with Vite.
- **Faker**: in Factories, use `fake()` helper instead of `$this-faker`.
- **Views**: to create new Blade files, use Artisan command `php artisan make:view` instead of `mkdir` or `touch`
- **Policies**: Laravel automatically auto-discovers Policies no need to register them in the Service Providers.
