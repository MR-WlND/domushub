function setExpiry(hours) {
    const d = new Date();
    d.setTime(d.getTime() + hours * 3600000);
    const pad = n => String(n).padStart(2, '0');
    const val = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    document.querySelector('input[name="expired_at"]').value = val;
}

function toggleVehicleFields() {
    const checked = document.getElementById('has-vehicle-toggle').checked;
    document.getElementById('vehicle-fields').style.display = checked ? 'block' : 'none';
    if (!checked) {
        document.querySelector('input[name="vehicle_plate"]').value = '';
        document.querySelector('select[name="vehicle_type"]').value = '';
    }
}

document.getElementById('visitor-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="spin-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg> Đang tạo QR...';
});