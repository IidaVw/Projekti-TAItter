function toggleTheme() {
  const html = document.documentElement;
  const themeIcon = document.getElementById('theme-icon');

  themeIcon.classList.add('rotating');
  setTimeout(() => themeIcon.classList.remove('rotating'), 600);

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