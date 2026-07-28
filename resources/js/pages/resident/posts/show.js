// === Share - Copy link ===
    document.querySelectorAll('.rh-fb-share-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            navigator.clipboard.writeText(url).then(() => {
                const orig = this.innerHTML;
                this.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Đã sao chép';
                setTimeout(() => { this.innerHTML = orig; }, 2000);
            });
        });
    });

    // === Lightbox ===
    function openLightbox(src) { const lb = document.getElementById('pcLightbox'); lb.style.display = "flex"; document.getElementById('pcLightboxTarget').src = src; }
    function closeLightbox() { document.getElementById('pcLightbox').style.display = "none"; }

    // === Reply ===
    function replyToComment(commentId, authorName) {
        document.getElementById('comment-parent-id').value = commentId;
        document.getElementById('reply-target-name').innerText = authorName;
        const indicator = document.getElementById('reply-indicator');
        indicator.style.display = 'flex';
        const textarea = document.getElementById('comment-content');
        textarea.placeholder = `Trả lời ${authorName}...`;
        textarea.focus();
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    function cancelReply() {
        document.getElementById('comment-parent-id').value = '';
        document.getElementById('reply-indicator').style.display = 'none';
        document.getElementById('comment-content').placeholder = 'Viết bình luận...';
    }

    // === Image Preview ===
    function previewCommentImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { document.getElementById('comment-image-preview').src = e.target.result; document.getElementById('comment-image-preview-wrap').style.display = 'block'; };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function removeCommentImage() {
        const input = document.getElementById('comment-image-input');
        if (input) input.value = '';
        document.getElementById('comment-image-preview').src = '';
        document.getElementById('comment-image-preview-wrap').style.display = 'none';
    }

// === Toggle Replies ===
    function toggleReplies(parentId) {
        const container = document.getElementById(`replies-container-${parentId}`);
        const btn = document.getElementById(`view-replies-btn-${parentId}`);
        if (!container || !btn) return;
        if (!container.classList.contains('fb-comment-replies--visible')) {
            container.classList.add('fb-comment-replies--visible');
            const count = btn.querySelector('.reply-count').innerText;
            btn.innerHTML = `<i class="fa-solid fa-angle-up"></i> Ẩn phản hồi`;
        } else {
            container.classList.remove('fb-comment-replies--visible');
            const count = container.querySelectorAll('.fb-comment').length;
            btn.innerHTML = `<i class="fa-solid fa-share-nodes"></i> <span class="reply-count">${count}</span> phản hồi`;
        }
    }

    // === Toggle Like / Reaction ===
    function toggleLike(event, id, type, reactionType = null) {
        event.preventDefault();
        event.stopPropagation();

        // If clicking on like-count, open reactions list modal
        if (event.target.classList.contains('like-count')) {
            openReactionsListModal(id, type);
            return;
        }

        const reqReactionType = reactionType || 'like';
        fetch('/resident/like', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ likeable_id: id, likeable_type: type, type: reqReactionType })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            if (type === 'post') {
                const btn = document.querySelector('.pc-like-btn');
                if (!btn) return;
                const countEl = document.querySelector('.rh-fb-card__stats span:first-child .like-count') || document.querySelector('.rh-fb-card__stats span:first-child');
                const textSpan = btn.querySelector('.reaction-text-span');
                // Update stats row
                const statsFirstSpan = document.querySelector('.rh-fb-card__stats span:first-child');
                if (statsFirstSpan) statsFirstSpan.innerHTML = `<span class="like-count">${data.likes_count}</span> lượt thích`;
                // Update button
                btn.className = 'rh-fb-card__action pc-like-btn';
                if (data.liked && data.reaction_type) {
                    btn.classList.add('rh-fb-like-btn--active');
                    if (textSpan) textSpan.innerText = getReactionText(data.reaction_type);
                } else {
                    if (textSpan) textSpan.innerText = 'Thích';
                }
            } else {
                // Comment like
                const commentEl = document.getElementById(`comment-${id}`);
                if (!commentEl) return;
                const likeWrapper = commentEl.querySelector('.fb-like-wrapper');
                const btn = likeWrapper.querySelector('.fb-comment__action-link--like');
                const textSpan = btn.querySelector('.reaction-text-span');
                const likeCountSpan = commentEl.querySelector('.fb-comment__like-count');

                btn.className = 'fb-comment__action-link fb-comment__action-link--like';
                if (data.liked && data.reaction_type) {
                    btn.classList.add('fb-comment__action-link--liked', 'reaction-active-' + data.reaction_type);
                    textSpan.innerText = getReactionText(data.reaction_type);
                } else {
                    textSpan.innerText = 'Thích';
                }
                if (likeCountSpan) {
                    likeCountSpan.querySelector('.like-count').innerText = data.likes_count;
                    likeCountSpan.style.display = data.likes_count > 0 ? 'inline-flex' : 'none';
                }
            }
        })
        .catch(err => console.error('Error toggling reaction:', err));
    }

// === Edit Comment Inline ===
    function startEditComment(id) {
        const textEl = document.getElementById('comment-text-' + id);
        const editWrap = document.getElementById('comment-edit-wrap-' + id);
        const editInput = document.getElementById('comment-edit-input-' + id);
        const contentWrap = document.getElementById('comment-content-wrap-' + id);
        let plainText = textEl.innerHTML.replace(/<br\s*\/?>/gi, '\n');
        const txt = document.createElement("textarea");
        txt.innerHTML = plainText;
        plainText = txt.value;
        editInput.value = plainText;
        contentWrap.style.display = 'none';
        editWrap.style.display = 'block';
        editInput.focus();
    }
    function cancelEditComment(id) {
        document.getElementById('comment-edit-wrap-' + id).style.display = 'none';
        document.getElementById('comment-content-wrap-' + id).style.display = 'block';
    }
    function saveEditComment(id) {
        const editInput = document.getElementById('comment-edit-input-' + id);
        const content = editInput.value.trim();
        if (!content) return;
        const saveBtn = document.querySelector(`#comment-edit-wrap-${id} button:last-child`);
        saveBtn.setAttribute('disabled', 'disabled');
        saveBtn.innerText = 'Đang lưu...';
        fetch(`/resident/comments/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ content: content, _method: 'PUT' })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('comment-text-' + id).innerHTML = nl2br(data.comment.content);
                cancelEditComment(id);
                showToast('Đã cập nhật bình luận!');
            }
        })
        .catch(() => showToast('Lỗi khi lưu bình luận.', 'error'))
        .finally(() => { saveBtn.removeAttribute('disabled'); saveBtn.innerText = 'Lưu'; });
    }

    // === Pin Comment ===
    function togglePinComment(commentId) {
        const pinBtn = document.getElementById('pin-btn-' + commentId);
        if (pinBtn) pinBtn.setAttribute('disabled', 'disabled');
        fetch(`/resident/comments/${commentId}/pin`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                const card = document.getElementById('comment-' + commentId);
                if (data.is_pinned) {
                    document.querySelectorAll('.fb-comment--pinned').forEach(el => {
                        el.classList.remove('fb-comment--pinned');
                        const b = el.querySelector('.fb-comment__pinned-badge'); if (b) b.style.display = 'none';
                        const p = el.querySelector('[id^="pin-btn-"]'); if (p) p.innerText = 'Ghim';
                    });
                    card.classList.add('fb-comment--pinned');
                    const badge = document.getElementById('pinned-badge-' + commentId); if (badge) badge.style.display = 'inline-flex';
                    if (pinBtn) pinBtn.innerText = 'Bỏ ghim';
                } else {
                    card.classList.remove('fb-comment--pinned');
                    const badge = document.getElementById('pinned-badge-' + commentId); if (badge) badge.style.display = 'none';
                    if (pinBtn) pinBtn.innerText = 'Ghim';
                }
            }
        })
        .catch(() => showToast('Có lỗi xảy ra.', 'error'))
        .finally(() => { if (pinBtn) pinBtn.removeAttribute('disabled'); });
    }

// === Report Post Modal ===
    function openReportModal(postId) {
        document.getElementById('report-target-post-id').value = postId;
        document.getElementById('report-post-form').reset();
        toggleCustomReason(false);
        document.getElementById('submit-report-btn').setAttribute('disabled', 'disabled');
        const modal = document.getElementById('reportPostModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }
    function closeReportModal() { const m = document.getElementById('reportPostModal'); m.classList.remove('show'); setTimeout(() => m.style.display = 'none', 300); }
    function handleOutsideModalClick(e) { if (e.target === document.getElementById('reportPostModal')) closeReportModal(); }
    function toggleCustomReason(show) {
        const c = document.getElementById('custom-reason-container');
        const t = document.getElementById('report-reason-custom');
        c.style.display = show ? 'block' : 'none';
        show ? (t.setAttribute('required', 'required'), t.focus()) : (t.removeAttribute('required'), t.value = '');
        checkReportFormStatus();
    }
    function checkReportFormStatus() {
        const btn = document.getElementById('submit-report-btn');
        const checked = document.querySelector('input[name="reason_preset"]:checked');
        if (!checked) { btn.setAttribute('disabled', 'disabled'); return; }
        if (checked.value === 'other') { btn[document.getElementById('report-reason-custom').value.trim() ? 'removeAttribute' : 'setAttribute']('disabled', 'disabled'); }
        else { btn.removeAttribute('disabled'); }
    }

    // === Report Comment Modal ===
    function openCommentReportModal(commentId) {
        document.getElementById('report-target-comment-id').value = commentId;
        document.getElementById('report-comment-form').reset();
        toggleCommentCustomReason(false);
        document.getElementById('submit-comment-report-btn').setAttribute('disabled', 'disabled');
        const modal = document.getElementById('reportCommentModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }
    function closeCommentReportModal() { const m = document.getElementById('reportCommentModal'); m.classList.remove('show'); setTimeout(() => m.style.display = 'none', 300); }
    function handleCommentOutsideModalClick(e) { if (e.target === document.getElementById('reportCommentModal')) closeCommentReportModal(); }
    function toggleCommentCustomReason(show) {
        const c = document.getElementById('comment-custom-reason-container');
        const t = document.getElementById('report-comment-reason-custom');
        c.style.display = show ? 'block' : 'none';
        show ? (t.setAttribute('required', 'required'), t.focus()) : (t.removeAttribute('required'), t.value = '');
        checkCommentReportFormStatus();
    }
    function checkCommentReportFormStatus() {
        const btn = document.getElementById('submit-comment-report-btn');
        const checked = document.querySelector('input[name="comment_reason_preset"]:checked');
        if (!checked) { btn.setAttribute('disabled', 'disabled'); return; }
        if (checked.value === 'other') { btn[document.getElementById('report-comment-reason-custom').value.trim() ? 'removeAttribute' : 'setAttribute']('disabled', 'disabled'); }
        else { btn.removeAttribute('disabled'); }
    }

// === Create Comment HTML (for AJAX/WebSocket) ===
    function createCommentHtml(comment) {
        const avatarUrl = comment.user.avatar ? `/storage/${comment.user.avatar}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=00236f&color=fff`;
        const roleText = comment.user.apartment ? 'Cư dân' : 'BQT';
        const isChild = comment.parent_id !== null;

        let imageHtml = '';
        if (comment.image_path && comment.content !== '[Bình luận này đã bị xóa bởi Ban quản trị do vi phạm quy chuẩn cộng đồng]' && comment.content !== '[Bình luận này đã bị ẩn tạm thời do nhận nhiều báo cáo vi phạm]') {
            const imgUrl = comment.image_path.startsWith('http') ? comment.image_path : `/storage/${comment.image_path}`;
            imageHtml = `<div class="fb-comment__image" onclick="openLightbox('${imgUrl}')" style="max-width:${isChild?'160':'200'}px;"><img src="${imgUrl}" style="max-height:${isChild?'120':'150'}px;"></div>`;
        }

        let editBtn = '', editForm = '';
        if (comment.user.id === currentUserId) {
            editBtn = `<button type="button" class="fb-comment__action-link" onclick="startEditComment(${comment.id})">Sửa</button>`;
            editForm = `<div class="fb-comment-edit-wrap" id="comment-edit-wrap-${comment.id}"><textarea id="comment-edit-input-${comment.id}" rows="2"></textarea><div class="fb-comment-edit-actions"><button type="button" class="pc-btn pc-btn--secondary" style="padding:0.3rem 0.65rem;font-size:0.75rem;border-radius:12px;" onclick="cancelEditComment(${comment.id})">Hủy</button><button type="button" class="pc-btn" style="padding:0.3rem 0.65rem;font-size:0.75rem;border-radius:12px;" onclick="saveEditComment(${comment.id})">Lưu</button></div></div>`;
        }

        let deleteBtn = '';
        if (comment.user.id === currentUserId || isPostOwner || isAdmin) {
            deleteBtn = `<form action="/resident/comments/${comment.id}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa bình luận này?')"><input type="hidden" name="_token" value="${document.querySelector('input[name=_token]').value}"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="fb-comment__action-link fb-comment__action-link--delete">Xóa</button></form>`;
        }

        let reportBtn = '';
        if (comment.user.id !== currentUserId) {
            reportBtn = `<button type="button" class="fb-comment__action-link fb-comment__action-link--delete" onclick="openCommentReportModal(${comment.id})"><i class="fa-regular fa-flag"></i></button>`;
        }

        let pinBtn = '';
        if (!isChild && (isPostOwner || isAdmin)) {
            pinBtn = `<button type="button" class="fb-comment__action-link fb-comment__action-link--pin" id="pin-btn-${comment.id}" onclick="togglePinComment(${comment.id})">${comment.is_pinned ? 'Bỏ ghim' : 'Ghim'}</button>`;
        }

        const pinnedBadge = `<span class="fb-comment__pinned-badge" id="pinned-badge-${comment.id}" style="display:${comment.is_pinned?'inline-flex':'none'};"><i class="fa-solid fa-thumbtack"></i> Ghim</span>`;
        const replyToId = isChild ? comment.parent_id : comment.id;
        const likesCount = comment.likes_count || 0;
        const likeCountHtml = `<span class="fb-comment__like-count" style="display:${likesCount>0?'inline-flex':'none'};"><span class="like-count">${likesCount}</span> 👍</span>`;

        const reactionsPopup = `<div class="fb-reactions-popup"><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','like')">👍</span><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','love')">❤️</span><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','haha')">😆</span><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','wow')">😮</span><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','sad')">😢</span><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','angry')">😡</span></div>`;

        const html = `
            <div class="fb-comment fb-comment--new" id="comment-${comment.id}">
                <img src="${avatarUrl}" alt="Avatar" class="fb-comment__avatar ${isChild?'fb-comment__avatar--reply':''}">
                <div class="fb-comment__main">
                    <div class="fb-comment__bubble">
                        <span class="fb-comment__name">${escapeHtml(comment.user.name)} <span class="fb-comment__role">${roleText}</span>${!isChild?pinnedBadge:''}</span>
                        <div class="comment-content-wrap" id="comment-content-wrap-${comment.id}">
                            <p class="fb-comment__text" id="comment-text-${comment.id}">${nl2br(comment.content)}</p>
                        </div>
                    </div>
                    ${imageHtml}${editForm}
                    <div class="fb-comment__meta">
                        <span class="fb-comment__time">Vừa xong</span>
                        <div class="fb-like-wrapper">${reactionsPopup}<button type="button" class="fb-comment__action-link fb-comment__action-link--like" onclick="toggleLike(event,${comment.id},'comment')"><span class="reaction-text-span">Thích</span></button></div>
                        ${likeCountHtml}
                        <button type="button" class="fb-comment__action-link" onclick="replyToComment(${replyToId},'${escapeHtml(comment.user.name)}')">Trả lời</button>
                        ${pinBtn}${editBtn}${deleteBtn}${reportBtn}
                    </div>
                </div>
            </div>`;

        if (isChild) return html;

        return `<div class="fb-comment-thread" id="comment-thread-${comment.id}">${html}<div class="fb-comment-replies" id="replies-container-${comment.id}"></div></div>`;
    }

// === Handle New Comment (real-time or AJAX) ===
    function handleNewComment(comment) {
        const emptyPlaceholder = document.getElementById('empty-comments-placeholder');
        if (emptyPlaceholder) emptyPlaceholder.remove();
        if (document.getElementById(`comment-${comment.id}`)) return;

        const container = document.querySelector('.fb-comments-list');
        if (!container) return;

        if (comment.parent_id !== null) {
            const repliesContainer = document.getElementById(`replies-container-${comment.parent_id}`);
            if (repliesContainer) {
                repliesContainer.insertAdjacentHTML('beforeend', createCommentHtml(comment));
                repliesContainer.classList.add('fb-comment-replies--visible');
                // Update or create toggle button
                let btn = document.getElementById(`view-replies-btn-${comment.parent_id}`);
                if (btn) {
                    const count = repliesContainer.querySelectorAll('.fb-comment').length;
                    btn.innerHTML = `<i class="fa-solid fa-angle-up"></i> Ẩn phản hồi`;
                } else {
                    const thread = document.getElementById(`comment-thread-${comment.parent_id}`);
                    if (thread) {
                        const count = repliesContainer.querySelectorAll('.fb-comment').length;
                        const newBtn = document.createElement('button');
                        newBtn.type = 'button';
                        newBtn.className = 'fb-view-replies-btn';
                        newBtn.id = `view-replies-btn-${comment.parent_id}`;
                        newBtn.onclick = () => toggleReplies(comment.parent_id);
                        newBtn.innerHTML = `<i class="fa-solid fa-angle-up"></i> Ẩn phản hồi`;
                        thread.insertBefore(newBtn, repliesContainer);
                    }
                }
            }
        } else {
            container.insertAdjacentHTML('beforeend', createCommentHtml(comment));
        }

        const countEl = document.getElementById('total-comments-count');
        if (countEl) {
            const count = container.querySelectorAll('.fb-comment-thread').length;
            countEl.innerText = count;
        }
    }

    // === Load Older Comments ===
    function loadOlderComments() {
        const btn = document.getElementById('load-older-btn');
        if (!btn) return;
        const offset = parseInt(btn.getAttribute('data-offset'), 10);
        const total = parseInt(btn.getAttribute('data-total'), 10);
        btn.setAttribute('disabled', 'disabled');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tải...';

        fetch(`/resident/posts/${currentPostId}/comments?offset=${offset}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.comments && data.comments.length > 0) {
                const container = document.querySelector('.fb-comments-list');
                const loadContainer = document.getElementById('load-older-container');
                const oldHeight = container.scrollHeight;
                for (let i = data.comments.length - 1; i >= 0; i--) {
                    const c = data.comments[i];
                    if (document.getElementById(`comment-${c.id}`)) continue;
                    const html = createCommentHtml(c);
                    if (loadContainer) loadContainer.insertAdjacentHTML('afterend', html);
                    else container.insertAdjacentHTML('afterbegin', html);
                }
                window.scrollBy(0, container.scrollHeight - oldHeight);
                const newOffset = offset + data.comments.length;
                btn.setAttribute('data-offset', newOffset);
                const remaining = total - newOffset;
                if (remaining <= 0) document.getElementById('load-older-container').remove();
                else { btn.removeAttribute('disabled'); btn.innerHTML = `<i class="fa-solid fa-clock-rotate-left"></i> Xem thêm bình luận cũ hơn (<span id="older-count">${remaining}</span>)`; }
            } else {
                const lc = document.getElementById('load-older-container'); if (lc) lc.remove();
            }
        })
        .catch(() => { btn.removeAttribute('disabled'); btn.innerHTML = `<i class="fa-solid fa-clock-rotate-left"></i> Xem thêm`; showToast('Lỗi tải bình luận.', 'error'); });
    }

// === Reactions List Modal ===
    let currentReactions = [];
    function openReactionsListModal(likeableId, likeableType) {
        const modal = document.getElementById('reactionsListModal');
        const container = document.getElementById('reactions-list-container');
        container.innerHTML = '<div style="text-align:center;color:var(--color-outline);padding:1rem;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>';
        document.querySelectorAll('.react-tab').forEach(t => { t.classList.remove('react-tab--active'); t.style.borderBottom = 'none'; t.style.color = 'var(--color-text-secondary)'; t.style.display = 'none'; });
        const allTab = document.querySelector('.react-tab[data-type="all"]');
        allTab.classList.add('react-tab--active'); allTab.style.borderBottom = '3px solid var(--color-primary)'; allTab.style.color = 'var(--color-primary)'; allTab.style.display = 'inline-block';
        modal.style.display = 'flex';
        fetch(`/resident/reactions/${likeableType}/${likeableId}`).then(r => r.json()).then(data => {
            if (data.success) { currentReactions = data.reactions; populateReactionsList('all'); updateReactionsTabsSummary(); }
            else container.innerHTML = '<div style="text-align:center;color:var(--color-error);padding:1rem;">Không thể tải.</div>';
        }).catch(() => container.innerHTML = '<div style="text-align:center;color:var(--color-error);padding:1rem;">Lỗi tải dữ liệu.</div>');
    }
    function populateReactionsList(type) {
        const container = document.getElementById('reactions-list-container');
        container.innerHTML = '';
        const filtered = type === 'all' ? currentReactions : currentReactions.filter(r => r.type === type);
        if (!filtered.length) { container.innerHTML = '<div style="text-align:center;color:var(--color-outline);padding:1.5rem 0;font-size:0.9rem;">Chưa có cảm xúc nào.</div>'; return; }
        filtered.forEach(r => {
            const emoji = getReactionEmoji(r.type);
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:0.75rem;padding-bottom:0.75rem;border-bottom:1px solid #f1f5f9;';
            row.innerHTML = `<div style="display:flex;align-items:center;gap:0.75rem;"><img src="${r.avatar}" style="width:38px;height:38px;border-radius:50%;object-fit:cover;"><div><span style="font-weight:700;font-size:0.9rem;">${escapeHtml(r.name)}</span><br><span style="font-size:0.75rem;color:var(--color-outline);">${r.apartment?'Cư dân':'BQT'}</span></div></div><span style="font-size:1.25rem;">${emoji}</span>`;
            container.appendChild(row);
        });
    }
    function updateReactionsTabsSummary() {
        const counts = {};
        currentReactions.forEach(r => { counts[r.type] = (counts[r.type] || 0) + 1; });
        Object.keys(counts).forEach(type => {
            const tab = document.querySelector(`.react-tab[data-type="${type}"]`);
            if (tab) { tab.style.display = 'inline-block'; const el = tab.querySelector('.react-count-tab'); if (el) el.innerText = counts[type]; }
        });
    }
    function filterReactions(type) {
        document.querySelectorAll('.react-tab').forEach(t => { t.classList.remove('react-tab--active'); t.style.borderBottom = 'none'; t.style.color = 'var(--color-text-secondary)'; });
        const tab = document.querySelector(`.react-tab[data-type="${type}"]`);
        if (tab) { tab.classList.add('react-tab--active'); tab.style.borderBottom = '3px solid var(--color-primary)'; tab.style.color = 'var(--color-primary)'; }
        populateReactionsList(type);
    }
    function closeReactionsModal() { document.getElementById('reactionsListModal').style.display = 'none'; }
    function handleReactionsOutsideClick(e) { if (e.target === document.getElementById('reactionsListModal')) closeReactionsModal(); }