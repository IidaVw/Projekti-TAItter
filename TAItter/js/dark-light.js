function toggleTheme() {
    const html = document.documentElement;
    const themeIcon = document.getElementById('theme-icon');
    
    // Add rotation animation
    themeIcon.classList.add('rotating');
    
    // Remove animation after it completes
    setTimeout(() => {
        themeIcon.classList.remove('rotating');
    }, 600);
    
    if (html.getAttribute('data-theme') === 'dark') {
        html.removeAttribute('data-theme');
        themeIcon.textContent = '🌙';
        themeIcon.className = 'theme-icon moon';
    } else {
        html.setAttribute('data-theme', 'dark');
        themeIcon.textContent = '☀️';
        themeIcon.className = 'theme-icon sun';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const themeIcon = document.getElementById('theme-icon');
    
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (prefersDark) {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeIcon.textContent = '☀️';
        themeIcon.className = 'theme-icon sun';
    }
});


document.querySelectorAll('.action').forEach(action => {
    action.addEventListener('click', function(e) {
        e.preventDefault();
        this.style.color = 'var(--accent-primary)';
        this.style.transform = 'scale(1.1)';
        
        setTimeout(() => {
            this.style.color = '';
            this.style.transform = '';
        }, 200);
    });
});

document.querySelectorAll('.btn-primary, .btn-secondary, .login-btn').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
    });
    
    btn.addEventListener('mouseleave', function() {
        this.style.transform = '';
    });
});