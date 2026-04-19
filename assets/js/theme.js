function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('collapsed');
}

function toggleTheme() {
  document.body.classList.toggle('light');
  const btn = document.querySelector('.settings-btn');
  if (document.body.classList.contains('light')) {
    btn.textContent = '🌞'; // soleil
  } else {
    btn.textContent = '🌙'; // lune
  }
}
