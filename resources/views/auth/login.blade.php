<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login — BPS Kota Bogor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden" style="background-color: #f1f5f9;">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 z-0" style="
        background-color: #f1f5f9;
        background-image:
            radial-gradient(circle at 1px 1px, #cbd5e1 1px, transparent 0);
        background-size: 32px 32px;
    "></div>

    {{-- Decorative shapes --}}
    <div class="absolute top-0 left-0 w-full h-full z-0 overflow-hidden pointer-events-none">
        {{-- Lingkaran besar kanan atas --}}
        <div style="
            position: absolute;
            top: -120px;
            right: -80px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            border: 40px solid rgba(59, 130, 246, 0.06);
        "></div>

        {{-- Lingkaran sedang kanan atas --}}
        <div style="
            position: absolute;
            top: 60px;
            right: 80px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 20px solid rgba(59, 130, 246, 0.05);
        "></div>

        {{-- Lingkaran besar kiri bawah --}}
        <div style="
            position: absolute;
            bottom: -150px;
            left: -100px;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            border: 50px solid rgba(59, 130, 246, 0.05);
        "></div>

        {{-- Kotak dekorasi kanan bawah --}}
        <div style="
            position: absolute;
            bottom: 60px;
            right: 40px;
            width: 120px;
            height: 120px;
            border: 12px solid rgba(59, 130, 246, 0.07);
            transform: rotate(45deg);
        "></div>

        {{-- Garis grid horizontal --}}
        <svg style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0.03;" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
                    <path d="M 60 0 L 0 0 0 60" fill="none" stroke="#1e40af" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
        </svg>

        {{-- Titik-titik digital pojok kiri atas --}}
        <svg style="position:absolute;top:20px;left:20px;opacity:0.08;" width="120" height="120" xmlns="http://www.w3.org/2000/svg">
            <g fill="#1e40af">
                @for($r = 0; $r < 5; $r++)
                    @for($c = 0; $c < 5; $c++)
                        <circle cx="{{ $c * 24 + 4 }}" cy="{{ $r * 24 + 4 }}" r="2.5"/>
                    @endfor
                @endfor
            </g>
        </svg>

        {{-- Titik-titik digital pojok kanan bawah --}}
        <svg style="position:absolute;bottom:20px;right:20px;opacity:0.08;" width="120" height="120" xmlns="http://www.w3.org/2000/svg">
            <g fill="#1e40af">
                @for($r = 0; $r < 5; $r++)
                    @for($c = 0; $c < 5; $c++)
                        <circle cx="{{ $c * 24 + 4 }}" cy="{{ $r * 24 + 4 }}" r="2.5"/>
                    @endfor
                @endfor
            </g>
        </svg>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex w-full max-w-3xl min-h-96 relative z-10">

        {{-- SISI KIRI --}}
        <div class="w-1/2 bg-blue-500 p-10 flex flex-col justify-between relative overflow-hidden">

            {{-- Lingkaran dekorasi --}}
            <div class="absolute -bottom-16 -right-16 w-64 h-64 bg-blue-400 rounded-full opacity-50"></div>
            <div class="absolute -bottom-8 -right-8 w-40 h-40 bg-white rounded-full opacity-10"></div>

            {{-- Konten kiri --}}
            <div class="relative z-10">
                {{-- Logo BPS --}}
                <div class="mb-8">
                    <img src="{{ asset('images/logo-bps.png') }}"
                         alt="Logo BPS"
                         class="w-16 h-16 object-contain"
                         onerror="this.style.display='none'" />
                </div>

                <h1 class="text-white text-2xl font-bold leading-snug mb-4">
                    Selamat Datang di Aplikasi Pelayanan Penyediaan Peta
                </h1>

                <div class="w-10 h-1 bg-white rounded mb-4"></div>

                <p class="text-blue-100 text-sm leading-relaxed">
                    BPS Kota Bogor — Sistem pengelolaan arsip peta SLS untuk mendukung kegiatan statistik yang akurat dan terpercaya.
                </p>
            </div>

            <div class="relative z-10">
                <p class="text-blue-200 text-xs">
                    © {{ date('Y') }} BPS Kota Bogor
                </p>
            </div>

        </div>

        {{-- SISI KANAN --}}
        <div class="w-1/2 p-10 flex flex-col justify-center">

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Masuk</h2>
                <div class="w-8 h-1 bg-blue-500 rounded"></div>
            </div>

            {{-- Form Login --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">
                        Email
                    </label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Masukkan email..."
                           required autofocus
                           class="w-full border-0 border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none pb-2 text-sm text-gray-700 bg-transparent transition-colors
                           @error('email') border-red-400 @enderror" />
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">
                        Kata Sandi
                    </label>
                    <input type="password"
                           name="password"
                           placeholder="Masukkan kata sandi..."
                           required
                           class="w-full border-0 border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none pb-2 text-sm text-gray-700 bg-transparent transition-colors
                           @error('password') border-red-400 @enderror" />
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                           class="w-4 h-4 accent-blue-500" />
                    <label for="remember" class="text-sm text-gray-500">
                        Ingat saya
                    </label>
                </div>

                {{-- Error umum --}}
                @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <p class="text-red-600 text-xs">{{ $errors->first() }}</p>
                    </div>
                @endif

                {{-- Tombol Login --}}
                <div class="pt-2">
                    <button type="submit"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl transition-colors text-sm tracking-wide uppercase">
                        Login
                    </button>
                </div>

                {{-- Info lupa password --}}
                <p class="text-center text-xs text-gray-400 italic">
                    Lupa kata sandi? Hubungi Supervisor.
                </p>

            </form>

        </div>

    </div>

</body>
</html>