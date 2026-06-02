<?php

namespace App\Http\Controllers\SJO;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Throwable;

class InvoiceController extends Controller
{
    public function create(Request $request)
    {
        Log::info('InvoiceController@create: invoice creation requested', [
            'ip' => $request->ip(),
            'user_id' => $request->user()?->id,
        ]);

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

            $filename = preg_replace('/[^a-zA-Z0-9]/', '_', $transaction['invoice_number']);
            $filename = strtolower($filename);
            $filename = 'invoice_' . $filename . '.pdf';

            $pdfContent = FacadePdf::loadView('pdf.invoice', [
                'transaction' => $transaction,
            ])
                ->setPaper('a4')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('isPhpEnabled', true)
                ->setOption('dpi', 96)
                ->setOption('defaultFont', 'sans-serif')
                ->output();

            Storage::disk('local')->put("invoices/{$filename}", $pdfContent);

            $signedUrl = URL::temporarySignedRoute(
                'pdf.temp',
                now()->addHour(),
                ['filename' => $filename]
            );

            Log::info('InvoiceController@create: invoice created successfully', [
                'invoice_number' => $transaction['invoice_number'],
                'filename' => $filename,
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
            ]);

            return response()->json([
                'success' => true,
                'url' => $signedUrl,
            ]);
        } catch (Throwable $e) {
            Log::error('InvoiceController@create: error creating invoice', [
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadTemp(string $filename)
    {
        Log::info('InvoiceController@downloadTemp: signed URL access for invoice download', [
            'filename' => $filename,
        ]);

        try {
            if (!preg_match('/^[a-z0-9_]+\.pdf$/', $filename)) {
                Log::warning('InvoiceController@downloadTemp: invalid filename rejected', [
                    'filename' => $filename,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid filename.',
                ], 400);
            }

            $path = "invoices/{$filename}";

            if (!Storage::disk('local')->exists($path)) {
                Log::warning('InvoiceController@downloadTemp: invoice file not found', [
                    'filename' => $filename,
                    'path' => $path,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invoice file not found.',
                ], 404);
            }

            $content = Storage::disk('local')->get($path);

            Log::info('InvoiceController@downloadTemp: invoice downloaded successfully', [
                'filename' => $filename,
            ]);

            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        } catch (Throwable $e) {
            Log::error('Error downloading temp invoice: ' . $e->getMessage(), [
                'filename' => $filename,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error downloading invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download($id)
    {
        Log::info('InvoiceController@download: invoice download requested', [
            'transaction_id' => $id,
        ]);

        try {
            $transaction = Transaction::findOrFail($id);

            $filename = preg_replace('/[^a-zA-Z0-9]/', '_', $transaction->invoice_number);
            $filename = strtolower($filename);
            $filename = 'invoice_' . $filename . '.pdf';

            Log::info('InvoiceController@download: invoice downloaded successfully', [
                'transaction_id' => $id,
                'invoice_number' => $transaction->invoice_number,
                'filename' => $filename,
            ]);

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
        } catch (ModelNotFoundException $e) {
            Log::warning('InvoiceController@download: transaction not found', [
                'transaction_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
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
