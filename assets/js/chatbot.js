const chatbotIcon = document.getElementById("chatbot-icon");
const chatbotContainer = document.getElementById("chatbot-container");
const chatbotClose = document.getElementById("chatbot-close");
const chatbotMessages = document.getElementById("chatbot-messages");
const chatbotInput = document.getElementById("chatbot-input");
const chatbotSend = document.getElementById("chatbot-send");

const responses = {
  "bonjour": "Bonjour ! Comment puis-je vous aider ?",
  "salut": "Salut ! Comment puis-je vous aider ?",
  "bonsoir": "Bonsoir ! Comment puis-je vous aider ?",
  "au revoir": "Au revoir ! Avez-vous une question ?",
  "aide": "Comment puis-je vous aider ?",
  "horaires": "Nous sommes ouverts du lundi au vendredi de 08h à 18h, et le samedi de 08h à 13h.",
  "adresse": "79, Boulevard Jean-Paul II, quartier Nukafu, Lomé, Togo.",
  "téléphone": "Vous pouvez nous appeler au +228 90 38 98 03.",
  "livre": "Connectez-vous et visitez la page 'Livres' pour voir les catégories disponibles.",
  "merci": "Je vous en prie."
};

function sendMessage() {
  const userText = chatbotInput.value.trim().toLowerCase();
  if (userText === "") return;

  chatbotMessages.innerHTML += `<div><strong>👤 Vous :</strong> ${chatbotInput.value}</div>`;
  chatbotInput.value = "";

  let found = false;
  for (let keyword in responses) {
    if (userText.includes(keyword)) {
      chatbotMessages.innerHTML += `<div><strong>🤖 Bot :</strong> ${responses[keyword]}</div>`;
      found = true;
      break;
    }
  }
  if (!found) {
    chatbotMessages.innerHTML += `<div><strong>🤖 Bot :</strong> Désolé, je n'ai pas compris. Essayez un mot-clé comme "bonjour", "horaires", "adresse", "livre","salut", "téléphone" ou "merci".</div>`;
  }

  chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
}

// Ouvrir/Fermer chatbot
chatbotIcon.addEventListener("click", () => chatbotContainer.style.display = "flex");
chatbotClose.addEventListener("click", () => chatbotContainer.style.display = "none");

// Envoi avec Entrée
chatbotInput.addEventListener("keypress", e => { if (e.key === "Enter") sendMessage(); });
chatbotSend.addEventListener("click", sendMessage);
