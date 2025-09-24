function toggleTheme() {
      const html = document.documentElement;
      const themeIcon = document.getElementById('theme-icon');
      const themeText = document.getElementById('theme-text');
      
      if (html.getAttribute('data-theme') === 'dark') {
        html.removeAttribute('data-theme');
        themeIcon.textContent = '🌙';
        themeText.textContent = 'Dark mode';
        localStorage.setItem('theme', 'light');
      } else {
        html.setAttribute('data-theme', 'dark');
        themeIcon.textContent = '☀️';
        themeText.textContent = 'Light mode';
        localStorage.setItem('theme', 'dark');
      }
    }

    // Load saved theme on page load
    document.addEventListener('DOMContentLoaded', function() {
      const savedTheme = localStorage.getItem('theme');
      const themeIcon = document.getElementById('theme-icon');
      const themeText = document.getElementById('theme-text');
      
      if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeIcon.textContent = '☀️';
        themeText.textContent = 'Light mode';
      }
    });

    // Add some interactivity to demo tweets
    document.querySelectorAll('.action').forEach(action => {
      action.addEventListener('click', function(e) {
        e.preventDefault();
        this.style.color = 'var(--accent-primary)';
        setTimeout(() => {
          this.style.color = '';
        }, 1000);
      });
    });