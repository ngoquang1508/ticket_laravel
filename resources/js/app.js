import './bootstrap';
import Toastify from 'toastify-js';

import { initSearchBars } from './components/search-bar';
import { initLoadMoreEvents } from './components/load-more-events';
import { initEventSchedule } from './components/event-schedule';
import './seatmap-konva.js';

document.addEventListener('DOMContentLoaded', () => {
    initSearchBars();
    initLoadMoreEvents();
    initEventSchedule();

    const resendForm = document.querySelector('form[action$="/forgot-password"] input[name="resend"]')?.form;
    const resendButton = document.getElementById('resend-otp');

    if (resendForm && resendButton) {
        resendForm.addEventListener('submit', () => {
            const email = resendForm.querySelector('input[name="email"]')?.value;

            if (email) {
                localStorage.setItem(`password-reset-resend-${email}`, Date.now().toString());
            }
        });
    }

    if (window.appToast) {
        const content = document.createElement('div');
        content.className = 'app-toast-content';

        const icon = document.createElement('span');
        icon.className = `app-toast-icon app-toast-icon-${window.appToast.type}`;
        icon.textContent = window.appToast.type === 'success' ? '✓' : '!';

        const copy = document.createElement('div');
        copy.className = 'app-toast-copy';

        const title = document.createElement('strong');
        title.className = 'app-toast-title';
        title.textContent = window.appToast.title;

        const message = document.createElement('span');
        message.className = 'app-toast-message';
        message.textContent = window.appToast.message;

        copy.append(title, message);
        content.append(icon, copy);

        Toastify({
            node: content,
            duration: 5000,
            close: true,
            gravity: 'top',
            position: 'right',
            stopOnFocus: true,
            className: `app-toast app-toast-${window.appToast.type}`,
        }).showToast();
    }
});
