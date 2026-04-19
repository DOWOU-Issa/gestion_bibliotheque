// Confirmation avant suppression
function confirmDelete() {
  return confirm("Êtes-vous sûr de vouloir supprimer ce livre ?");
}

// Exemple d’alerte automatique qui disparaît
document.addEventListener("DOMContentLoaded", function() {
  let alerts = document.querySelectorAll(".alert");
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.classList.add("fade-out");
      setTimeout(() => alert.remove(), 500); // supprime après animation
    }, 4000);
  });
});

// Animation fade-out pour les alertes
const style = document.createElement('style');
style.innerHTML = `
  .fade-out {
    opacity: 0;
    transition: opacity 0.5s ease-out;
  }
`;
document.head.appendChild(style);

// Amélioration UX : focus automatique sur le premier champ de formulaire
document.addEventListener("DOMContentLoaded", function() {
  const firstInput = document.querySelector("form input");
  if (firstInput) firstInput.focus();
});
