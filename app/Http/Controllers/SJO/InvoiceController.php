<?php

namespace App\Http\Controllers\SJO;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

class InvoiceController extends Controller
{
    public function create(Request $request)
    {
        $json = $request->getContent();
        $data = json_decode($json, true);

        $transaction = [
            'invoice_number' => $data['transaction_no'],
            'receive_from' => $data['receive_from'],
            'patient_name' => $data['patient_name'],
            'optometrist_name' => $data['optometrist_name'],
            'pay_for'   => $data['pay_for'],
            'frame_type' => $data['frame_type'],
            'frame_price' => $data['frame_price'],
            'lens_type' => $data['lens_type'],
            'lens_price' => $data['lens_price'],
            'total_price' => $data['total'],
            'amount_in_words' => $data['in_words'],
            'date' => $data['date'] ?? now()->format('Jakarta, d F Y'),
        ];

        // save to database
        $transaction = Transaction::create($transaction);

        $signedUrl = URL::temporarySignedRoute(
            'pdf.download',
            now()->addMinutes(50),
            ['id' => $transaction->id]
        );

        return response()->json([
            'success'       => true,
            'signed_url'    => $signedUrl,
        ]);
    }

    public function download($id)
    {
        $transaction = Transaction::findOrFail($id);

        return Pdf::view('pdf.invoice', [
            'transaction' => $transaction,
        ])
            ->withBrowsershot(
                function (Browsershot $shot) {
                    $shot->setIncludePath(config('app.node_path', '/home/fajar/.nvm/versions/node/v22.12.0/bin'))
                    ->setNodeBinary(config('app.node_path', '/home/fajar/.nvm/versions/node/v22.12.0/node'))
                    ->setChromePath(config('app.chrome_path', '/home/fajar/.cache/puppeteer/chrome/linux-138.0.7204.168/chrome-linux64/chrome'))
                    ->setNpmBinary(config('app.node_path', '/home/fajar/.nvm/versions/node/v22.12.0/npm'));
                }
            )
            ->name('invoice_' . $transaction->invoice_number . '.pdf');
    }
}
