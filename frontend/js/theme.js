// Dark mode persistente
const themeToggle = document.getElementById('theme-toggle');
const root = document.documentElement;

// Carregar tema salvo
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    root.classList.add('dark');
    themeToggle.textContent = '☀️';
} else {
    themeToggle.textContent = '🌙';
}

// Alternar tema
themeToggle.addEventListener('click', () => {
    if (root.classList.contains('dark')) {
    root.classList.remove('dark');
    localStorage.setItem('theme', 'light');
    themeToggle.textContent = '🌙';
    } else {
    root.classList.add('dark');
    localStorage.setItem('theme', 'dark');
    themeToggle.textContent = '☀️';
    }
});