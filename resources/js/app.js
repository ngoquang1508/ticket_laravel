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

    window.showAppToast = function(type, title, message) {
        const content = document.createElement('div');
        content.className = 'app-toast-content';

        const icon = document.createElement('span');
        icon.className = `app-toast-icon app-toast-icon-${type}`;
        icon.textContent = type === 'success' ? '✓' : '!';

        const copy = document.createElement('div');
        copy.className = 'app-toast-copy';

        const titleEl = document.createElement('strong');
        titleEl.className = 'app-toast-title';
        titleEl.textContent = title;

        const messageEl = document.createElement('span');
        messageEl.className = 'app-toast-message';
        messageEl.textContent = message;

        copy.append(titleEl, messageEl);
        content.append(icon, copy);

        Toastify({
            node: content,
            duration: 5000,
            close: true,
            gravity: 'top',
            position: 'right',
            stopOnFocus: true,
            className: `app-toast app-toast-${type}`,
        }).showToast();
    };

    if (window.appToast) {
        window.showAppToast(window.appToast.type, window.appToast.title, window.appToast.message);
    }
});
