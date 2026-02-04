<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-2xl font-bold text-center mb-6">PDF Preview</h1>

                <div class="flex justify-between mb-4">
                    <a
                        href="{{ route('pdf-test.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        Back to Upload
                    </a>

                    <a
                        href="{{ Storage::url($pdfPath) }}"
                        download
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-full h-screen">
                    <embed
                        src="{{ $pdfUrl }}"
                        type="application/pdf"
                        width="100%"
                        height="100%"
                        class="border border-gray-300 rounded"
                    >
                </div>
            </div>
        </div>
    </div>
</body>
</html>
