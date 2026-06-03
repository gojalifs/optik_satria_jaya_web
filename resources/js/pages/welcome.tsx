import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Eye, FileText, MapPin, Menu, Phone, Shield, Sparkles, Star, X } from 'lucide-react';
import { useState } from 'react';

// ─── Data ────────────────────────────────────────────────────────────────────

const SERVICES = [
    {
        icon: Eye,
        title: 'Pemeriksaan Mata',
        desc: 'Pemeriksaan refraksi lengkap oleh optometris berpengalaman untuk mengetahui kebutuhan kacamata Anda.',
    },
    {
        icon: Sparkles,
        title: 'Frame & Lensa Berkualitas',
        desc: 'Koleksi frame stylish dari berbagai merek ternama dengan lensa berkualitas tinggi untuk kenyamanan terbaik.',
    },
    {
        icon: Shield,
        title: 'Garansi Produk',
        desc: 'Setiap produk yang kami jual disertai garansi resmi sehingga Anda dapat berbelanja dengan tenang.',
    },
    {
        icon: FileText,
        title: 'Bukti Pembayaran Digital',
        desc: 'Invoice digital dikirim langsung via PDF setelah transaksi, memudahkan administrasi Anda.',
    },
];

const TESTIMONIALS = [
    {
        name: 'Budi Santoso',
        text: 'Pelayanannya ramah dan profesional. Kacamata yang saya dapat sangat nyaman dan sesuai resep dokter.',
        rating: 5,
    },
    {
        name: 'Siti Rahayu',
        text: 'Pilihan framenya banyak dan harganya terjangkau. Optometrisnya juga sangat sabar menjelaskan hasil pemeriksaan.',
        rating: 5,
    },
    {
        name: 'Ahmad Fauzi',
        text: 'Sudah lebih dari 3 tahun jadi pelanggan setia di sini. Kualitasnya tidak pernah mengecewakan!',
        rating: 5,
    },
];

const BRANCHES = [
    {
        name: 'Cabang Sukaraya',
        address: 'Jalan Cagak Sukamantri RT 06/02, Sukaraya, Karangbahagia – Bekasi',
    },
    {
        name: 'Cabang Grand Cikarang City',
        address: 'Perum Grand Cikarang City Cluster Sakura Blok H7/17, Cikarang Utara – Bekasi',
    },
];

// ─── Component ───────────────────────────────────────────────────────────────

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;
    const [menuOpen, setMenuOpen] = useState(false);

    return (
        <>
            <Head title="Optik Satria Jaya – Kacamata Berkualitas di Bekasi" />

            {/* ── NAVBAR ── */}
            <header className="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-white/80 backdrop-blur-md">
                <div className="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
                    {/* Logo */}
                    <div className="flex items-center gap-3">
                        <img src="/logo.png" alt="Optik Satria Jaya" className="h-10 w-auto" />
                        <span className="text-lg font-bold text-indigo-700 leading-tight">
                            Optik<br />
                            <span className="font-semibold text-gray-700 text-sm">Satria Jaya</span>
                        </span>
                    </div>

                    {/* Desktop nav */}
                    <nav className="hidden items-center gap-6 text-sm font-medium text-gray-600 sm:flex">
                        <a href="#layanan" className="hover:text-indigo-600 transition-colors">Layanan</a>
                        <a href="#cabang" className="hover:text-indigo-600 transition-colors">Cabang</a>
                        <a href="#testimoni" className="hover:text-indigo-600 transition-colors">Testimoni</a>
                        <a href="#kontak" className="hover:text-indigo-600 transition-colors">Kontak</a>
                    </nav>

                    {/* Auth buttons */}
                    <div className="hidden items-center gap-2 sm:flex">
                        {auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={route('login')}
                                    className="rounded-full px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
                                >
                                    Masuk
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors"
                                >
                                    Daftar
                                </Link>
                            </>
                        )}
                    </div>

                    {/* Mobile hamburger */}
                    <button
                        className="sm:hidden rounded-md p-1 text-gray-600 hover:bg-gray-100"
                        onClick={() => setMenuOpen(!menuOpen)}
                        aria-label="Toggle menu"
                    >
                        {menuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
                    </button>
                </div>

                {/* Mobile menu */}
                {menuOpen && (
                    <div className="sm:hidden border-t border-gray-100 bg-white px-4 pb-4 pt-2 text-sm">
                        <nav className="flex flex-col gap-3 font-medium text-gray-700">
                            <a href="#layanan" onClick={() => setMenuOpen(false)} className="hover:text-indigo-600">Layanan</a>
                            <a href="#cabang" onClick={() => setMenuOpen(false)} className="hover:text-indigo-600">Cabang</a>
                            <a href="#testimoni" onClick={() => setMenuOpen(false)} className="hover:text-indigo-600">Testimoni</a>
                            <a href="#kontak" onClick={() => setMenuOpen(false)} className="hover:text-indigo-600">Kontak</a>
                        </nav>
                        <div className="mt-3 flex flex-col gap-2">
                            {auth.user ? (
                                <Link href={route('dashboard')} className="rounded-full bg-indigo-600 px-5 py-2 text-center text-sm font-semibold text-white">
                                    Dashboard
                                </Link>
                            ) : (
                                <>
                                    <Link href={route('login')} className="rounded-full border border-gray-300 px-5 py-2 text-center text-sm font-medium text-gray-700">
                                        Masuk
                                    </Link>
                                    <Link href={route('register')} className="rounded-full bg-indigo-600 px-5 py-2 text-center text-sm font-semibold text-white">
                                        Daftar
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                )}
            </header>

            {/* ── HERO ── */}
            <section className="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 pt-24 pb-20 sm:pt-32 sm:pb-28">
                {/* Blobs */}
                <div className="pointer-events-none absolute inset-0">
                    <div className="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-white/10 blur-3xl" />
                    <div className="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-pink-300/20 blur-3xl" />
                    <div className="absolute top-1/2 left-1/2 h-72 w-72 -translate-x-1/2 -translate-y-1/2 rounded-full bg-purple-300/20 blur-2xl" />
                </div>

                <div className="relative mx-auto max-w-6xl px-4 sm:px-6">
                    <div className="flex flex-col items-center gap-10 lg:flex-row lg:gap-16">
                        {/* Text */}
                        <div className="flex-1 text-center lg:text-left">
                            <span className="mb-4 inline-block rounded-full bg-white/20 px-4 py-1.5 text-sm font-semibold text-white backdrop-blur-sm">
                                ✨ Optik Terpercaya di Bekasi
                            </span>
                            <h1 className="text-4xl font-extrabold leading-tight text-white drop-shadow sm:text-5xl lg:text-6xl">
                                Lihat Dunia
                                <br />
                                <span className="text-yellow-300">Lebih Jelas</span>
                            </h1>
                            <p className="mt-5 max-w-lg text-lg text-white/80 mx-auto lg:mx-0">
                                Optik Satria Jaya hadir dengan pelayanan pemeriksaan mata profesional, koleksi frame terkini,
                                dan lensa berkualitas tinggi untuk kenyamanan penglihatan Anda.
                            </p>
                            <div className="mt-8 flex flex-wrap justify-center gap-4 lg:justify-start">
                                <a
                                    href="#layanan"
                                    className="rounded-full bg-white px-7 py-3 text-sm font-bold text-indigo-700 shadow-lg transition hover:bg-white/90 hover:shadow-xl"
                                >
                                    Lihat Layanan
                                </a>
                                <a
                                    href="#kontak"
                                    className="rounded-full border-2 border-white/60 px-7 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                                >
                                    Hubungi Kami
                                </a>
                            </div>
                        </div>

                        {/* Logo card */}
                        <div className="flex-shrink-0">
                            <div className="relative flex h-64 w-64 items-center justify-center rounded-3xl bg-white/20 shadow-2xl backdrop-blur-sm sm:h-72 sm:w-72">
                                <img
                                    src="/logo.png"
                                    alt="Optik Satria Jaya"
                                    className="h-40 w-40 object-contain drop-shadow-2xl sm:h-48 sm:w-48"
                                />
                                <div className="absolute -bottom-4 -right-4 rounded-2xl bg-yellow-400 px-4 py-2 shadow-lg">
                                    <span className="text-xs font-bold text-yellow-900">Sejak 2010</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Stats */}
                    <div className="mt-16 grid grid-cols-2 gap-4 sm:grid-cols-4">
                        {[
                            { value: '15+', label: 'Tahun Pengalaman' },
                            { value: '5000+', label: 'Pelanggan Puas' },
                            { value: '2', label: 'Cabang' },
                            { value: '100%', label: 'Garansi Produk' },
                        ].map((s) => (
                            <div key={s.label} className="rounded-2xl bg-white/15 p-4 text-center backdrop-blur-sm">
                                <div className="text-2xl font-extrabold text-white">{s.value}</div>
                                <div className="mt-1 text-xs text-white/70">{s.label}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── LAYANAN ── */}
            <section id="layanan" className="bg-gray-50 py-20">
                <div className="mx-auto max-w-6xl px-4 sm:px-6">
                    <div className="mb-12 text-center">
                        <span className="text-sm font-semibold uppercase tracking-widest text-indigo-600">Layanan Kami</span>
                        <h2 className="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                            Apa yang Kami Tawarkan?
                        </h2>
                        <p className="mt-3 text-gray-500">
                            Kami memberikan layanan terbaik untuk memenuhi kebutuhan penglihatan Anda.
                        </p>
                    </div>

                    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {SERVICES.map(({ icon: Icon, title, desc }) => (
                            <div
                                key={title}
                                className="group rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:shadow-lg"
                            >
                                <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white">
                                    <Icon className="h-6 w-6" />
                                </div>
                                <h3 className="mb-2 font-semibold text-gray-900">{title}</h3>
                                <p className="text-sm leading-relaxed text-gray-500">{desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── CTA BAND ── */}
            <section className="bg-gradient-to-r from-indigo-600 to-purple-600 py-14">
                <div className="mx-auto max-w-3xl px-4 text-center sm:px-6">
                    <h2 className="text-2xl font-extrabold text-white sm:text-3xl">
                        Sudah siap periksa mata?
                    </h2>
                    <p className="mt-3 text-white/80">
                        Kunjungi salah satu cabang kami atau hubungi kami sekarang untuk konsultasi gratis.
                    </p>
                    <div className="mt-7 flex flex-wrap justify-center gap-4">
                        <a
                            href="https://wa.me/6281909419741"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="rounded-full bg-white px-7 py-3 text-sm font-bold text-indigo-700 shadow-lg transition hover:bg-white/90"
                        >
                            💬 Hubungi via WhatsApp
                        </a>
                        <a
                            href="#cabang"
                            className="rounded-full border-2 border-white/60 px-7 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                        >
                            Cari Cabang Terdekat
                        </a>
                    </div>
                </div>
            </section>

            {/* ── CABANG ── */}
            <section id="cabang" className="bg-white py-20">
                <div className="mx-auto max-w-6xl px-4 sm:px-6">
                    <div className="mb-12 text-center">
                        <span className="text-sm font-semibold uppercase tracking-widest text-indigo-600">Lokasi</span>
                        <h2 className="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">Cabang Kami</h2>
                        <p className="mt-3 text-gray-500">Temukan kami di dua lokasi strategis di Bekasi.</p>
                    </div>

                    <div className="grid gap-6 sm:grid-cols-2">
                        {BRANCHES.map((b) => (
                            <div key={b.name} className="flex gap-4 rounded-2xl border border-gray-100 bg-gray-50 p-6 shadow-sm">
                                <div className="mt-1 flex-shrink-0">
                                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                                        <MapPin className="h-5 w-5" />
                                    </div>
                                </div>
                                <div>
                                    <h3 className="font-semibold text-gray-900">{b.name}</h3>
                                    <p className="mt-1 text-sm text-gray-500">{b.address}</p>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="mt-6 flex justify-center gap-6 text-sm text-gray-600">
                        <a
                            href="tel:+6281909419741"
                            className="flex items-center gap-2 hover:text-indigo-600 transition-colors"
                        >
                            <Phone className="h-4 w-4 text-indigo-500" />
                            0819-0941-9741
                        </a>
                        <a
                            href="tel:+6285311123440"
                            className="flex items-center gap-2 hover:text-indigo-600 transition-colors"
                        >
                            <Phone className="h-4 w-4 text-indigo-500" />
                            0853-1112-3440
                        </a>
                    </div>
                </div>
            </section>

            {/* ── TESTIMONI ── */}
            <section id="testimoni" className="bg-gray-50 py-20">
                <div className="mx-auto max-w-6xl px-4 sm:px-6">
                    <div className="mb-12 text-center">
                        <span className="text-sm font-semibold uppercase tracking-widest text-indigo-600">Testimoni</span>
                        <h2 className="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                            Kata Pelanggan Kami
                        </h2>
                    </div>

                    <div className="grid gap-6 sm:grid-cols-3">
                        {TESTIMONIALS.map((t) => (
                            <div key={t.name} className="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                                <div className="mb-3 flex gap-1">
                                    {Array.from({ length: t.rating }).map((_, i) => (
                                        <Star key={i} className="h-4 w-4 fill-yellow-400 text-yellow-400" />
                                    ))}
                                </div>
                                <p className="text-sm leading-relaxed text-gray-600 italic">"{t.text}"</p>
                                <p className="mt-4 text-sm font-semibold text-gray-900">— {t.name}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── KONTAK / FOOTER ── */}
            <footer id="kontak" className="bg-gradient-to-br from-gray-900 to-indigo-950 py-16 text-white">
                <div className="mx-auto max-w-6xl px-4 sm:px-6">
                    <div className="grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                        {/* Brand */}
                        <div>
                            <div className="flex items-center gap-3">
                                <img src="/logo.png" alt="Optik Satria Jaya" className="h-10 w-auto brightness-200" />
                                <span className="text-lg font-bold">Optik Satria Jaya</span>
                            </div>
                            <p className="mt-3 text-sm text-gray-400 leading-relaxed">
                                Memberikan layanan optik terbaik dengan produk berkualitas dan optometris berpengalaman sejak 2010.
                            </p>
                        </div>

                        {/* Cabang */}
                        <div>
                            <h4 className="mb-4 text-sm font-semibold uppercase tracking-widest text-gray-400">Lokasi</h4>
                            <ul className="space-y-3 text-sm text-gray-300">
                                {BRANCHES.map((b) => (
                                    <li key={b.name}>
                                        <p className="font-medium text-white">{b.name}</p>
                                        <p className="text-gray-400">{b.address}</p>
                                    </li>
                                ))}
                            </ul>
                        </div>

                        {/* Kontak */}
                        <div>
                            <h4 className="mb-4 text-sm font-semibold uppercase tracking-widest text-gray-400">Kontak</h4>
                            <ul className="space-y-3 text-sm text-gray-300">
                                <li className="flex items-center gap-2">
                                    <Phone className="h-4 w-4 text-indigo-400" />
                                    <a href="tel:+6281909419741" className="hover:text-white transition-colors">0819-0941-9741</a>
                                </li>
                                <li className="flex items-center gap-2">
                                    <Phone className="h-4 w-4 text-indigo-400" />
                                    <a href="tel:+6285311123440" className="hover:text-white transition-colors">0853-1112-3440</a>
                                </li>
                            </ul>
                            {auth.user ? (
                                <Link
                                    href={route('dashboard')}
                                    className="mt-6 inline-block rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors"
                                >
                                    Dashboard
                                </Link>
                            ) : (
                                <Link
                                    href={route('login')}
                                    className="mt-6 inline-block rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors"
                                >
                                    Login Staff
                                </Link>
                            )}
                        </div>
                    </div>

                    <div className="mt-12 border-t border-white/10 pt-6 text-center text-xs text-gray-500">
                        © {new Date().getFullYear()} Optik Satria Jaya. Semua hak dilindungi.
                        {' · '}
                        <Link href={route('privacy')} className="hover:text-white transition-colors">Kebijakan Privasi</Link>
                    </div>
                </div>
            </footer>
        </>
    );
}
