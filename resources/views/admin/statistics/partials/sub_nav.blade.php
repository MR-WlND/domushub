<div class="stat-sub-nav">
    <a href="{{ route('admin.statistics.finance', request()->only(['year', 'month', 'block_id'])) }}" class="stat-sub-nav__item {{ request()->routeIs('admin.statistics.finance') ? 'stat-sub-nav__item--active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        Tài chính & Tiêu thụ
    </a>
    <a href="{{ route('admin.statistics.operations', request()->only(['year', 'month', 'block_id'])) }}" class="stat-sub-nav__item {{ request()->routeIs('admin.statistics.operations') ? 'stat-sub-nav__item--active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        Vận hành & SLA
    </a>
    <a href="{{ route('admin.statistics.residents', request()->only(['year', 'month', 'block_id'])) }}" class="stat-sub-nav__item {{ request()->routeIs('admin.statistics.residents') ? 'stat-sub-nav__item--active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        Cư dân & Hạ tầng
    </a>
    <a href="{{ route('admin.amenities.statistics', request()->only(['year', 'month', 'block_id'])) }}" class="stat-sub-nav__item {{ request()->routeIs('admin.amenities.statistics') ? 'stat-sub-nav__item--active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
        Thống kê Tiện ích
    </a>
</div>


<style>
    .stat-sub-nav {
        display: flex;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 0px;
        margin-bottom: 24px;
        margin-top: 8px;
    }
    .stat-sub-nav__item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 6px 6px 0 0;
        margin-bottom: -1px;
    }
    .stat-sub-nav__item:hover {
        color: #1e293b;
        background: #f8fafc;
    }
    .stat-sub-nav__item--active {
        color: #3b82f6;
        border-bottom: 2px solid #3b82f6;
        background: rgba(59, 130, 246, 0.04);
    }
    .stat-sub-nav__item svg {
        transition: transform 0.2s ease, stroke 0.2s ease;
    }
    .stat-sub-nav__item--active svg {
        stroke: #3b82f6;
    }
    .stat-sub-nav__item:hover svg {
        transform: translateY(-1px);
    }
</style>
