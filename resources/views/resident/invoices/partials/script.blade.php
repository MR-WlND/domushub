<script>
document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.invoice-checkbox');
        const totalAmountEl = document.getElementById('total-amount');
        const invoiceCountEl = document.getElementById('invoice-count');
        const payBtn = document.getElementById('pay-btn');

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function updateTotal() {
            let total = 0;
            let count = 0;
            let checkedCount = 0;

            checkboxes.forEach(cb => {
                count++;
                const item = cb.closest('.pay-item');
                if (cb.checked) {
                    checkedCount++;
                    total += parseInt(item.dataset.amount);
                    item.classList.remove('unchecked');
                } else {
                    item.classList.add('unchecked');
                }
            });

            totalAmountEl.textContent = formatNumber(total) + ' đ';
            invoiceCountEl.textContent = checkedCount + ' dịch vụ';
            payBtn.disabled = checkedCount === 0;

            // Update "select all" state
            selectAll.checked = checkedCount === count;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < count;
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateTotal();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateTotal);
        });

        // Initialize total calculations on load
        updateTotal();
    });

</script>