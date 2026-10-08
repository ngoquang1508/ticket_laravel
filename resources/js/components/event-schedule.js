export function initEventSchedule() {
    const headers = document.querySelectorAll('.schedule-header');

    headers.forEach(header => {
        header.addEventListener('click', (e) => {
            if (e.target.closest('button')) {
                return; // Prevent toggling when clicking the "Mua vé ngay" button
            }

            const content = header.nextElementSibling;
            const arrow = header.querySelector('.arrow');

            if (!content || !content.classList.contains('schedule-content')) {
                return;
            }

            const isOpen = content.classList.contains('max-h-[500px]');

            if (isOpen) {
                content.classList.remove(
                    'max-h-[500px]',
                    'opacity-100'
                );

                content.classList.add(
                    'max-h-0',
                    'opacity-0'
                );

                if (arrow) {
                    arrow.classList.remove('rotate-90');
                }
            } else {
                content.classList.remove(
                    'max-h-0',
                    'opacity-0'
                );

                content.classList.add(
                    'max-h-[500px]',
                    'opacity-100'
                );

                if (arrow) {
                    arrow.classList.add('rotate-90');
                }
            }
        });
    });
}