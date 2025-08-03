<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font: Roboto Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto Mono', monospace;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }

        .address {
            font-size: 12px;
            color: #6b7280;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .border-box {
            border: 1px solid #6b7280;
            padding: 4px 8px;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        .mt-16 {
            margin-top: 4rem;
        }
    </style>
</head>

<body>

    <!-- Header with logos -->
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="vertical-align: top;">
                <img src="logo.png" alt="Logo Kiri" style="max-width: 80px;">
            </td>
            <td style="width: auto; padding: 0 8px; ">
                <div class="text-bold" style="font-size: 22px; text-align: center;">Optik Satria Jaya</div>
                <div class="address">
                    <div>
                        Jalan Cagak Sukamantri RT 06/02, Sukaraya, Karangbahagia - Bekasi
                    </div>
                    <div>
                        Perum Grand Cikarang City Cluster Sakura Blok H7/17, Cikarang Utara - Bekasi
                    </div>
                    <div>
                        Telp: 0819 0941 9741 | 0853 1112 3440
                    </div>
                </div>
                <hr style="margin: 8px 0;">
                <div class="text-center">
                    <div>Bukti Pembayaran</div>
                    <div>No : {{ $transaction['invoice_number'] }}</div>
                </div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <img src="logo-2.png" alt="Logo Kanan" style="max-width: 80px;">
            </td>
        </tr>
    </table>

    <!-- Customer Details -->
    <div style="margin: 0px 35px;">
        <table style="width: 100%; margin-bottom: 16px;" cellspacing="0" cellpadding="4">
            <tr>
                <td style="width: 180px;">Sudah Terima Dari</td>
                <td>: {{ $transaction['receive_from'] }}</td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>: {{ $transaction['patient_name'] }}</td>
            </tr>
            <tr>
                <td>Nama optometris</td>
                <td>: {{ $transaction['optometrist_name'] }}</td>
            </tr>
            <tr>
                <td>Untuk Pembayaran</td>
                <td></td>
            </tr>
        </table>

        <!-- Rincian Pembayaran -->
        <div style="margin-left: 30px;">
            <div style="margin-bottom: 6px; padding-left: 4px;">Kacamata</div>
            <table style="width: 100%;" cellspacing="0" cellpadding="4">
                <tr>
                    <td style="width: 150px; ">Jenis Frame</td>
                    <td style="width: 10px; ">:</td>
                    <td style="width: 300px; ">{{ $transaction['frame_type'] }}</td>
                    <td style="width: 10px; ">:</td>
                    <td>{{ $transaction['frame_price'] }}</td>
                </tr>
                <tr>
                    <td>Jenis Lensa</td>
                    <td>:</td>
                    <td>{{ $transaction['lens_type'] }}</td>
                    <td>:</td>
                    <td>{{ $transaction['lens_price'] }}</td>
                </tr>
            </table>
        </div>

        <!-- Total -->
        <table style="width: 100%; margin-top: 16px;" cellspacing="0" cellpadding="4">
            <tr>
                <td style="width: 468px;">Total Pembayaran</td>
                <td style="padding-left: 41px;">: {{ $transaction['total_price'] }}</td>
            </tr>
        </table>

        <!-- Terbilang -->
        <div style="margin-top: 16px;">
            <div style="vertical-align: middle; width: 80px;">Terbilang</div>
            <div>
                <div class="border-box" style="margin: 16px 16px;">
                    {{ $transaction['amount_in_words'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Signature -->
    <div style="margin-top: 32px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: auto; text-align: center;">
                    <div>{{ $transaction['date'] }}</div>
                    <div style="margin-top: 16px;">Yang Menerima</div>
                    <div style="margin-top: 64px;">Nursafaat, Amd.RO</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="mt-8 text-center">
        <div class="text-bold">Terima Kasih</div>
        <div>Atas kepercayaan Anda menggunakan jasa kami.</div>
    </div>

</body>

</html>
