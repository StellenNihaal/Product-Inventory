<x-admin-layout>

    <style>
        /* Page header */
        .page-eyebrow {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #bc3f2c;
            margin-bottom: 8px;
        }

        .page-title {
            font-size: 30px;
            font-weight: 600;
            letter-spacing: -0.02em;
            color: #1c1a16;
            line-height: 1.2;
        }

        .page-title em {
            font-family: 'Serif', Georgia, serif;
            font-style: italic;
            font-weight: 400;
            color: #1d3c68;
            letter-spacing: 0;
        }

        .page-sub {
            font-size: 15px;
            color: #8a8478;
            margin-top: 6px;
            font-weight: 400;
        }

        /* Search panel */
        .search-panel {
            background: #ffffff;
            border: 1px solid rgba(28, 26, 22, 0.10);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 220px;
            border: 1px solid rgba(28, 26, 22, 0.14);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 15px;
            font-family: inherit;
            color: #1c1a16;
            background: #fbfafa;
            outline: none;
            transition: border-color 0.16s, box-shadow 0.16s;
        }

        .search-input::placeholder {
            color: #8a8478;
        }

        .search-input:focus {
            border-color: #1d3c68;
            box-shadow: 0 0 0 3px rgba(29, 60, 104, 0.10);
            background: #ffffff;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #1d3c68;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.16s, box-shadow 0.16s;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: #112844;
            box-shadow: 0 6px 16px -8px rgba(17, 40, 68, 0.5);
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #5b554b;
            border: 1px solid rgba(28, 26, 22, 0.14);
            border-radius: 10px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.16s, border-color 0.16s;
            text-decoration: none;
        }

        .btn-ghost:hover {
            background: #f3f2f1;
            border-color: rgba(28, 26, 22, 0.22);
            color: #1c1a16;
        }

        /* Flash */
        .flash-success {
            background: #f0fdf4;
            border: 1px solid rgba(63, 125, 82, 0.25);
            color: #3f7d52;
            border-radius: 12px;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Table panel */
        .panel {
            background: #ffffff;
            border: 1px solid rgba(28, 26, 22, 0.10);
            border-radius: 16px;
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead th {
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #8a8478;
            padding: 14px 24px;
            background: #fbfafa;
            border-bottom: 1px solid rgba(28, 26, 22, 0.10);
        }

        .data-table tbody td {
            padding: 16px 24px;
            font-size: 15px;
            color: #5b554b;
            border-bottom: 1px solid rgba(28, 26, 22, 0.07);
            vertical-align: middle;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr {
            transition: background 0.14s;
        }

        .data-table tbody tr:hover {
            background: #fbfafa;
        }

        /* User cell */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            object-fit: cover;
            flex: none;
        }

        .user-avatar-initial {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #dfe6ef;
            display: grid;
            place-items: center;
            color: #1d3c68;
            font-size: 13px;
            font-weight: 700;
            flex: none;
        }

        .user-name {
            font-weight: 600;
            color: #1c1a16;
        }

        /* Status pill */
        .pill-active {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 11px;
            border-radius: 100px;
            background: #f0fdf4;
            color: #3f7d52;
        }

        .pill-inactive {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 11px;
            border-radius: 100px;
            background: #f9ede8;
            color: #bc3f2c;
        }

        /* Action buttons */
        .btn-deactivate {
            font-size: 13px;
            font-weight: 500;
            padding: 5px 14px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            background: #f9ede8;
            color: #bc3f2c;
            transition: background 0.14s;
        }

        .btn-deactivate:hover {
            background: #f0ddd6;
        }

        .btn-activate {
            font-size: 13px;
            font-weight: 500;
            padding: 5px 14px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            background: #f0fdf4;
            color: #3f7d52;
            transition: background 0.14s;
        }

        .btn-activate:hover {
            background: #dcfce7;
        }

        /* Empty state */
        .empty-row td {
            padding: 48px 24px;
            text-align: center;
            color: #8a8478;
            font-size: 15px;
        }

        /* Pagination wrapper */
        .pagination-wrap {
            padding: 16px 24px;
            border-top: 1px solid rgba(28, 26, 22, 0.08);
        }
    </style>

    {{-- Page heading --}}
    <div style="margin-bottom:26px;">
        <div class="page-eyebrow">Management</div>
        <h2 class="page-title">Users <em>& Access</em></h2>
        <p class="page-sub">View, search and manage registered users.</p>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="flash-success">
            <svg style="width:16px;height:16px;flex:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <form id="searchForm" method="GET" action="{{ route('admin.users.index') }}" class="search-panel">
        <svg style="width:18px;height:18px;color:#8a8478;flex:none;" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input type="text" name="search" id="searchInput" placeholder="Search by name or email…"
            value="{{ request('search') }}" class="search-input">
        <button type="submit" class="btn-primary">
            <svg style="width:15px;height:15px;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            Search
        </button>
        @if (request()->filled('search'))
            <a href="{{ route('admin.users.index') }}" class="btn-ghost">
                <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
                Reset
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="panel">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr id="user-row-{{ $user->id }}">
                            <td style="color:#8a8478;">{{ $loop->iteration }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    @if ($user->profile_photo)
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}" class="user-avatar" />
                                    @else
                                        <div class="user-avatar-initial">{{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="user-name">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->country ?? '—' }}</td>
                            <td style="font-size:13px; color:#8a8478;">{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <span id="status-{{ $user->id }}"
                                    class="{{ $user->is_active ? 'pill-active' : 'pill-inactive' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <button
                                    onclick="toggleUser({{ $user->id }}, '{{ route('admin.users.toggle', $user) }}')"
                                    id="toggle-btn-{{ $user->id }}"
                                    class="{{ $user->is_active ? 'btn-deactivate' : 'btn-activate' }}">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="7">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $users->links() }}
        </div>
    </div>

    <script>
        (function() {
            const searchInput = document.getElementById('searchInput');
            const form = document.getElementById('searchForm');
            let timer;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const cursorPos = this.selectionStart;
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        const params = new URLSearchParams(new FormData(form));
                        loadPage(`{{ route('admin.users.index') }}?${params.toString()}`)
                            .then(() => {
                                const newInput = document.getElementById('searchInput');
                                if (newInput) {
                                    newInput.focus();
                                    newInput.setSelectionRange(cursorPos, cursorPos);
                                }
                            });
                    }, 350);
                });
            }
        })();

        function toggleUser(id, url) {
            const form = new FormData();
            form.append('_token', '{{ csrf_token() }}');
            form.append('_method', 'PATCH');

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: form
                })
                .then(res => res.json())
                .then(data => {
                    const status = document.getElementById(`status-${id}`);
                    const btn = document.getElementById(`toggle-btn-${id}`);

                    if (data.is_active) {
                        status.textContent = 'Active';
                        status.className = 'pill-active';
                        btn.textContent = 'Deactivate';
                        btn.className = 'btn-deactivate';
                    } else {
                        status.textContent = 'Inactive';
                        status.className = 'pill-inactive';
                        btn.textContent = 'Activate';
                        btn.className = 'btn-activate';
                    }
                });
        }
    </script>

</x-admin-layout>
