<?php

namespace App\Http\Controllers\SJO;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Throwable;

class InvoiceController extends Controller
{
    public function create(Request $request)
    {
        try {
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
                now()->addMinutes(10),
                ['id' => $transaction->id]
            );

            return response()->json([
                'success'       => true,
                'signed_url'    => $signedUrl,
            ]);
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duplicate transaction number.',
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error creating invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            $filename = preg_replace('/[^a-zA-Z0-9]/', '_', $transaction->invoice_number);
            $filename = strtolower($filename);
            $filename = 'invoice_' . $filename . '.pdf';

            return FacadePdf::loadView('pdf.invoice', [
                'transaction' => $transaction,
            ])
                ->setPaper('a4')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('isPhpEnabled', true)
                ->setOption('dpi', 96)
                ->setOption('defaultFont', 'sans-serif')
                ->download($filename);
        } catch (Throwable $e) {
            Log::error('Error downloading invoice: ' . $e->getMessage(), [
                'transaction_id' => $id,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error downloading invoice: ' . $e->getMessage(),
            ], 500);
        }
    }
}
