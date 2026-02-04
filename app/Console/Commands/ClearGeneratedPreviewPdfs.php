<?php

namespace App\Console\Commands;

use App\Http\Services\InvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClearGeneratedPreviewPdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-generated-preview-pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseDirectory = 'user';
        $allUserDirectories = Storage::disk('local')->directories($baseDirectory);

        foreach ($allUserDirectories as $userDirectory) {
            $allCompanyDirectories = Storage::disk('local')->directories($userDirectory);

            foreach ($allCompanyDirectories as $companyDirectory) {
                if (Str::startsWith(basename($companyDirectory), 'company_')) {
                    $allFiles = Storage::disk('local')->allFiles($companyDirectory);

                    foreach ($allFiles as $file) {
                        if (Str::startsWith(basename($file), InvoiceService::TMP_INVOICE_PREFIX) && Str::endsWith($file, '.pdf')) {
                            Storage::disk('local')->delete($file);
                        }
                    }
                }
            }
        }
    }
}
