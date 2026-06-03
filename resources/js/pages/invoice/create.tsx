import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head } from '@inertiajs/react';
import { CheckCircle2, Download, FileText, Loader2 } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';

// ─── Terbilang (Indonesian number-to-words) ──────────────────────────────────

const SATUAN = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
const BELASAN = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];

function terbilangRibuan(n: number): string {
    if (n === 0) return '';
    if (n < 10) return SATUAN[n];
    if (n < 20) return BELASAN[n - 10];
    if (n < 100) {
        const tens = Math.floor(n / 10);
        const ones = n % 10;
        return SATUAN[tens] + ' Puluh' + (ones > 0 ? ' ' + SATUAN[ones] : '');
    }
    if (n < 1000) {
        const hundreds = Math.floor(n / 100);
        const rest = n % 100;
        const prefix = hundreds === 1 ? 'Seratus' : SATUAN[hundreds] + ' Ratus';
        return prefix + (rest > 0 ? ' ' + terbilangRibuan(rest) : '');
    }
    return '';
}

function terbilang(n: number): string {
    if (n === 0) return 'Nol Rupiah';
    if (n < 0) return 'Minus ' + terbilang(-n);

    const parts: string[] = [];

    const triliun = Math.floor(n / 1_000_000_000_000);
    if (triliun > 0) {
        parts.push((triliun === 1 ? 'Satu' : terbilangRibuan(triliun)) + ' Triliun');
        n %= 1_000_000_000_000;
    }

    const miliar = Math.floor(n / 1_000_000_000);
    if (miliar > 0) {
        parts.push((miliar === 1 ? 'Satu' : terbilangRibuan(miliar)) + ' Miliar');
        n %= 1_000_000_000;
    }

    const juta = Math.floor(n / 1_000_000);
    if (juta > 0) {
        parts.push((juta === 1 ? 'Satu' : terbilangRibuan(juta)) + ' Juta');
        n %= 1_000_000;
    }

    const ribu = Math.floor(n / 1_000);
    if (ribu > 0) {
        parts.push((ribu === 1 ? 'Seribu' : terbilangRibuan(ribu) + ' Ribu'));
        n %= 1_000;
    }

    if (n > 0) {
        parts.push(terbilangRibuan(n));
    }

    return parts.join(' ') + ' Rupiah';
}

// ─── Format currency display ──────────────────────────────────────────────────

function formatRupiah(value: string): string {
    const num = value.replace(/\D/g, '');
    if (!num) return '';
    return parseInt(num, 10).toLocaleString('id-ID');
}

function parseRupiah(value: string): number {
    return parseInt(value.replace(/\D/g, '') || '0', 10);
}

// ─── Constants ────────────────────────────────────────────────────────────────

const API_CREATE_INVOICE = '/api/transaction';

// ─── Date helper ──────────────────────────────────────────────────────────────

function formatIndonesianDate(date: Date = new Date()): string {
    return date.toLocaleDateString('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

// ─── Types ────────────────────────────────────────────────────────────────────

interface FormState {
    transaction_no: string;
    receive_from: string;
    patient_name: string;
    optometrist_name: string;
    pay_for: string;
    frame_type: string;
    frame_price: string;
    lens_type: string;
    lens_price: string;
    date: string;
}

const initialForm: FormState = {
    transaction_no: '',
    receive_from: '',
    patient_name: '',
    optometrist_name: '',
    pay_for: 'Kacamata',
    frame_type: '',
    frame_price: '',
    lens_type: '',
    lens_price: '',
    date: formatIndonesianDate(),
};

// ─── CSRF helper ──────────────────────────────────────────────────────────────

function getCsrfToken(): string {
    return (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
}

// ─── Component ────────────────────────────────────────────────────────────────

export default function InvoiceCreate() {
    const [form, setForm] = useState<FormState>(initialForm);
    const [total, setTotal] = useState(0);
    const [inWords, setInWords] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [successUrl, setSuccessUrl] = useState<string | null>(null);
    const [showModal, setShowModal] = useState(false);

    // Recalculate total & terbilang whenever prices change
    useEffect(() => {
        const frame = parseRupiah(form.frame_price);
        const lens = parseRupiah(form.lens_price);
        const sum = frame + lens;
        setTotal(sum);
        setInWords(sum > 0 ? terbilang(sum) : '');
    }, [form.frame_price, form.lens_price]);

    const handleChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        if (name === 'frame_price' || name === 'lens_price') {
            setForm((prev) => ({ ...prev, [name]: formatRupiah(value) }));
        } else {
            setForm((prev) => ({ ...prev, [name]: value }));
        }
    }, []);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);
        setLoading(true);

        try {
            const payload = {
                transaction_no: form.transaction_no,
                receive_from: form.receive_from,
                patient_name: form.patient_name,
                optometrist_name: form.optometrist_name,
                pay_for: form.pay_for,
                frame_type: form.frame_type,
                frame_price: parseRupiah(form.frame_price),
                lens_type: form.lens_type,
                lens_price: parseRupiah(form.lens_price),
                total,
                in_words: inWords,
                date: form.date,
            };

            const res = await fetch(API_CREATE_INVOICE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    Accept: 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                throw new Error(data.message ?? 'Terjadi kesalahan saat membuat invoice.');
            }

            setSuccessUrl(data.url);
            setShowModal(true);
            setForm(initialForm);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Terjadi kesalahan tidak diketahui.');
        } finally {
            setLoading(false);
        }
    };

    const handleDownload = () => {
        if (successUrl) {
            window.open(successUrl, '_blank');
        }
    };

    const handleCloseModal = () => {
        setShowModal(false);
        setSuccessUrl(null);
    };

    return (
        <>
            <Head title="Buat Invoice" />

            {/* ── Photo background with glass overlay ── */}
            <div
                className="relative min-h-screen bg-gray-600 bg-cover bg-center"
                style={{ backgroundImage: 'url(https://images.unsplash.com/photo-0are122T4ho?auto=format&fit=crop&w=1920&q=80)' }}
            >
                {/* Dark glass overlay */}
                <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" />

                <div className="relative z-10 flex min-h-screen items-start justify-center px-4 py-10">
                    <div className="w-full max-w-3xl">
                        {/* ── Header ── */}
                        <div className="mb-8 text-center">
                            <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 shadow-lg backdrop-blur-sm">
                                <FileText className="h-8 w-8 text-white" />
                            </div>
                            <h1 className="text-4xl font-bold text-white drop-shadow">Buat Invoice</h1>
                            <p className="mt-2 text-white/80">Optik Satria Jaya — Bukti Pembayaran</p>
                        </div>

                        {/* ── Form card ── */}
                        <form
                            onSubmit={handleSubmit}
                            className="rounded-2xl border border-white/20 bg-white/15 p-8 shadow-2xl backdrop-blur-md"
                        >
                            {/* Section: Informasi Transaksi */}
                            <SectionTitle>Informasi Transaksi</SectionTitle>
                            <div className="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <Field label="No. Invoice" id="transaction_no">
                                    <Input
                                        id="transaction_no"
                                        name="transaction_no"
                                        placeholder="INV-001"
                                        required
                                        value={form.transaction_no}
                                        onChange={handleChange}
                                        className="border-white/30 bg-white/20 text-white placeholder:text-white/50 focus-visible:border-white/60 focus-visible:ring-white/20"
                                    />
                                </Field>
                                <Field label="Tanggal" id="date">
                                    <Input
                                        id="date"
                                        name="date"
                                        placeholder="Jakarta, 1 Juni 2025"
                                        required
                                        value={form.date}
                                        onChange={handleChange}
                                        className="border-white/30 bg-white/20 text-white placeholder:text-white/50 focus-visible:border-white/60 focus-visible:ring-white/20"
                                    />
                                </Field>
                            </div>

                            {/* Section: Data Pasien */}
                            <SectionTitle className="mt-8">Data Pasien</SectionTitle>
                            <div className="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <Field label="Sudah Terima Dari" id="receive_from">
                                    <Input
                                        id="receive_from"
                                        name="receive_from"
                                        placeholder="Nama pembayar"
                                        required
                                        value={form.receive_from}
                                        onChange={handleChange}
                                        className="border-white/30 bg-white/20 text-white placeholder:text-white/50 focus-visible:border-white/60 focus-visible:ring-white/20"
                                    />
                                </Field>
                                <Field label="Nama Pasien" id="patient_name">
                                    <Input
                                        id="patient_name"
                                        name="patient_name"
                                        placeholder="Nama pasien"
                                        required
                                        value={form.patient_name}
                                        onChange={handleChange}
                                        className="border-white/30 bg-white/20 text-white placeholder:text-white/50 focus-visible:border-white/60 focus-visible:ring-white/20"
                                    />
                                </Field>
                                <Field label="Nama Optometris" id="optometrist_name">
                                    <Input
                                        id="optometrist_name"
                                        name="optometrist_name"
                                        placeholder="Nama optometris"
                                        required
                                        value={form.optometrist_name}
                                        onChange={handleChange}
                                        className="border-white/30 bg-white/20 text-white placeholder:text-white/50 focus-visible:border-white/60 focus-visible:ring-white/20"
                                    />
                                </Field>
                                <Field label="Untuk Pembayaran" id="pay_for">
                                    <Input
                                        id="pay_for"
                                        name="pay_for"
                                        placeholder="Kacamata"
                                        required
                                        value={form.pay_for}
                                        onChange={handleChange}
                                        className="border-white/30 bg-white/20 text-white placeholder:text-white/50 focus-visible:border-white/60 focus-visible:ring-white/20"
                                    />
                                </Field>
                            </div>

                            {/* Section: Rincian Kacamata */}
                            <SectionTitle className="mt-8">Rincian Kacamata</SectionTitle>
                            <div className="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <Field label="Jenis Frame" id="frame_type">
                                    <Input
                                        id="frame_type"
                                        name="frame_type"
                                        placeholder="Nama / model frame"
                                        required
                                        value={form.frame_type}
                                        onChange={handleChange}
                                        className="border-white/30 bg-white/20 text-white placeholder:text-white/50 focus-visible:border-white/60 focus-visible:ring-white/20"
                                    />
                                </Field>
                                <Field label="Harga Frame (Rp)" id="frame_price">
                                    <Input
                                        id="frame_price"
                                        name="frame_price"
                                        inputMode="numeric"
                                        placeholder="0"
                                        required
                                        value={form.frame_price}
                                        onChange={handleChange}
                                        className="border-white/30 bg-white/20 text-white placeholder:text-white/50 focus-visible:border-white/60 focus-visible:ring-white/20"
                                    />
                                </Field>
                                <Field label="Jenis Lensa" id="lens_type">
                                    <Input
                                        id="lens_type"
                                        name="lens_type"
                                        placeholder="Nama / jenis lensa"
                                        required
                                        value={form.lens_type}
                                        onChange={handleChange}
                                        className="border-white/30 bg-white/20 text-white placeholder:text-white/50 focus-visible:border-white/60 focus-visible:ring-white/20"
                                    />
                                </Field>
                                <Field label="Harga Lensa (Rp)" id="lens_price">
                                    <Input
                                        id="lens_price"
                                        name="lens_price"
                                        inputMode="numeric"
                                        placeholder="0"
                                        required
                                        value={form.lens_price}
                                        onChange={handleChange}
                                        className="border-white/30 bg-white/20 text-white placeholder:text-white/50 focus-visible:border-white/60 focus-visible:ring-white/20"
                                    />
                                </Field>
                            </div>

                            {/* ── Total auto-calculated ── */}
                            {total > 0 && (
                                <div className="mt-6 rounded-xl border border-white/30 bg-white/20 p-4 backdrop-blur-sm">
                                    <div className="flex items-center justify-between">
                                        <span className="font-semibold text-white">Total Pembayaran</span>
                                        <span className="text-xl font-bold text-white">
                                            Rp {total.toLocaleString('id-ID')}
                                        </span>
                                    </div>
                                    {inWords && (
                                        <p className="mt-1 text-sm text-white/80 italic">
                                            Terbilang: {inWords}
                                        </p>
                                    )}
                                </div>
                            )}

                            {/* ── Error ── */}
                            {error && (
                                <div className="mt-4 rounded-xl border border-red-300/50 bg-red-500/20 p-3 text-sm text-white">
                                    ⚠️ {error}
                                </div>
                            )}

                            {/* ── Submit ── */}
                            <Button
                                type="submit"
                                disabled={loading}
                                className="mt-8 w-full bg-white py-3 text-base font-semibold text-indigo-700 shadow-lg transition hover:bg-white/90 hover:shadow-xl disabled:opacity-60"
                                size="lg"
                            >
                                {loading ? (
                                    <>
                                        <Loader2 className="mr-2 h-5 w-5 animate-spin" />
                                        Membuat Invoice...
                                    </>
                                ) : (
                                    <>
                                        <FileText className="mr-2 h-5 w-5" />
                                        Buat Invoice
                                    </>
                                )}
                            </Button>
                        </form>

                        {/* ── Logout ── */}
                        <div className="mt-4 text-center">
                            <form method="POST" action="/invoice/logout">
                                <input type="hidden" name="_token" value={getCsrfToken()} />
                                <button
                                    type="submit"
                                    className="cursor-pointer border-0 bg-transparent p-0 text-sm text-white/50 transition hover:text-white/80"
                                >
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {/* ── Success Modal ── */}
            <Dialog open={showModal} onOpenChange={handleCloseModal}>
                <DialogContent className="max-w-sm text-center">
                    <DialogHeader>
                        <div className="mx-auto mb-2 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                            <CheckCircle2 className="h-10 w-10 text-green-500" />
                        </div>
                        <DialogTitle className="text-xl">Invoice Berhasil Dibuat!</DialogTitle>
                        <DialogDescription>
                            Invoice telah berhasil dibuat dan siap untuk diunduh sebagai PDF.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter className="flex flex-col gap-3 sm:flex-col">
                        <Button
                            onClick={handleDownload}
                            className="w-full bg-indigo-600 text-white hover:bg-indigo-700"
                        >
                            <Download className="mr-2 h-4 w-4" />
                            Download PDF
                        </Button>
                        <Button variant="outline" onClick={handleCloseModal} className="w-full">
                            Tutup
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}

// ─── Small helpers ────────────────────────────────────────────────────────────

function SectionTitle({ children, className }: { children: React.ReactNode; className?: string }) {
    return (
        <h2 className={`text-sm font-semibold uppercase tracking-wider text-white/60 ${className ?? ''}`}>
            {children}
        </h2>
    );
}

function Field({ label, id, children }: { label: string; id: string; children: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1.5">
            <Label htmlFor={id} className="text-sm font-medium text-white/90">
                {label}
            </Label>
            {children}
        </div>
    );
}
