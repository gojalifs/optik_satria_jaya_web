<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Mockery;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private array $validPayload = [
        'transaction_no' => 'INV-2024-001',
        'receive_from' => 'John Doe',
        'patient_name' => 'Jane Doe',
        'optometrist_name' => 'Dr. Smith',
        'pay_for' => 'Kacamata',
        'frame_type' => 'Titanium',
        'frame_price' => 500000,
        'lens_type' => 'Progressive',
        'lens_price' => 750000,
        'total' => 1250000,
        'in_words' => 'Satu juta dua ratus lima puluh ribu rupiah',
        'date' => 'Jakarta, 01 January 2024',
    ];

    private function mockPdf(string $fakeContent = '%PDF-1.4 fake pdf content'): void
    {
        $pdfMock = Mockery::mock();
        $pdfMock->shouldReceive('setPaper')->andReturnSelf();
        $pdfMock->shouldReceive('setOption')->andReturnSelf();
        $pdfMock->shouldReceive('output')->andReturn($fakeContent);
        $pdfMock->shouldReceive('download')->andReturn(
            response($fakeContent, 200, ['Content-Type' => 'application/pdf'])
        );

        FacadePdf::shouldReceive('loadView')->andReturn($pdfMock);
    }

    // -----------------------------------------------------------------------
    // POST /api/transaction  (create)
    // -----------------------------------------------------------------------

    public function test_create_invoice_returns_success_and_signed_url(): void
    {
        Storage::fake('local');
        $this->mockPdf();

        $response = $this->postJson('/api/transaction', $this->validPayload);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'url'])
            ->assertJson(['success' => true]);

        $this->assertStringContainsString('/api/pdf/temp/', $response->json('url'));
    }

    public function test_create_invoice_stores_pdf_on_local_disk(): void
    {
        Storage::fake('local');
        $this->mockPdf();

        $this->postJson('/api/transaction', $this->validPayload);

        Storage::disk('local')->assertExists('invoices/invoice_inv_2024_001.pdf');
    }

    public function test_create_invoice_url_contains_signature_and_expiry(): void
    {
        Storage::fake('local');
        $this->mockPdf();

        $response = $this->postJson('/api/transaction', $this->validPayload);

        $url = $response->json('url');
        $this->assertStringContainsString('signature=', $url);
        $this->assertStringContainsString('expires=', $url);
    }

    public function test_create_invoice_with_missing_fields_returns_500(): void
    {
        $response = $this->postJson('/api/transaction', []);

        $response->assertStatus(500)
            ->assertJson(['success' => false]);
    }

    // -----------------------------------------------------------------------
    // GET /api/pdf/temp/{filename}  (downloadTemp)
    // -----------------------------------------------------------------------

    public function test_download_temp_with_valid_signed_url_returns_pdf(): void
    {
        Storage::fake('local');
        $filename = 'invoice_inv_2024_001.pdf';
        Storage::disk('local')->put("invoices/{$filename}", '%PDF-1.4 fake pdf content');

        $signedUrl = URL::temporarySignedRoute(
            'pdf.temp',
            now()->addHour(),
            ['filename' => $filename]
        );

        $response = $this->get($signedUrl);

        $response->assertStatus(200);
        $this->assertStringStartsWith('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_download_temp_without_signature_returns_403(): void
    {
        $response = $this->get('/api/pdf/temp/invoice_inv_2024_001.pdf');

        $response->assertStatus(403);
    }

    public function test_download_temp_with_expired_signature_returns_403(): void
    {
        Storage::fake('local');
        $filename = 'invoice_inv_2024_001.pdf';
        Storage::disk('local')->put("invoices/{$filename}", '%PDF-1.4 fake pdf content');

        $signedUrl = URL::temporarySignedRoute(
            'pdf.temp',
            now()->subMinute(),
            ['filename' => $filename]
        );

        $response = $this->get($signedUrl);

        $response->assertStatus(403);
    }

    public function test_download_temp_with_invalid_filename_returns_400(): void
    {
        $filename = '../etc/passwd';

        $signedUrl = URL::temporarySignedRoute(
            'pdf.temp',
            now()->addHour(),
            ['filename' => $filename]
        );

        $response = $this->get($signedUrl);

        $response->assertStatus(400)
            ->assertJson(['success' => false, 'message' => 'Invalid filename.']);
    }

    public function test_download_temp_with_nonexistent_file_returns_404(): void
    {
        Storage::fake('local');
        $filename = 'invoice_nonexistent.pdf';

        $signedUrl = URL::temporarySignedRoute(
            'pdf.temp',
            now()->addHour(),
            ['filename' => $filename]
        );

        $response = $this->get($signedUrl);

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Invoice file not found.']);
    }

    // -----------------------------------------------------------------------
    // GET /api/pdf/{id}  (download)
    // -----------------------------------------------------------------------

    public function test_download_invoice_by_id_with_valid_signed_url_returns_pdf(): void
    {
        $this->mockPdf();
        $transaction = Transaction::factory()->create();

        $signedUrl = URL::temporarySignedRoute(
            'pdf.download',
            now()->addHour(),
            ['id' => $transaction->id]
        );

        $response = $this->get($signedUrl);

        $response->assertStatus(200);
        $this->assertStringStartsWith('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_download_invoice_by_nonexistent_id_returns_404(): void
    {
        $signedUrl = URL::temporarySignedRoute(
            'pdf.download',
            now()->addHour(),
            ['id' => 99999]
        );

        $response = $this->get($signedUrl);

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Transaction not found.']);
    }

    public function test_download_invoice_by_id_without_signature_returns_403(): void
    {
        $response = $this->get('/api/pdf/1');

        $response->assertStatus(403);
    }
}
