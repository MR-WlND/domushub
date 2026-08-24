document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const invoiceCheckboxes = document.querySelectorAll('.invoice-checkbox');
    const itemCheckboxes = document.querySelectorAll('.item-check');
    const payBtn = document.getElementById('pay-btn');
    const summaryList = document.getElementById('summary-list');
    const summaryCountText = document.getElementById('summary-count-text');
    const summarySubtotalVal = document.getElementById('summary-subtotal-val');
    const summaryTotalVal = document.getElementById('summary-total-val');

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function updateTotal() {
        let total = 0;
        let checkedCount = 0;
        let invoiceTotals = {};
        let invoiceOverdue = {};

        summaryList.innerHTML = '';

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                checkedCount++;
                const amount = parseInt(cb.dataset.amount);
                const title = cb.dataset.invoiceTitle || 'Hóa đơn';
                
                const parentRow = document.getElementById('details-' + cb.dataset.parent).previousElementSibling;
                const isOverdue = parentRow && parentRow.classList.contains('row-overdue');
                
                if (!invoiceTotals[title]) {
                    invoiceTotals[title] = 0;
                    invoiceOverdue[title] = false;
                }
                
                invoiceTotals[title] += amount;
                if (isOverdue) {
                    invoiceOverdue[title] = true;
                }
                
                total += amount;
            }
        });

        for (const [title, amount] of Object.entries(invoiceTotals)) {
            const itemDiv = document.createElement('div');
            itemDiv.className = `summary-item ${invoiceOverdue[title] ? 'is-overdue' : ''}`;
            itemDiv.innerHTML = `
                <span class="item-name">${title}</span>
                <span class="item-val">${formatNumber(amount)} đ</span>
            `;
            summaryList.appendChild(itemDiv);
        }

        // Cập nhật text Tạm tính và Tổng
        summaryCountText.textContent = `Tạm tính (${checkedCount} khoản phí)`;
        const formattedTotal = formatNumber(total);
        summarySubtotalVal.textContent = `${formattedTotal} đ`;
        summaryTotalVal.innerHTML = `${formattedTotal} <span>đ</span>`;

        payBtn.disabled = checkedCount === 0;

        // Cập nhật trạng thái "Chọn tất cả" và trạng thái hóa đơn cha
        if(selectAll) {
            selectAll.checked = checkedCount === itemCheckboxes.length && itemCheckboxes.length > 0;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
        }

        // Cập nhật class 'unchecked' cho row cha nếu không có detail nào được chọn
        invoiceCheckboxes.forEach(invCb => {
            const invoiceId = invCb.dataset.id;
            const children = document.querySelectorAll(`.child-of-${invoiceId}`);
            let checkedChildren = 0;
            children.forEach(child => { if (child.checked) checkedChildren++; });
            
            const row = invCb.closest('.pay-row');
            if (checkedChildren === 0) {
                if(row) row.classList.add('unchecked');
                invCb.checked = false;
                invCb.indeterminate = false;
            } else if (checkedChildren === children.length) {
                if(row) row.classList.remove('unchecked');
                invCb.checked = true;
                invCb.indeterminate = false;
            } else {
                if(row) row.classList.remove('unchecked');
                invCb.checked = false; // logic: only fully checked if all checked
                invCb.indeterminate = true;
            }
        });
    }

    if(selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateTotal();
        });
    }

    invoiceCheckboxes.forEach(invCb => {
        invCb.addEventListener('change', function(e) {
            const invoiceId = invCb.dataset.id;
            const children = document.querySelectorAll(`.child-of-${invoiceId}`);
            children.forEach(child => {
                child.checked = invCb.checked;
            });
            updateTotal();
        });
    });

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateTotal);
    });

    // Khởi tạo ban đầu
    updateTotal();
});