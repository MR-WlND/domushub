@if($residents->isEmpty())
    <div class="residents-empty">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p>Không tìm thấy cư dân nào</p>
    </div>
@else
    <div class="residents-table-card">
        <div class="residents-table-wrap">
            <table class="residents-table">
                <thead>
                    <tr>
                        <th>Cư dân</th>
                        <th>Căn hộ</th>
                        <th>Tòa / Tầng</th>
                        <th>Liên hệ</th>
                        <th>Trạng thái</th>
                        <th>Ngày tham gia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($residents as $user)
                    @php
                        $apt = $user->apartment;
                        $block = $apt?->floor?->block;
                        $floor = $apt?->floor;
                    @endphp
                    <tr>
                        <td>
                            <div class="res-user-cell">
                                @if($apt)
                                    <a href="{{ route('admin.apartments.show', $apt->id) }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px; width: 100%;">
                                @endif
                                <div class="res-user-avatar">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                                    @else
                                        <span>{{ mb_substr($user->name ?? '?', 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="res-user-name" style="transition: color 0.2s ease;">{{ $user->name }}</span>
                                    @if($user->cccd)
                                        <span class="res-user-cccd">CCCD: {{ $user->cccd }}</span>
                                    @endif
                                </div>
                                @if($apt)
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($apt)
                                <a href="{{ route('admin.apartments.show', $apt->id) }}" style="text-decoration: none;">
                                    <span class="res-apt-number" style="border-bottom: 1px dashed rgba(11, 87, 208, 0.6); padding-bottom: 2px; transition: all 0.2s ease;">{{ $apt->apartment_number }}</span>
                                </a>
                            @else
                                <span class="res-apt-number">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="res-location-cell">
                                <span class="res-location-block">{{ $block->name ?? '—' }}</span>
                                @if($floor)
                                    <span class="res-location-floor">{{ $floor->name }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="res-contact-cell">
                                @if($user->phone)
                                    <a href="tel:{{ $user->phone }}" class="res-contact-phone">{{ $user->phone }}</a>
                                @endif
                                @if($user->email)
                                    <span class="res-contact-email">{{ $user->email }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php $status = $user->status ?? 'inactive'; @endphp
                            <span class="res-status res-status--{{ $status }}">
                                @switch($status)
                                    @case('active') Hoạt động @break
                                    @case('inactive') Ngừng hoạt động @break
                                    @case('banned') Bị khóa @break
                                    @default {{ $status }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            <span class="res-date">{{ $user->created_at?->format('d/m/Y') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($residents->hasPages())
        <div class="residents-pagination">{{ $residents->links() }}</div>
    @endif
@endif

<style>
.res-user-cell a:hover .res-user-name {
    color: #0b57d0 !important;
}
.res-user-cell a:hover .res-user-avatar {
    box-shadow: 0 0 0 2px #0b57d0;
    transition: all 0.2s ease;
}
</style>
