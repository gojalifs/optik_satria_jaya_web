<?php

namespace App\Http\Controllers\SJO;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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
            now()->addMinutes(5),
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

        $pdf = Pdf::view('pdf.invoice', [
            'transaction' => $transaction,
        ])->set_include_path(config('app.node_path'));

        // stream pdf
        return $pdf->stream('invoice_' . $transaction->invoice_number . '.pdf');
    }
}
