<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/js/app.tsx', 'resources/css/app.css'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">
</head>

<body class="px-24 py-16 mx-auto" style="font-family: 'Roboto Mono', monospace; font-size: 14px; line-height: 1.5;">
    <div class="float-start">
        <img src="logo.png" alt="">
    </div>

    {{-- Header --}}
    <div style="text-align: center; margin-bottom: 20px;">
        <div class="text-lg font-bold">Optik Satria Jaya</div>
        <hr class="my-2">
        <div class="">Bukti Pembayaran</div>
        <div class="">No : {{ $transaction['invoice_number'] }}</div>
    </div>

    {{-- Customer Details --}}
    <div>
        <div class="flex">
            <div class="min-w-48">Sudah Terima Dari</div>
            <div>: {{ $transaction['receive_from'] }}</div>
        </div>
        <div class="flex">
            <div class="min-w-48">Nama Pasien</div>
            <div>: {{ $transaction['patient_name'] }}</div>
        </div>
        <div class="flex">
            <div class="min-w-48">Nama optometris</div>
            <div>: {{ $transaction['optometrist_name'] }}</div>
        </div>
        <div class="flex">
            <div class="min-w-48">Untuk Pembayaran</div>
        </div>

        <div class="ml-8">
            <div>Kacamata</div>
            <div class="flex">
                <div class="min-w-32">Jenis Frame</div>
                <div class="flex w-full">
                    <div class="ml-8 mr-1">:</div>
                    <div class="min-w-72 max-w-[300px]">
                        {{ $transaction['frame_type'] }}
                    </div>
                    <div class="ml-2">: {{ $transaction['frame_price'] }}</div>
                </div>
            </div>
            <div class="flex">
                <div class="min-w-32">Jenis Lensa</div>
                <div class="flex w-full">
                    <div class="ml-8 mr-1">:</div>
                    <div class="min-w-72 max-w-[300px]">
                        {{ $transaction['lens_type'] }}
                    </div>
                    <div class="ml-2">: {{ $transaction['lens_price'] }}</div>
                </div>
            </div>
        </div>

        <div>
            <div class="flex">
                <div class="w-[468px]">Total Pembayaran</div>
                <div class="ml-8">: {{ $transaction['total_price'] }}</div>
            </div>
        </div>
        <div class="mt-4">
            <div>Terbilang</div>
            <div class="mx-8 mt-2 px-2 py-1 border border-gray-500">{{ $transaction['amount_in_words'] }}</div>
        </div>

        <div class="mt-8">
            <div class="flex justify-end">
                <div class="text-center">
                    <div>{{ $transaction['date'] }}</div>
                    <div class="mt-4">Yang Menerima</div>
                    <div class="mt-16">Nursafaat, Amd.RO</div>
                </div>
            </div>
</body>

</html>
