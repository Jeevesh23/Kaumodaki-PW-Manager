const darkMode = document.querySelector('.dark-mode');

function enableDarkMode() {
    localStorage.setItem('darkMode', 'enabled');
    document.body.classList.toggle('dark-mode-variables');
    darkMode.querySelector('span:nth-child(1)').classList.toggle('active');
    darkMode.querySelector('span:nth-child(2)').classList.toggle('active');
}

function disableDarkMode() {
    localStorage.setItem('darkMode', 'disabled');
    document.body.classList.toggle('dark-mode-variables');
    darkMode.querySelector('span:nth-child(1)').classList.toggle('active');
    darkMode.querySelector('span:nth-child(2)').classList.toggle('active');
}

function toggleDarkMode() {
    if (localStorage.getItem('darkMode') === 'enabled') {
        disableDarkMode();
    } else {
        enableDarkMode();
    }
}

if (localStorage.getItem('darkMode') === 'enabled') {
    enableDarkMode();
}

const sideMenu = document.querySelector('aside');
const menuBtn = document.getElementById('menu-btn');
const closeBtn = document.getElementById('close-btn');

menuBtn.addEventListener('click', () => {
    sideMenu.style.display = 'block';
});

closeBtn.addEventListener('click', () => {
    sideMenu.style.display = 'none';
});

darkMode.addEventListener('click', toggleDarkMode);