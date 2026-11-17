<x-admin-layout>

<style>
    .stat-card {
        position: relative;
        background: #ffffff;
        border: 1px solid rgba(28,26,22,0.10);
        border-radius: 16px;
        padding: 24px 24px 20px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card::before {
        content: "";
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: var(--accent);
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px -24px rgba(28,26,22,0.30);
    }
    .stat-card .ico {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: grid; place-items: center;
        background: color-mix(in srgb, var(--accent) 12%, transparent);
        color: var(--accent);
    }
    .stat-card .ico svg { width: 20px; height: 20px; }
    .stat-card .num {
        font-family: 'Instrument Serif', Georgia, serif;
        font-size: 52px;
        line-height: 1;
        margin: 14px 0 16px;
        color: #1c1a16;
    }
    .stat-card .view-link {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 14px; font-weight: 600;
        color: var(--accent);
        text-decoration: none;
        transition: gap 0.2s;
    }
    .stat-card:hover .view-link { gap: 10px; }

    .c-blue  { --accent: #1d3c68; }
    .c-green { --accent: #3f7d52; }
    .c-amber { --accent: #c4922e; }
    .c-red   { --accent: #bc3f2c; }

    /* Panel */
    .panel {
        background: #ffffff;
        border: 1px solid rgba(28,26,22,0.10);
        border-radius: 16px;
        overflow: hidden;
        margin-top: 26px;
    }
    .panel-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 22px 26px;
        border-bottom: 1px solid rgba(28,26,22,0.10);
    }
    .panel-head h3 {
        font-size: 19px; font-weight: 600; letter-spacing: -0.015em;
        color: #1c1a16;
    }
    .panel-head a {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 14px; font-weight: 600;
        color: #1d3c68; text-decoration: none;
        transition: gap 0.2s;
    }
    .panel-head a:hover { gap: 10px; }

    /* Table */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th {
        text-align: left;
        font-size: 12px; font-weight: 600;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: #8a8478;
        padding: 14px 26px;
        background: #fbfafa;
        border-bottom: 1px solid rgba(28,26,22,0.10);
    }
    .data-table tbody td {
        padding: 18px 26px;
        font-size: 15px;
        color: #5b554b;
        border-bottom: 1px solid rgba(28,26,22,0.07);
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background 0.14s; }
    .data-table tbody tr:hover { background: #fbfafa; }
    .data-table .td-name { font-weight: 600; color: #1c1a16; }
    .data-table .td-price { font-weight: 600; color: #1c1a16; }
    .cat-pill {
        display: inline-block;
        font-size: 13px; font-weight: 500;
        padding: 3px 11px;
        border-radius: 100px;
        background: #f3f2f1;
        color: #5b554b;
    }
    .stock-dot {
        display: inline-flex; align-items: center; gap: 7px;
        font-weight: 500; color: #3f7d52;
    }
    .stock-dot::before {
        content: "";
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #3f7d52;
    }
    .empty-row td {
        padding: 40px 26px;
        text-align: center;
        color: #8a8478;
        font-size: 15px;
    }
</style>

{{-- Page header --}}
<div style="margin-bottom:26px;">
    <div style="font-size:12px; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:#bc3f2c; margin-bottom:8px;">Overview</div>
    <h2 style="font-size:30px; font-weight:600; letter-spacing:-0.02em; color:#1c1a16;">
        Here's your inventory
        <em style="font-family:'Serif',Georgia,serif; font-style:italic; font-weight:400; color:#1d3c68; letter-spacing:0;">at a glance</em>.
    </h2>
</div>

{{-- Stat Cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:20px;">

    <div class="stat-card c-blue">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
            <span style="font-size:15px; color:#5b554b; font-weight:500;">Total Products</span>
            <span class="ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            </span>
        </div>
        <div class="num">{{ $totalProducts }}</div>
        <a href="{{ route('admin.products.index') }}" class="view-link">View all →</a>
    </div>

    <div class="stat-card c-green">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
            <span style="font-size:15px; color:#5b554b; font-weight:500;">Total Users</span>
            <span class="ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
        </div>
        <div class="num">{{ $totalUsers }}</div>
        <a href="{{ route('admin.users.index') }}" class="view-link">View all →</a>
    </div>

    <div class="stat-card c-amber">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
            <span style="font-size:15px; color:#5b554b; font-weight:500;">Total Categories</span>
            <span class="ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </span>
        </div>
        <div class="num">{{ $totalCategories }}</div>
        <a href="{{ route('admin.categories.index') }}" class="view-link">View all →</a>
    </div>

    <div class="stat-card c-red">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
            <span style="font-size:15px; color:#5b554b; font-weight:500;">Total Brands</span>
            <span class="ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            </span>
        </div>
        <div class="num">{{ $totalBrands }}</div>
        <a href="{{ route('admin.brands.index') }}" class="view-link">View all →</a>
    </div>

</div>

{{-- Responsive stat grid --}}
<style>
    @media (max-width: 1080px) {
        div[style*="grid-template-columns:repeat(4,1fr)"] { grid-template-columns: repeat(2,1fr) !important; }
    }
    @media (max-width: 560px) {
        div[style*="grid-template-columns:repeat(4,1fr)"] { grid-template-columns: 1fr !important; }
    }
</style>

{{-- Recent Products --}}
<div class="panel">
    <div class="panel-head">
        <h3>Recent Products</h3>
        <a href="{{ route('admin.products.index') }}">View all →</a>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentProducts as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><span class="td-name">{{ $product->name }}</span></td>
                        <td><span class="cat-pill">{{ $product->category->name }}</span></td>
                        <td>{{ $product->brand->name }}</td>
                        <td><span class="td-price">₹{{ number_format($product->price, 2) }}</span></td>
                        <td><span class="stock-dot">{{ number_format($product->stock) }}</span></td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="6">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</x-admin-layout>