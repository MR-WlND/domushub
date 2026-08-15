/**
 * Phân ca làm việc — Schedule Management
 */
(function () {
    'use strict';

    const token = document.querySelector('meta[name="csrf-token"]').content;
    let tomSelectInstance = null;
    let customDates = [];

    // ═══════════════════════════════════════════════════════
    // TOAST
    // ═══════════════════════════════════════════════════════
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `toast toast--${type} toast--visible`;
        setTimeout(() => toast.classList.remove('toast--visible'), 3500);
    }

    // ═══════════════════════════════════════════════════════
    // MODAL
    // ═══════════════════════════════════════════════════════
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('modal-overlay--visible');
        document.body.style.overflow = 'hidden';
        const first = modal.querySelector('input:not([type="hidden"]), select, button[type="submit"]');
        if (first) setTimeout(() => first.focus(), 150);
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('modal-overlay--visible');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', function (e) { if (e.target === this) closeModal(this.id); });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') document.querySelectorAll('.modal-overlay--visible').forEach(m => closeModal(m.id));
    });

    // ═══════════════════════════════════════════════════════
    // CUSTOM CONFIRM
    // ═══════════════════════════════════════════════════════
    function showConfirm(message) {
        return new Promise((resolve) => {
            const overlay = document.getElementById('confirmModal');
            document.getElementById('confirmMessage').textContent = message;
            overlay.classList.add('modal-overlay--visible');
            document.body.style.overflow = 'hidden';

            const yesBtn = document.getElementById('confirmYes');
            const noBtn = document.getElementById('confirmNo');
            function cleanup() { overlay.classList.remove('modal-overlay--visible'); document.body.style.overflow = ''; yesBtn.removeEventListener('click', onYes); noBtn.removeEventListener('click', onNo); }
            function onYes() { cleanup(); resolve(true); }
            function onNo() { cleanup(); resolve(false); }
            yesBtn.addEventListener('click', onYes);
            noBtn.addEventListener('click', onNo);
        });
    }

    // ═══════════════════════════════════════════════════════
    // BADGE UPDATE
    // ═══════════════════════════════════════════════════════
    function updateBadge(listEl) {
        const deptGroup = listEl.closest('.dept-group');
        if (!deptGroup) return;
        const badge = deptGroup.querySelector('.dept-badge');
        if (!badge) return;
        const currentCount = listEl.querySelectorAll('.schedule-item').length;
        const required = parseInt(deptGroup.dataset.required) || 0;
        if (currentCount < required) {
            badge.className = 'dept-badge dept-badge--warning';
            badge.textContent = `Thiếu ${required - currentCount}`;
        } else {
            badge.className = 'dept-badge dept-badge--ok';
            badge.textContent = `${currentCount}/${required}`;
        }
    }

    // ═══════════════════════════════════════════════════════
    // JUMP TO WEEK (Date Picker)
    // ═══════════════════════════════════════════════════════
    window.jumpToWeek = function (dateStr) {
        if (!dateStr) return;
        const url = new URL(window.location.href);
        url.searchParams.set('date', dateStr);
        window.location.href = url.toString();
    };

    // ═══════════════════════════════════════════════════════
    // TOM SELECT
    // ═══════════════════════════════════════════════════════
    document.addEventListener('DOMContentLoaded', function () {
        tomSelectInstance = new TomSelect('#inputStaffId', {
            valueField: 'id', labelField: 'text', searchField: 'text',
            placeholder: 'Gõ tên nhân viên...',
            load: function (query, callback) {
                const deptId = document.getElementById('inputDeptId').value;
                const shiftId = document.getElementById('inputShiftId').value;
                const workDate = document.getElementById('inputWorkDate').value;
                if (!deptId) return callback();
                const url = new URL(window.scheduleConfig.staffsUrl, window.location.origin);
                url.searchParams.set('department_id', deptId);
                if (shiftId) url.searchParams.set('shift_id', shiftId);
                if (workDate) url.searchParams.set('work_date', workDate);
                fetch(url).then(r => r.json()).then(j => callback(j)).catch(() => callback());
            },
            render: {
                option: (item, escape) => `<div>${escape(item.text)}</div>`,
                item: (item, escape) => `<div>${escape(item.text)}</div>`
            }
        });

        // Cascading: Block → Floor
        const blockSelect = document.getElementById('inputBlockId');
        if (blockSelect) {
            blockSelect.addEventListener('change', function () {
                const floorSelect = document.getElementById('inputFloorId');
                floorSelect.innerHTML = '<option value="">Đang tải...</option>';
                if (!this.value) {
                    floorSelect.innerHTML = '<option value="">Chọn tầng</option>';
                    return;
                }
                const url = new URL(window.scheduleConfig.floorsUrl, window.location.origin);
                url.searchParams.set('block_id', this.value);
                fetch(url)
                    .then(r => r.json())
                    .then(floors => {
                        floorSelect.innerHTML = '<option value="">Chọn tầng</option>';
                        floors.forEach(f => {
                            const opt = document.createElement('option');
                            opt.value = f.id;
                            opt.textContent = f.name || ('Tầng ' + f.floor_number);
                            floorSelect.appendChild(opt);
                        });
                    })
                    .catch(() => {
                        floorSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                    });
            });
        }
    });

    // ═══════════════════════════════════════════════════════
    // ADD MODAL
    // ═══════════════════════════════════════════════════════
    window.openAddModal = function (dateStr, shiftId, shiftName, dateFormatted, deptId, deptName) {
        document.getElementById('inputWorkDate').value = dateStr;
        document.getElementById('inputShiftId').value = shiftId;
        document.getElementById('inputDeptId').value = deptId;
        document.getElementById('modalSubtitle').textContent = `${shiftName} — ${dateFormatted} | ${deptName}`;
        document.getElementById('modalError').style.display = 'none';
        tomSelectInstance.clear(); tomSelectInstance.clearOptions(); tomSelectInstance.load('');

        // Show/hide location fields for cleaning department
        const locationGroup = document.getElementById('locationGroup');
        if (window.scheduleConfig.cleaningDeptId && parseInt(deptId) === window.scheduleConfig.cleaningDeptId) {
            locationGroup.style.display = 'block';
            document.getElementById('inputBlockId').value = '';
            document.getElementById('inputFloorId').innerHTML = '<option value="">Chọn tầng</option>';
        } else {
            locationGroup.style.display = 'none';
            document.getElementById('inputBlockId').value = '';
            document.getElementById('inputFloorId').innerHTML = '<option value="">Chọn tầng</option>';
        }

        openModal('addModal');
    };
    window.closeAddModal = () => closeModal('addModal');

    window.submitAddForm = async function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitAdd');
        const err = document.getElementById('modalError');
        btn.disabled = true; btn.textContent = 'Đang lưu...'; err.style.display = 'none';
        const workDate = document.getElementById('inputWorkDate').value;
        const shiftId = document.getElementById('inputShiftId').value;
        const deptId = document.getElementById('inputDeptId').value;
        const blockId = document.getElementById('inputBlockId').value || null;
        const floorId = document.getElementById('inputFloorId').value || null;
        try {
            const res = await fetch(window.scheduleConfig.storeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ work_date: workDate, shift_id: shiftId, staff_id: document.getElementById('inputStaffId').value, block_id: blockId, floor_id: floorId })
            });
            const json = await res.json();
            if (json.success) {
                closeModal('addModal');
                showToast(json.message || 'Gán ca thành công!');
                const listEl = document.getElementById(`list-${workDate}-${shiftId}-${deptId}`);
                if (listEl && json.assignment) {
                    const a = json.assignment;
                    const div = document.createElement('div');
                    div.className = 'schedule-item'; div.id = `schedule-item-${a.id}`;
                    let locationHtml = '';
                    if (a.block_name) {
                        locationHtml = `<span class="schedule-item__location" title="${a.block_name}${a.floor_name ? ' - ' + a.floor_name : ''}"><i class="fa-solid fa-location-dot"></i> ${a.block_name}${a.floor_name ? ' - ' + a.floor_name : ''}</span>`;
                    }
                    div.innerHTML = `<span class="schedule-item__name" title="${a.staff_name}">${a.staff_name.length > 18 ? a.staff_name.substring(0, 18) + '...' : a.staff_name}</span>${locationHtml}<div class="schedule-actions"><button type="button" class="action-btn action-btn--star" onclick="toggleLeader(${a.id}, this)" title="Trưởng ca"><i class="fa-solid fa-star"></i></button><button type="button" class="action-btn action-btn--del" onclick="deleteSchedule(${a.id}, this)" title="Xóa"><i class="fa-solid fa-xmark"></i></button></div>`;
                    listEl.appendChild(div);
                    updateBadge(listEl);
                }
            } else { err.textContent = json.message || 'Có lỗi.'; err.style.display = 'block'; }
        } catch (error) { err.textContent = 'Lỗi kết nối.'; err.style.display = 'block'; }
        finally { btn.disabled = false; btn.textContent = 'Gán ca'; }
    };

    // ═══════════════════════════════════════════════════════
    // DELETE
    // ═══════════════════════════════════════════════════════
    window.deleteSchedule = async function (id, btnEl) {
        if (!await showConfirm('Gỡ nhân sự khỏi ca này?')) return;
        const item = btnEl.closest('.schedule-item');
        const listEl = item.closest('.schedule-list');
        item.style.opacity = '0.4'; item.style.pointerEvents = 'none';
        try {
            const res = await fetch(`${window.scheduleConfig.baseUrl}/${id}`, { method: 'DELETE', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token } });
            const json = await res.json();
            if (json.success) {
                item.style.transition = 'all 0.25s'; item.style.maxHeight = '0'; item.style.opacity = '0'; item.style.overflow = 'hidden';
                setTimeout(() => { item.remove(); updateBadge(listEl); showToast('Đã gỡ nhân sự!'); }, 250);
            } else { item.style.opacity = '1'; item.style.pointerEvents = ''; showToast(json.message || 'Lỗi', 'error'); }
        } catch (e) { item.style.opacity = '1'; item.style.pointerEvents = ''; showToast('Lỗi kết nối', 'error'); }
    };

    // ═══════════════════════════════════════════════════════
    // TOGGLE LEADER
    // ═══════════════════════════════════════════════════════
    window.toggleLeader = async function (id, btnEl) {
        const item = btnEl.closest('.schedule-item');
        try {
            const res = await fetch(`${window.scheduleConfig.baseUrl}/${id}/leader`, { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token } });
            const json = await res.json();
            if (json.success) {
                item.classList.toggle('schedule-item--leader');
                const nameSpan = item.querySelector('.schedule-item__name');
                const tag = nameSpan.querySelector('.schedule-item__leader-tag');
                if (json.is_leader) { if (!tag) { const t = document.createElement('span'); t.className = 'schedule-item__leader-tag'; t.textContent = 'TC'; nameSpan.insertBefore(t, nameSpan.firstChild); } showToast('Đã bổ nhiệm trưởng ca!'); }
                else { if (tag) tag.remove(); showToast('Đã hủy trưởng ca!'); }
            } else { showToast(json.message || 'Lỗi', 'error'); }
        } catch (e) { showToast('Lỗi kết nối', 'error'); }
    };

    // ═══════════════════════════════════════════════════════
    // COPY MODAL
    // ═══════════════════════════════════════════════════════
    window.openCopyModal = function () {
        document.getElementById('copyError').style.display = 'none';
        document.getElementById('copySuccess').style.display = 'none';
        document.getElementById('copyPreview').innerHTML = '<span class="copy-preview__empty">Chọn ngày để xem số lượng phân công</span>';
        customDates = [];
        renderCustomDates();
        openModal('copyModal');
    };
    window.closeCopyModal = () => closeModal('copyModal');

    window.loadPreview = async function () {
        const fromDate = document.getElementById('copyFrom').value;
        const el = document.getElementById('copyPreview');
        if (!fromDate) { el.innerHTML = '<span class="copy-preview__empty">Chọn ngày để xem số lượng phân công</span>'; return; }
        el.innerHTML = '<span class="copy-preview__loading">Đang tải...</span>';
        try {
            const res = await fetch(window.scheduleConfig.previewCopyUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ from_date: fromDate }) });
            const json = await res.json();
            if (json.total === 0) { el.innerHTML = '<span class="copy-preview__empty">Ngày này chưa có lịch phân ca.</span>'; }
            else {
                let html = `<div class="copy-preview__summary"><strong>${json.total}</strong> phân công sẽ được sao chép:</div><div class="copy-preview__breakdown">`;
                json.breakdown.forEach(i => { html += `<span class="copy-preview__tag">${i.shift_name}: ${i.count} người</span>`; });
                el.innerHTML = html + '</div>';
            }
        } catch (e) { el.innerHTML = '<span class="copy-preview__empty">Lỗi tải dữ liệu</span>'; }
    };

    window.toggleTargetType = function () {
        const type = document.querySelector('input[name="target_type"]:checked').value;
        document.getElementById('targetSingle').classList.toggle('copy-hidden', type !== 'single');
        document.getElementById('targetWeek').classList.toggle('copy-hidden', type !== 'week');
        document.getElementById('targetCustom').classList.toggle('copy-hidden', type !== 'custom');
    };

    window.updateWeekPreview = function () {
        const val = document.getElementById('copyToWeekStart').value;
        const el = document.getElementById('weekPreview');
        if (!val) { el.innerHTML = ''; return; }
        const days = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
        const start = new Date(val);
        const dow = start.getDay(); const diff = dow === 0 ? -6 : 1 - dow;
        start.setDate(start.getDate() + diff);
        let html = '<div class="copy-week-tags">';
        for (let i = 0; i < 7; i++) { const d = new Date(start); d.setDate(d.getDate() + i); html += `<span class="copy-week-tag">${days[i]} ${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}</span>`; }
        el.innerHTML = html + '</div>';
    };

    window.addCustomDate = function () {
        const input = document.getElementById('copyToCustomInput');
        const val = input.value;
        if (!val || customDates.includes(val)) { input.value = ''; return; }
        customDates.push(val); customDates.sort(); input.value = '';
        renderCustomDates();
    };
    window.removeCustomDate = function (d) { customDates = customDates.filter(x => x !== d); renderCustomDates(); };

    function renderCustomDates() {
        const c = document.getElementById('customDateTags');
        if (!c) return;
        if (customDates.length === 0) { c.innerHTML = ''; return; }
        c.innerHTML = customDates.map(d => { const p = d.split('-'); return `<span class="copy-date-tag">${p[2]}/${p[1]} <button type="button" onclick="removeCustomDate('${d}')">&times;</button></span>`; }).join('');
    }

    function getTargetDates() {
        const type = document.querySelector('input[name="target_type"]:checked').value;
        if (type === 'single') { const v = document.getElementById('copyToSingle').value; return v ? [v] : []; }
        if (type === 'week') {
            const v = document.getElementById('copyToWeekStart').value; if (!v) return [];
            const start = new Date(v); const dow = start.getDay(); const diff = dow === 0 ? -6 : 1 - dow; start.setDate(start.getDate() + diff);
            const dates = []; for (let i = 0; i < 7; i++) { const d = new Date(start); d.setDate(d.getDate() + i); dates.push(d.toISOString().split('T')[0]); }
            const from = document.getElementById('copyFrom').value;
            return dates.filter(d => d !== from);
        }
        if (type === 'custom') return [...customDates];
        return [];
    }

    window.submitCopyForm = async function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitCopy');
        const err = document.getElementById('copyError');
        const suc = document.getElementById('copySuccess');
        err.style.display = 'none'; suc.style.display = 'none';
        const fromDate = document.getElementById('copyFrom').value;
        const toDates = getTargetDates();
        const mode = document.querySelector('input[name="copy_mode"]:checked').value;
        if (!fromDate) { err.textContent = 'Vui lòng chọn ngày nguồn.'; err.style.display = 'block'; return; }
        if (toDates.length === 0) { err.textContent = 'Vui lòng chọn ít nhất một ngày đích.'; err.style.display = 'block'; return; }
        btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang sao chép...';
        try {
            const res = await fetch(window.scheduleConfig.copyUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ from_date: fromDate, to_dates: toDates, mode }) });
            const json = await res.json();
            if (json.success) { suc.textContent = json.message; suc.style.display = 'block'; setTimeout(() => window.location.reload(), 1500); }
            else { err.textContent = json.message || 'Có lỗi.'; err.style.display = 'block'; }
        } catch (error) { err.textContent = 'Lỗi kết nối.'; err.style.display = 'block'; }
        finally { btn.disabled = false; btn.innerHTML = '<i class="fa-regular fa-copy"></i> Sao chép'; }
    };

    // ═══════════════════════════════════════════════════════
    // REQUIREMENT MODAL
    // ═══════════════════════════════════════════════════════
    window.openReqModal = function (shiftId, shiftName, reqs) {
        document.getElementById('reqShiftId').value = shiftId;
        document.getElementById('reqModalSubtitle').textContent = `Số lượng nhân sự cần thiết cho ${shiftName}`;
        document.getElementById('reqError').style.display = 'none';
        document.querySelectorAll('.req-dept-input').forEach(el => el.value = 0);
        for (const [deptId, count] of Object.entries(reqs)) { const input = document.getElementById(`req-dept-${deptId}`); if (input) input.value = count; }
        openModal('reqModal');
    };
    window.closeReqModal = () => closeModal('reqModal');

    window.submitReqForm = async function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitReq');
        const err = document.getElementById('reqError');
        const shiftId = document.getElementById('reqShiftId').value;
        btn.disabled = true; btn.textContent = 'Đang lưu...'; err.style.display = 'none';
        const requirements = {};
        document.querySelectorAll('.req-dept-input').forEach(el => { requirements[el.dataset.deptId] = el.value; });
        try {
            const res = await fetch(`${window.scheduleConfig.schedulesUrl}/${shiftId}/requirements`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ requirements }) });
            const json = await res.json();
            if (json.success) { closeModal('reqModal'); showToast('Đã cập nhật định mức!'); setTimeout(() => window.location.reload(), 600); }
            else { err.textContent = json.message || 'Có lỗi.'; err.style.display = 'block'; }
        } catch (error) { err.textContent = 'Lỗi kết nối.'; err.style.display = 'block'; }
        finally { btn.disabled = false; btn.textContent = 'Lưu định mức'; }
    };
})();
