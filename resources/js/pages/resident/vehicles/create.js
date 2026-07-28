function rvPreview(input, phId, pvId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(phId).style.display = 'none';
            const pv = document.getElementById(pvId);
            pv.style.display = 'block';
            pv.querySelector('img').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.querySelector('select[name="vehicle_type"]');
    if(typeSelect) {
        typeSelect.addEventListener('change', function() {
            const type = this.value;
            const licenseField = document.getElementById('license_plate_input');
            const licenseLabelStar = document.querySelector('#license-plate-field label span');
            
            if (type === 'bicycle' || type === 'electric_bike') {
                licenseField.required = false;
                licenseField.disabled = true;
                licenseField.placeholder = "Không yêu cầu đối với xe đạp/xe điện";
                licenseField.value = "";
                if(licenseLabelStar) licenseLabelStar.style.display = 'none';
            } else {
                licenseField.required = true;
                licenseField.disabled = false;
                licenseField.placeholder = "VD: 30A-123.45";
                if(licenseLabelStar) licenseLabelStar.style.display = 'inline';
            }
        });
        typeSelect.dispatchEvent(new Event('change'));
    }
});