<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <style>
        /* Auth layout — split screen premium */
        .auth-left-panel {
            background-color: #0f172a;
            background-image:
                radial-gradient(ellipse 80% 60% at 20% 40%, rgba(37, 99, 235, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 80%, rgba(99, 102, 241, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 60% 10%, rgba(14, 165, 233, 0.10) 0%, transparent 50%);
        }

        .auth-grid-overlay {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        .auth-orb-1 {
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.22) 0%, transparent 70%);
            filter: blur(40px);
            position: absolute;
            top: -80px;
            left: -80px;
        }

        .auth-orb-2 {
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, transparent 70%);
            filter: blur(32px);
            position: absolute;
            bottom: 80px;
            right: -60px;
        }

        .auth-orb-3 {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 70%);
            filter: blur(24px);
            position: absolute;
            bottom: 30%;
            left: 30%;
        }

        /* Geometric corner accent */
        .auth-corner-tl {
            position: absolute;
            top: 0;
            left: 0;
            width: 120px;
            height: 120px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            border-left: 1px solid rgba(255, 255, 255, 0.08);
        }

        .auth-corner-br {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 120px;
            height: 120px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Feature stat line */
        .auth-stat-line {
            border-left: 2px solid rgba(37, 99, 235, 0.6);
            padding-left: 14px;
        }

        /* Form panel */
        .auth-right-panel {
            background: #ffffff;
        }

        .dark .auth-right-panel {
            background: #1e1e2e;
        }

        /* Subtle animated ring on logo */
        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .auth-logo-ring {
            animation: spin-slow 18s linear infinite;
        }

        /* Divider fade */
        .auth-divider {
            background: linear-gradient(to right, transparent, rgba(37, 99, 235, 0.3), transparent);
            height: 1px;
        }
    </style>
</head>

<body class="antialiased overflow-hidden">
    <div class="flex h-screen w-screen">

        {{-- ═══════════════════════════════════════
                LEFT PANEL — Visual / Brand
            ═══════════════════════════════════════ --}}
        <div class="auth-left-panel hidden lg:flex lg:w-[52%] xl:w-[55%] relative flex-col overflow-hidden">

            {{-- Grid overlay --}}
            <div class="auth-grid-overlay absolute inset-0 pointer-events-none"></div>

            {{-- Ambient orbs --}}
            <div class="auth-orb-1"></div>
            <div class="auth-orb-2"></div>
            <div class="auth-orb-3"></div>

            {{-- Geometric corners --}}
            <div class="auth-corner-tl"></div>
            <div class="auth-corner-br"></div>

            {{-- Floating dot accent — top right --}}
            <div class="absolute top-8 right-8 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-blue-400 opacity-60"></div>
                <div class="w-1.5 h-1.5 rounded-full bg-indigo-400 opacity-40"></div>
                <div class="w-1 h-1 rounded-full bg-sky-400 opacity-30"></div>
            </div>

            {{-- Main Content --}}
            <div class="relative z-10 flex flex-col justify-between h-full p-12 xl:p-16">

                {{-- Top: Logo + App Name --}}
                <div class="flex items-center gap-3">
                    <div class="relative">
                        {{-- Spinning ring --}}
                        <svg class="auth-logo-ring absolute inset-0 w-12 h-12 opacity-20" viewBox="0 0 48 48"
                            fill="none">
                            <circle cx="24" cy="24" r="22" stroke="url(#ring-grad)" stroke-width="1"
                                stroke-dasharray="8 4" />
                            <defs>
                                <linearGradient id="ring-grad" x1="0" y1="0" x2="48"
                                    y2="48" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#3b82f6" />
                                    <stop offset="1" stop-color="#6366f1" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div
                            class="relative w-12 h-12 bg-blue-600/20 border border-blue-500/30 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <x-ui.app-logo-icon class="w-7 h-7 text-blue-400" />
                        </div>
                    </div>
                    <span class="text-white font-semibold text-lg tracking-wide">
                        {{ config('app.name', 'FinanceOS') }}
                    </span>
                </div>

                {{-- Center: Hero text --}}
                <div class="space-y-8">
                    <div class="space-y-4">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 backdrop-blur-sm">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></div>
                            <span class="text-blue-300 text-xs font-medium tracking-wider uppercase">Finance
                                Management</span>
                        </div>

                        <h1 class="text-4xl xl:text-5xl font-bold text-white leading-[1.15] tracking-tight">
                            Kendali Penuh<br>
                            <span
                                class="text-transparent bg-clip-text bg-linear-to-r from-blue-400 via-indigo-400 to-sky-300">
                                Atas Keuangan
                            </span><br>
                            Bisnis Anda
                        </h1>

                        <p class="text-slate-400 text-base xl:text-lg leading-relaxed max-w-sm">
                            Platform terintegrasi untuk invoice, pembayaran, arus kas, dan laporan keuangan dalam satu
                            tempat.
                        </p>
                    </div>

                    {{-- Divider --}}
                    <div class="auth-divider w-48"></div>

                    {{-- Stats / feature highlights --}}
                    <div class="grid grid-cols-2 gap-5">
                        <div class="auth-stat-line space-y-0.5">
                            <div class="text-white font-bold text-2xl">121+</div>
                            <div class="text-slate-500 text-xs">Komponen Livewire</div>
                        </div>
                        <div class="auth-stat-line space-y-0.5">
                            <div class="text-white font-bold text-2xl">Multi</div>
                            <div class="text-slate-500 text-xs">Role & Permission</div>
                        </div>
                        <div class="auth-stat-line space-y-0.5">
                            <div class="text-white font-bold text-2xl">Real-time</div>
                            <div class="text-slate-500 text-xs">Notifikasi & Laporan</div>
                        </div>
                        <div class="auth-stat-line space-y-0.5">
                            <div class="text-white font-bold text-2xl">PDF</div>
                            <div class="text-slate-500 text-xs">Invoice & Export</div>
                        </div>
                    </div>
                </div>

                {{-- Bottom: trust badge --}}
                <div class="flex items-center gap-3">
                    <div class="flex -space-x-2">
                        <div
                            class="w-7 h-7 rounded-full bg-linear-to-br from-blue-500 to-indigo-600 border-2 border-slate-900 flex items-center justify-center">
                            <span class="text-white text-[9px] font-bold">A</span>
                        </div>
                        <div
                            class="w-7 h-7 rounded-full bg-linear-to-br from-emerald-500 to-teal-600 border-2 border-slate-900 flex items-center justify-center">
                            <span class="text-white text-[9px] font-bold">B</span>
                        </div>
                        <div
                            class="w-7 h-7 rounded-full bg-linear-to-br from-violet-500 to-purple-600 border-2 border-slate-900 flex items-center justify-center">
                            <span class="text-white text-[9px] font-bold">C</span>
                        </div>
                    </div>
                    <div class="text-slate-500 text-xs">
                        Dipercaya oleh tim keuangan profesional
                    </div>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════
                RIGHT PANEL — Form
            ═══════════════════════════════════════ --}}
        <div class="auth-right-panel flex-1 flex flex-col items-center justify-center overflow-y-auto relative">

            {{-- Mobile: show logo at top --}}
            <div class="lg:hidden absolute top-6 left-6 flex items-center gap-2.5">
                <div class="w-9 h-9 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                    <x-ui.app-logo-icon class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                </div>
                <span
                    class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ config('app.name', 'FinanceOS') }}</span>
            </div>

            {{-- Subtle top-right decoration --}}
            <div class="hidden lg:block absolute top-0 right-0 w-48 h-48 opacity-[0.03] dark:opacity-[0.06]"
                style="background: radial-gradient(circle at top right, #2563eb, transparent 70%);">
            </div>

            {{-- Form container --}}
            <div class="w-full max-w-[400px] px-8 py-10 xl:px-12">
                {{ $slot }}
            </div>

            {{-- Bottom copyright --}}
            <div class="absolute bottom-5 text-xs text-slate-400 dark:text-slate-600 select-none">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>

        </div>

    </div>

    <tallstack-ui:script />
</body>

</html>
