export function initLoadMoreEvents() {
    document.querySelectorAll('.load-more-btn').forEach(button => {
        button.addEventListener('click', function () {
            const type = this.dataset.type;
            const page = this.dataset.page;
            const url = this.dataset.url;

            const originalHtml = this.innerHTML;
            const originalWidth = this.offsetWidth + 'px';
            
            // Set fixed width so it can transition smoothly
            this.style.width = originalWidth;
            
            // Force browser reflow
            void this.offsetWidth;
            
            // Change text to spinner and shrink
            this.innerHTML = `<svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            this.style.width = '40px'; 
            this.classList.add('opacity-70', 'cursor-not-allowed', '!px-0');
            this.disabled = true;

            fetch(`${url}?type=${type}&page=${page}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.html) {
                        document.getElementById(`${type}-events-container`).insertAdjacentHTML('beforeend', data.html);

                        if (data.hasMorePages) {
                            this.dataset.page = parseInt(page) + 1;
                            this.innerHTML = originalHtml;
                            this.disabled = false;
                            this.style.width = originalWidth; // Restore width
                            this.classList.remove('opacity-70', 'cursor-not-allowed', '!px-0');
                            
                            // Remove fixed width after transition ends (300ms)
                            setTimeout(() => { this.style.width = ''; }, 300);
                        } else {
                            const wrapper = document.getElementById(`${type}-load-more-wrapper`);
                            if(wrapper) wrapper.remove();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading more events:', error);
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                    this.style.width = originalWidth;
                    this.classList.remove('opacity-70', 'cursor-not-allowed', '!px-0');
                    setTimeout(() => { this.style.width = ''; }, 300);
                });
        });
    });
}
