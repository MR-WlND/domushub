<!-- Chatbot AI Floating Widget -->
<div id="domushub-chatbot" class="chatbot-widget">
    <!-- Chat Bubble (Nút mở chat) -->
    <button id="chatbot-bubble" class="chatbot-bubble" aria-label="Trò chuyện với trợ lý ảo">
        <i class="fas fa-comments"></i>
        <span class="chatbot-badge">AI</span>
    </button>

    <!-- Chat Window (Khung chat) -->
    <div id="chatbot-window" class="chatbot-window hidden">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar">
                    <i class="fas fa-robot"></i>
                    <span class="status-dot online"></span>
                </div>
                <div>
                    <h4 class="chatbot-title">Trợ lý ảo DomusHub</h4>
                    <p class="chatbot-status">Sẵn sàng hỗ trợ</p>
                </div>
            </div>
            <div class="chatbot-header-actions">
                <button id="chatbot-clear" title="Xóa lịch sử chat" class="chatbot-action-btn">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button id="chatbot-close" title="Thu nhỏ" class="chatbot-action-btn">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="chatbot-messages" class="chatbot-messages">
            <!-- Tin nhắn chào mừng mặc định -->
            <div class="chat-message bot">
                <div class="chat-bubble">
                    Xin chào! Tôi là trợ lý ảo **DomusHub** 😊. Tôi có thể giúp bạn tra cứu thông tin nội quy tòa nhà, biểu phí dịch vụ, hoặc hỗ trợ gửi yêu cầu sửa chữa sự cố. Bạn cần tôi giúp gì hôm nay?
                </div>
            </div>
            

        </div>

        <!-- Typing Indicator (Đang gõ...) -->
        <div id="chatbot-typing" class="chatbot-typing hidden">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>

        <!-- Suggestion Chips -->
        <div id="chatbot-suggestions" class="chatbot-suggestions">
            <button type="button" class="suggestion-chip" data-question="Hóa đơn chưa thanh toán của tôi">💰 Hóa đơn cần đóng</button>
            <button type="button" class="suggestion-chip" data-question="Trạng thái sửa chữa sự cố của tôi">🔧 Trạng thái sửa chữa</button>
            <button type="button" class="suggestion-chip" data-question="Thông tin xe đăng ký và lốt đỗ của tôi">🚗 Xe & lốt đỗ</button>
            <button type="button" class="suggestion-chip" data-question="Thời gian làm việc của Ban quản lý tòa nhà">📞 Lịch làm việc BQL</button>
        </div>

        <!-- Input Area -->
        <form id="chatbot-form" class="chatbot-input-area" onsubmit="return false;">
            <input type="text" id="chatbot-input" placeholder="Nhập câu hỏi của bạn..." autocomplete="off" maxlength="1000">
            <button type="submit" id="chatbot-send" aria-label="Gửi tin nhắn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<style>
/* CSS cho Chatbot AI */
/* Suggestion Chips */
.chatbot-suggestions {
    padding: 8px 12px;
    background-color: #f8f9ff;
    border-top: 1px solid rgba(0, 35, 111, 0.05);
    display: flex;
    gap: 8px;
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    cursor: grab;
    scrollbar-width: none; /* Firefox */
}
.chatbot-suggestions:active {
    cursor: grabbing;
}
/* For Webkit browsers (Chrome, Safari, Edge) */
.chatbot-suggestions::-webkit-scrollbar {
    display: none; /* Hide scrollbar completely */
}
.suggestion-chip {
    flex-shrink: 0;
    user-select: none;
    background-color: #ffffff;
    border: 1px solid rgba(0, 35, 111, 0.1);
    border-radius: 16px;
    padding: 6px 12px;
    font-size: 11.5px;
    color: #00236f;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
    font-weight: 500;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.suggestion-chip:hover {
    background-color: #00236f;
    color: #ffffff;
    border-color: #00236f;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 35, 111, 0.15);
}
.suggestion-chip:active {
    transform: translateY(0);
}

.chatbot-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: var(--font-family, 'Inter', sans-serif);
}

/* Chat Bubble Button */
.chatbot-bubble {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00236f 0%, #1e3a8a 100%);
    color: #ffffff;
    border: none;
    outline: none;
    cursor: pointer;
    box-shadow: 0px 8px 24px rgba(30, 58, 138, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 24px;
    position: relative;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.chatbot-bubble:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0px 12px 28px rgba(30, 58, 138, 0.4);
}

.chatbot-bubble:active {
    transform: scale(0.95);
}

.chatbot-bubble--hidden {
    opacity: 0 !important;
    transform: scale(0.7) !important;
    pointer-events: none !important;
}

.chatbot-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background-color: #10b981;
    color: white;
    font-size: 9px;
    font-weight: 700;
    padding: 3px 6px;
    border-radius: 10px;
    border: 2px solid #ffffff;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Chat Window */
.chatbot-window {
    width: 340px;
    height: 480px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: var(--radius-lg, 16px);
    box-shadow: var(--color-shadow-lg, 0px 12px 32px rgba(30,58,138,.12));
    display: flex;
    flex-direction: column;
    position: fixed;
    bottom: 24px;
    right: 24px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    transform-origin: bottom right;
    z-index: 99999;
}

.chatbot-window.align-left {
    right: auto;
    left: 24px;
    transform-origin: bottom left;
}

.chatbot-window.align-right {
    left: auto;
    right: 24px;
    transform-origin: bottom right;
}

.chatbot-window.hidden {
    opacity: 0;
    transform: scale(0.8) translateY(20px);
    pointer-events: none;
}

/* Header */
.chatbot-header {
    background: linear-gradient(135deg, #00236f 0%, #1e3a8a 100%);
    color: #ffffff;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top-left-radius: var(--radius-lg, 16px);
    border-top-right-radius: var(--radius-lg, 16px);
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.chatbot-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chatbot-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 18px;
    position: relative;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    position: absolute;
    bottom: 0;
    right: 0;
    border: 2px solid #00236f;
}

.status-dot.online {
    background-color: #10b981;
}

.chatbot-title {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.2;
}

.chatbot-status {
    margin: 2px 0 0 0;
    font-size: 11px;
    opacity: 0.8;
}

.chatbot-header-actions {
    display: flex;
    gap: 8px;
}

.chatbot-action-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background 0.2s;
}

.chatbot-action-btn:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
}

/* Messages List */
.chatbot-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background-color: #f8f9ff;
    scroll-behavior: smooth;
}

/* Custom Scrollbar */
.chatbot-messages::-webkit-scrollbar {
    width: 5px;
}

.chatbot-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chatbot-messages::-webkit-scrollbar-thumb {
    background: rgba(0, 35, 111, 0.15);
    border-radius: 10px;
}

.chatbot-messages::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 35, 111, 0.3);
}

/* Chat Bubbles */
.chat-message {
    display: flex;
    max-width: 85%;
}

.chat-message.bot {
    align-self: flex-start;
}

.chat-message.user {
    align-self: flex-end;
}

.chat-message .chat-bubble {
    padding: 10px 14px;
    font-size: 13.5px;
    line-height: 1.5;
    word-break: break-word;
}

.chat-message.bot .chat-bubble {
    background-color: #ffffff;
    color: #0b1c30;
    border-radius: 14px 14px 14px 2px;
    box-shadow: 0px 2px 6px rgba(0, 35, 111, 0.04);
    border: 1px solid rgba(0, 35, 111, 0.05);
}

.chat-message.user .chat-bubble {
    background: linear-gradient(135deg, #00236f 0%, #1e3a8a 100%);
    color: #ffffff;
    border-radius: 14px 14px 2px 14px;
    box-shadow: 0px 4px 10px rgba(30, 58, 138, 0.15);
}

/* markdown-like strong text support inside bubbles */
.chat-message .chat-bubble strong {
    font-weight: 600;
    color: inherit;
}

/* Typing Indicator Styling */
.chatbot-typing {
    align-self: flex-start;
    background-color: #ffffff;
    border-radius: 14px 14px 14px 2px;
    padding: 10px 18px;
    margin-left: 16px;
    margin-bottom: 8px;
    display: flex;
    gap: 4px;
    box-shadow: 0px 2px 6px rgba(0, 35, 111, 0.04);
    border: 1px solid rgba(0, 35, 111, 0.05);
}

.chatbot-typing.hidden {
    display: none;
}

.typing-dot {
    width: 6px;
    height: 6px;
    background-color: #00236f;
    border-radius: 50%;
    opacity: 0.4;
    animation: typing-animation 1.4s infinite ease-in-out both;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing-animation {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}

/* Input Area */
.chatbot-input-area {
    padding: 12px 16px;
    background-color: #ffffff;
    border-top: 1px solid rgba(0, 35, 111, 0.06);
    display: flex;
    align-items: center;
    gap: 8px;
}

.chatbot-input-area input {
    flex: 1;
    height: 38px;
    border: 1px solid rgba(0, 35, 111, 0.12);
    border-radius: 20px;
    padding: 0 16px;
    font-size: 13px;
    outline: none;
    transition: all 0.2s;
    background-color: #fcfdfe;
}

.chatbot-input-area input:focus {
    border-color: #00236f;
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(0, 35, 111, 0.08);
}

.chatbot-input-area button {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background-color: #00236f;
    color: #ffffff;
    border: none;
    outline: none;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(0, 35, 111, 0.15);
}

.chatbot-input-area button:hover {
    background-color: #1e3a8a;
    transform: scale(1.05);
}

.chatbot-input-area button:active {
    transform: scale(0.95);
}

/* Responsive */
@media (max-width: 480px) {
    .chatbot-widget {
        bottom: 16px;
        right: 16px;
    }
    .chatbot-window {
        width: calc(100vw - 32px);
        height: 480px;
        bottom: 16px;
        right: 16px;
        left: 16px;
    }
    .chatbot-window.align-left, .chatbot-window.align-right {
        left: 16px;
        right: 16px;
        transform-origin: bottom center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bubble = document.getElementById('chatbot-bubble');
    const win = document.getElementById('chatbot-window');
    const closeBtn = document.getElementById('chatbot-close');
    const clearBtn = document.getElementById('chatbot-clear');
    const form = document.getElementById('chatbot-form');
    const input = document.getElementById('chatbot-input');
    const messagesContainer = document.getElementById('chatbot-messages');
    const typingIndicator = document.getElementById('chatbot-typing');

    // Kiểm tra trạng thái lưu trữ cửa sổ chat và tải lịch sử bằng AJAX
    let isHistoryLoaded = false;

    function loadChatHistory() {
        if (isHistoryLoaded) return;
        
        fetch("{{ route('resident.chatbot.history') }}")
            .then(response => response.json())
            .then(data => {
                if (data.success && data.history.length > 0) {
                    // Xóa các tin nhắn cũ để load lại mới từ DB (giữ lại tin nhắn chào mừng mặc định)
                    const welcomeMsg = messagesContainer.firstElementChild;
                    messagesContainer.innerHTML = '';
                    if (welcomeMsg) {
                        messagesContainer.appendChild(welcomeMsg);
                    }
                    
                    data.history.forEach(chat => {
                        appendMessage(chat.role === 'model' ? 'bot' : 'user', chat.message);
                    });
                    scrollToBottom();
                }
                isHistoryLoaded = true;
            })
            .catch(error => {
                console.error('Error loading chat history:', error);
            });
    }

    // Logic kéo thả (Drag & Drop) cho nút AI - Classic Method
    const widget = document.getElementById('domushub-chatbot');
    let isDraggingBubble = false;
    let hasMovedBubble = false;
    let offsetX = 0, offsetY = 0;

    function startDrag(clientX, clientY) {
        if (!widget) return;
        isDraggingBubble = true;
        hasMovedBubble = false;
        
        const rect = widget.getBoundingClientRect();
        offsetX = clientX - rect.left;
        offsetY = clientY - rect.top;
        
        widget.style.transition = 'none';
        widget.style.bottom = 'auto';
        widget.style.right = 'auto';
    }

    function doDrag(clientX, clientY, e) {
        if (!isDraggingBubble || !widget) return;
        
        let newX = clientX - offsetX;
        let newY = clientY - offsetY;
        
        // Cần di chuyển ít nhất 5px để phân biệt với click
        if (!hasMovedBubble) {
            const rect = widget.getBoundingClientRect();
            if (Math.abs(newX - rect.left) + Math.abs(newY - rect.top) > 5) {
                hasMovedBubble = true;
            }
        }
        
        if (hasMovedBubble) {
            if (e && e.cancelable) e.preventDefault();
            
            const maxX = window.innerWidth - widget.offsetWidth;
            const maxY = window.innerHeight - widget.offsetHeight;
            
            widget.style.left = Math.max(0, Math.min(newX, maxX)) + 'px';
            widget.style.top = Math.max(0, Math.min(newY, maxY)) + 'px';
        }
    }

    function endDrag() {
        if (!isDraggingBubble || !widget) return;
        isDraggingBubble = false;
        
        widget.style.transition = 'left 0.3s ease-out, top 0.3s ease-out';
        
        const centerX = window.innerWidth / 2;
        const rect = widget.getBoundingClientRect();
        const widgetCenterX = rect.left + widget.offsetWidth / 2;
        
        if (widgetCenterX < centerX) {
            widget.style.left = '24px';
            if (win) {
                win.classList.add('align-left');
                win.classList.remove('align-right');
            }
        } else {
            const targetLeft = window.innerWidth - widget.offsetWidth - 24;
            widget.style.left = targetLeft + 'px';
            if (win) {
                win.classList.add('align-right');
                win.classList.remove('align-left');
            }
        }
        
        setTimeout(() => {
            if (!isDraggingBubble && widget) {
                widget.style.transition = 'none';
            }
        }, 300);
    }

    if (bubble) {
        // Mouse events
        bubble.addEventListener('mousedown', (e) => startDrag(e.clientX, e.clientY));
        document.addEventListener('mousemove', (e) => doDrag(e.clientX, e.clientY, e));
        document.addEventListener('mouseup', endDrag);

        // Touch events
        bubble.addEventListener('touchstart', (e) => startDrag(e.touches[0].clientX, e.touches[0].clientY), {passive: true});
        document.addEventListener('touchmove', (e) => doDrag(e.touches[0].clientX, e.touches[0].clientY, e), {passive: false});
        document.addEventListener('touchend', endDrag);

        // Mở/Thu nhỏ cửa sổ chat
        bubble.addEventListener('click', function(e) {
            if (hasMovedBubble) {
                hasMovedBubble = false;
                return;
            }
            
            bubble.classList.add('chatbot-bubble--hidden');
            setTimeout(function() {
                if (win) {
                    win.classList.remove('hidden');
                    sessionStorage.setItem('chatbot_open', 'true');
                    input.focus();
                    loadChatHistory();
                    scrollToBottom();
                }
            }, 250);
        });
    }

    const isChatOpen = sessionStorage.getItem('chatbot_open') === 'true';
    if (isChatOpen) {
        win.classList.remove('hidden');
        bubble.classList.add('chatbot-bubble--hidden');
        loadChatHistory();
    }

    function closeChatWindow() {
        win.classList.add('hidden');
        sessionStorage.setItem('chatbot_open', 'false');
        // Bubble hiện lại sau khi cửa sổ đóng
        setTimeout(function() {
            bubble.classList.remove('chatbot-bubble--hidden');
        }, 200);
    }

    closeBtn.addEventListener('click', function() {
        closeChatWindow();
    });

    // Cuộn xuống cuối khung chat
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Tự động cuộn khi mở
    scrollToBottom();

    // Drag-to-scroll for suggestions chips on desktop & Click handling
    const suggestionsContainer = document.getElementById('chatbot-suggestions');
    let isDown = false;
    let startX;
    let scrollLeft;
    let dragDistance = 0;
    let dragStartX = 0;

    suggestionsContainer.addEventListener('mousedown', (e) => {
        isDown = true;
        dragStartX = e.pageX;
        startX = e.pageX - suggestionsContainer.offsetLeft;
        scrollLeft = suggestionsContainer.scrollLeft;
    });

    suggestionsContainer.addEventListener('mouseleave', () => {
        isDown = false;
    });

    suggestionsContainer.addEventListener('mouseup', () => {
        isDown = false;
    });

    suggestionsContainer.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - suggestionsContainer.offsetLeft;
        const walk = (x - startX) * 1.5; // Tốc độ trượt
        suggestionsContainer.scrollLeft = scrollLeft - walk;
        dragDistance = Math.abs(e.pageX - dragStartX);
    });

    // Cuộn ngang bằng chuột cuộn (vertical wheel scroll -> horizontal scroll)
    suggestionsContainer.addEventListener('wheel', (e) => {
        if (e.deltaY !== 0) {
            e.preventDefault();
            suggestionsContainer.scrollLeft += e.deltaY;
        }
    });

    document.querySelectorAll('.suggestion-chip').forEach(function(chip) {
        chip.addEventListener('click', function(e) {
            // Nếu khoảng cách kéo rê chuột lớn hơn 5px thì tính là drag, không trigger click
            if (dragDistance > 5) {
                dragDistance = 0;
                return;
            }
            dragDistance = 0; // reset
            const question = this.getAttribute('data-question');
            input.value = question;
            form.dispatchEvent(new Event('submit'));
        });
    });

    // Helper phân tích an toàn HTML, chữ in đậm markdown (**) và link markdown [text](url), và danh sách bullet list
    function formatMessageText(text) {
        // Chuyển đổi các thẻ <br> có sẵn (từ Blade render) thành dòng mới \n để parse đồng bộ
        let cleaned = text.replace(/<br\s*\/?>/gi, '\n');

        // Escape các thẻ HTML thô để tránh XSS
        let escaped = cleaned
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");

        // Thay thế các ký tự **text** thành <strong>text</strong>
        let formatted = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        // Thay thế [text](url) thành thẻ liên kết <a href="$2">text</a>
        formatted = formatted.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" style="color: #00236f; font-weight: 600; text-decoration: underline;">$1</a>');

        // Phân tích danh sách gạch đầu dòng (bắt đầu bằng - hoặc * kèm dấu cách)
        let lines = formatted.split('\n');
        let inList = false;
        for (let i = 0; i < lines.length; i++) {
            let line = lines[i].trim();
            if (line.startsWith('- ') || line.startsWith('* ')) {
                let content = line.substring(2);
                if (!inList) {
                    lines[i] = '<ul style="margin: 6px 0 6px 18px; padding-left: 0; list-style-type: disc; display: block;"><li style="margin-bottom: 3px;">' + content + '</li>';
                    inList = true;
                } else {
                    lines[i] = '<li style="margin-bottom: 3px;">' + content + '</li>';
                }
            } else {
                if (inList) {
                    lines[i - 1] += '</ul>';
                    inList = false;
                }
            }
        }
        if (inList) {
            lines[lines.length - 1] += '</ul>';
        }

        // Ghép các dòng lại và xử lý xuống dòng <br>, bỏ qua <br> bao quanh thẻ danh sách
        let result = '';
        for (let i = 0; i < lines.length; i++) {
            let current = lines[i];
            let next = (i + 1 < lines.length) ? lines[i + 1].trim() : '';
            let currentTrimmed = current.trim();
            
            result += current;
            
            if (currentTrimmed.endsWith('</ul>') || 
                currentTrimmed.startsWith('<li') || 
                next.startsWith('- ') || 
                next.startsWith('* ')) {
                result += '\n';
            } else if (i < lines.length - 1) {
                result += '<br>';
            }
        }
        return result;
    }

    // Định dạng tất cả các tin nhắn cũ/mặc định lúc load trang
    document.querySelectorAll('.chatbot-messages .chat-bubble').forEach(function(bubble) {
        bubble.innerHTML = formatMessageText(bubble.innerHTML.trim());
    });

    // Gửi tin nhắn
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = input.value.trim();
        if (!message) return;

        // Thêm tin nhắn của User vào giao diện
        appendMessage('user', message);
        input.value = '';
        input.focus();

        // Hiển thị hiệu ứng đang gõ
        typingIndicator.classList.remove('hidden');
        scrollToBottom();

        // Gửi request API
        fetch("{{ route('resident.chatbot.message') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => {
            if (response.status === 429) {
                return { success: false, isRateLimit: true, message: 'Bạn đang gửi tin nhắn quá nhanh. Vui lòng đợi 1 phút và thử lại.' };
            }
            return response.json();
        })
        .then(data => {
            // Ẩn hiệu ứng gõ
            typingIndicator.classList.add('hidden');

            if (data.success) {
                appendMessage('bot', data.reply);
            } else {
                if (data.isRateLimit) {
                    appendMessage('bot', '⚠️ ' + data.message);
                } else {
                    appendMessage('bot', '⚠️ Có lỗi xảy ra: ' + (data.message || 'Không rõ nguyên nhân.'));
                }
            }
            scrollToBottom();
        })
        .catch(error => {
            typingIndicator.classList.add('hidden');
            appendMessage('bot', '⚠️ Lỗi mạng. Không thể kết nối đến máy chủ AI.');
            console.error('Chatbot error:', error);
            scrollToBottom();
        });
    });

    // Xóa lịch sử chat
    clearBtn.addEventListener('click', function() {
        if (confirm('Bạn có chắc chắn muốn xóa lịch sử cuộc trò chuyện này?')) {
            fetch("{{ route('resident.chatbot.clear') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Xóa tất cả tin nhắn trên giao diện ngoại trừ tin nhắn chào mừng đầu tiên
                    const welcomeMsg = messagesContainer.firstElementChild;
                    messagesContainer.innerHTML = '';
                    if (welcomeMsg) {
                        messagesContainer.appendChild(welcomeMsg);
                    }
                    appendMessage('bot', '🧹 Lịch sử chat đã được làm mới. Tôi có thể giúp gì thêm cho bạn?');
                    scrollToBottom();
                }
            })
            .catch(error => {
                alert('Không thể xóa lịch sử chat. Vui lòng thử lại sau.');
                console.error('Clear chat error:', error);
            });
        }
    });

    // Hàm append tin nhắn vào DOM
    function appendMessage(sender, text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender === 'bot' ? 'bot' : 'user'}`;
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'chat-bubble';
        bubbleDiv.innerHTML = formatMessageText(text);
        
        messageDiv.appendChild(bubbleDiv);
        messagesContainer.appendChild(messageDiv);
    }
});
</script>
