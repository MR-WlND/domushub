document.addEventListener('DOMContentLoaded', function() {
    // Image upload preview
    const imgInput = document.getElementById('rv-img1');
    if (imgInput) {
        imgInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const ph = document.getElementById('rv-ph1');
                    const pv = document.getElementById('rv-pv1');
                    if (ph) ph.style.display = 'none';
                    if (pv) {
                        pv.style.display = 'block';
                        const img = pv.querySelector('img');
                        if (img) img.src = e.target.result;
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

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