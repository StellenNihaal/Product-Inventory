<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventory — Smart Product Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap"
        rel="stylesheet" />
    <style>
        body {
            font-family: 'Hanken Grotesk', system-ui, sans-serif;
        }

        .font-serif-italic {
            font-family: 'Instrument Serif', Georgia, serif;
            font-style: italic;
            font-weight: 400;
        }

        .hero-visual-bg {
            background: linear-gradient(155deg, #20406d 0%, #112844 100%);
        }

        .orb {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 30%;
            height: 30%;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #e9dfcd, #bc3f2c 130%);
            box-shadow: 0 20px 50px -10px rgba(188, 63, 44, 0.6);
        }

        .cat-card {
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }

        .cat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 48px -28px rgba(28, 26, 22, 0.4);
        }

        .btn-arrow {
            transition: transform 0.2s;
        }

        .cat-card:hover .btn-arrow {
            transform: translateX(4px);
        }

        .nav-blur {
            background: rgba(251, 250, 250, 0.86);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>

<body class="bg-[#fbfafa] text-[#1c1a16] antialiased">

    {{-- ANNOUNCEMENT BAR --}}
    <div class="bg-[#112844] text-[#fbfafa]">
        <div class="max-w-7xl mx-auto px-10 h-9 flex items-center justify-center gap-3">
            <span class="w-1.5 h-1.5 rounded-full bg-[#bc3f2c] inline-block"></span>
            <span class="text-xs font-semibold tracking-widest uppercase">
                {{ $cms['announce_text'] }}
            </span>
        </div>
    </div>

    {{-- NAVBAR --}}
    <header class="sticky top-0 z-50 nav-blur border-b border-black/10">
        <div class="max-w-7xl mx-auto px-10 h-[76px] flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 font-semibold text-[19px] tracking-tight">
                <span class="w-[30px] h-[30px] rounded-lg bg-[#1d3c68] relative flex-none">
                    <span class="absolute right-[6px] bottom-[6px] w-2 h-2 rounded-full bg-[#bc3f2c]"></span>
                </span>
                Product Inventory
            </a>
            <nav class="hidden md:flex items-center gap-10 text-[15px] font-medium">
                <a href="#categories" class="text-[#5b554b] hover:text-[#1c1a16] transition">Shop</a>
                <a href="#categories" class="text-[#5b554b] hover:text-[#1c1a16] transition">Categories</a>
                <a href="{{ route('login') }}" class="text-[#5b554b] hover:text-[#1c1a16] transition">Login</a>
                <a href="{{ route('register') }}" class="text-[#5b554b] hover:text-[#1c1a16] transition">Register</a>
            </nav>
            <div class="flex items-center gap-5">
                <a href="{{ route('login') }}"
                    class="text-[15px] font-medium text-[#5b554b] hover:text-[#1c1a16] transition">Login</a>
                <a href="{{ route('register') }}"
                    class="flex items-center gap-2 bg-[#1d3c68] text-[#fbfafa] font-semibold text-[15px] px-6 py-3 rounded-full hover:bg-[#112844] transition">
                    Shop now
                </a>
            </div>
        </div>
    </header>

    {{-- HERO --}}
    <section class="py-22 md:py-24">
        <div class="max-w-7xl mx-auto px-10">
            <div class="flex flex-col items-center text-center gap-14">
                <div class="flex flex-col items-center">

                    {{-- Eyebrow --}}
                    <span
                        class="flex items-center gap-2 text-[13px] font-semibold tracking-widest uppercase text-[#bc3f2c] mb-6">
                        <span class="w-6 h-px bg-[#bc3f2c]"></span>
                        {{ $cms['hero_eyebrow'] }}
                    </span>

                    {{-- Title with italic word --}}
                    <h1
                        class="font-semibold text-[clamp(44px,5.6vw,76px)] leading-[1.05] tracking-tight max-w-[14ch] text-balance">
                        @php
                            $title = $cms['hero_title'];
                            $italicWord = $cms['hero_italic_word'];
                            $parts = explode($italicWord, $title, 2);
                        @endphp
                        {{ $parts[0] ?? '' }}<span
                            class="font-serif-italic text-[#1d3c68]">{{ $italicWord }}</span>{{ $parts[1] ?? '' }}
                    </h1>

                    {{-- Subtitle --}}
                    <p class="mt-6 text-[18.5px] leading-relaxed text-[#5b554b] max-w-[46ch]">
                        {{ $cms['hero_subtitle'] }}
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="mt-9 flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('register') }}"
                            class="flex items-center gap-2 bg-[#1d3c68] text-[#fbfafa] font-semibold text-[15px] px-6 py-3.5 rounded-full hover:bg-[#112844] transition">
                            {{ $cms['hero_btn_primary'] }} <span class="btn-arrow">→</span>
                        </a>
                        <a href="{{ route('login') }}"
                            class="flex items-center gap-2 bg-transparent border border-[#5b554b]/40 text-[#1c1a16] font-semibold text-[15px] px-6 py-3.5 rounded-full hover:border-[#1c1a16] transition">
                            {{ $cms['hero_btn_secondary'] }}
                        </a>
                    </div>

                    {{-- Stats --}}
                    <div
                        class="mt-14 flex flex-wrap justify-center gap-11 border-t border-black/10 pt-7 w-full max-w-2xl">
                        <div class="text-center">
                            <div class="font-serif-italic text-[38px] leading-none">{{ $totalProducts }}+</div>
                            <div class="mt-1.5 text-[13px] text-[#8a8478] tracking-wide">Products</div>
                        </div>
                        <div class="text-center">
                            <div class="font-serif-italic text-[38px] leading-none">{{ $totalCategories }}+</div>
                            <div class="mt-1.5 text-[13px] text-[#8a8478] tracking-wide">Categories</div>
                        </div>
                        <div class="text-center">
                            <div class="font-serif-italic text-[38px] leading-none">{{ $totalBrands }}+</div>
                            <div class="mt-1.5 text-[13px] text-[#8a8478] tracking-wide">Brands</div>
                        </div>
                        <div class="text-center">
                            <div class="font-serif-italic text-[38px] leading-none">{{ $totalUsers }}+</div>
                            <div class="mt-1.5 text-[13px] text-[#8a8478] tracking-wide">Users</div>
                        </div>
                    </div>
                </div>


            </div>
    </section>



    {{-- FOOTER --}}
    <footer class="bg-[#112844] text-[#fbfafa] pt-20 pb-9 mt-5">
        <div class="max-w-7xl mx-auto px-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 pb-14 border-b border-[#fbfafa]/[0.14]">

                {{-- Brand --}}
                <div class="lg:col-span-1">
                    <a href="/"
                        class="flex items-center gap-3 font-semibold text-[22px] tracking-tight text-[#fbfafa]">
                        <span class="w-[30px] h-[30px] rounded-lg bg-[#fbfafa] relative flex-none">
                            <span class="absolute right-[6px] bottom-[6px] w-2 h-2 rounded-full bg-[#bc3f2c]"></span>
                        </span>
                        Product Inventory
                    </a>
                    <p class="mt-5 text-[#fbfafa]/65 text-[15px] leading-[1.65] max-w-[34ch]">
                        {{ $cms['footer_brand_desc'] }}
                    </p>
                </div>

                {{-- Shop --}}
                <div>
                    <h4 class="text-[13px] tracking-widest uppercase text-[#fbfafa]/50 font-semibold mb-4">Shop</h4>
                    <ul class="flex flex-col gap-3">
                        @foreach ($categories->take(4) as $category)
                            <li>
                                <a href="{{ route('register') }}"
                                    class="text-[#fbfafa]/85 text-[15px] hover:text-[#fbfafa] transition">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Account --}}
                <div>
                    <h4 class="text-[13px] tracking-widest uppercase text-[#fbfafa]/50 font-semibold mb-4">Account</h4>
                    <ul class="flex flex-col gap-3">
                        <li><a href="{{ route('login') }}"
                                class="text-[#fbfafa]/85 text-[15px] hover:text-[#fbfafa] transition">Login</a></li>
                        <li><a href="{{ route('register') }}"
                                class="text-[#fbfafa]/85 text-[15px] hover:text-[#fbfafa] transition">Register</a></li>
                        <li><a href="{{ route('login') }}"
                                class="text-[#fbfafa]/85 text-[15px] hover:text-[#fbfafa] transition">Dashboard</a>
                        </li>
                    </ul>
                </div>

                {{-- Support --}}
                <div>
                    <h4 class="text-[13px] tracking-widest uppercase text-[#fbfafa]/50 font-semibold mb-4">Support</h4>
                    <ul class="flex flex-col gap-3">
                        <li><a href="#"
                                class="text-[#fbfafa]/85 text-[15px] hover:text-[#fbfafa] transition">Contact</a></li>
                        <li><a href="#"
                                class="text-[#fbfafa]/85 text-[15px] hover:text-[#fbfafa] transition">FAQ</a></li>
                    </ul>
                </div>
            </div>

            <div
                class="mt-8 flex flex-col md:flex-row items-center justify-between gap-5 text-[13.5px] text-[#fbfafa]/55">
                <span>© {{ date('Y') }} {{ $cms['footer_copyright'] }}</span>
                <div class="flex gap-6">
                    <a href="{{ $cms['footer_instagram'] }}" class="hover:text-[#fbfafa] transition">Instagram</a>
                    <a href="{{ $cms['footer_twitter'] }}" class="hover:text-[#fbfafa] transition">Twitter</a>
                    <a href="{{ $cms['footer_linkedin'] }}" class="hover:text-[#fbfafa] transition">LinkedIn</a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
