class GlassUI {
    constructor() {
        this.setupToastContainer();
    }

    setupToastContainer() {
        if (!document.querySelector('.toast-container')) {
            const container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
    }

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `glass-toast ${type}`;
        toast.innerHTML = `
            <i class="fi fi-sr-${type === 'success' ? 'check' : 'cross-circle'}"></i>
            <span>${message}</span>
        `;
        
        const container = document.querySelector('.toast-container');
        if (container) {
            container.appendChild(toast);
        } else {
            document.body.appendChild(toast);
        }
        
        requestAnimationFrame(() => {
            toast.classList.add('active');
        });
        
        setTimeout(() => {
            toast.classList.remove('active');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize the UI
const glassUI = new GlassUI();