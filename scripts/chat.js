class ChatApp {
    constructor() {
        this.dialogsList = document.getElementById('dialogs-list');
        this.messagesBox = document.getElementById('messages');
        this.form = document.getElementById('msg-form');
        this.input = document.getElementById('msg-input');

        this.loadDialogs();
        setInterval(() => this.loadDialogs(), 5000);

        if (this.form) {
            const receiverId = document.getElementById('receiver_id').value;
            const adId = document.getElementById('advertisement_id').value;

            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.sendMessage(receiverId, adId);
            });

            this.loadMessages(receiverId, adId);
            setInterval(() => this.loadMessages(receiverId, adId), 3000);
        }
    }

    loadDialogs() {
        fetch('/scripts/get_dialogs.php')
            .then(r => r.json())
            .then(data => {
                if (!data.dialogs) return;
                this.dialogsList.innerHTML = '';
                data.dialogs.forEach(d => {
                    const a = document.createElement('a');
                    a.className = 'dialog-item';
                    a.href = '/layout/chat.php?to=' + d.other_id + (d.ad_id ? '&ad=' + d.ad_id : '');

                    const unread = d.unread > 0 ? '<span class="dialog-badge">' + d.unread + '</span>' : '';

                    a.innerHTML = `
                        <div class="dialog-top">
                            <span class="dialog-name">${d.name}</span>
                            <span class="dialog-date">${d.last_date}</span>
                        </div>
                        <div class="dialog-bottom">
                            <span class="dialog-text">${d.last_text}</span>
                            ${unread}
                        </div>
                    `;
                    this.dialogsList.appendChild(a);
                });
            });
    }

    loadMessages(receiverId, adId) {
        fetch('/scripts/get_messages.php?chat_with=' + receiverId + '&advertisement_id=' + adId)
            .then(r => r.json())
            .then(data => {
                if (!data.messages) return;
                const box = this.messagesBox;
                const wasBottom = box.scrollTop + box.clientHeight >= box.scrollHeight - 20;

                box.innerHTML = '';
                data.messages.forEach(m => {
                    const div = document.createElement('div');
                    div.className = 'msg ' + (m.is_mine ? 'msg-mine' : 'msg-other');
                    div.innerHTML = `
                        <div class="msg-text">${m.text}</div>
                        <div class="msg-date">${m.date}</div>
                    `;
                    box.appendChild(div);
                });

                if (wasBottom) box.scrollTop = box.scrollHeight;
            });
    }

    sendMessage(receiverId, adId) {
        const text = this.input.value.trim();
        if (!text) return;

        const csrf = document.getElementById('csrf_token').value;
        const body = new URLSearchParams();
        body.append('receiver_id', receiverId);
        body.append('advertisement_id', adId);
        body.append('text', text);
        body.append('csrf_token', csrf);

        fetch('/scripts/send_message.php', {
            method: 'POST',
            body: body
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                this.input.value = '';
                this.loadMessages(receiverId, adId);
                this.loadDialogs();
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => new ChatApp());