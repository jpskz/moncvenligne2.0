// Sélectionne toutes les sections de la page
const sections = document.querySelectorAll('section');

// Crée une fonction d'observation avec l'API Intersection Observer
const options = {
  root: null, // viewport
  threshold: 0.1 // 10% de la section visible déclenche l'effet
};

const observer = new IntersectionObserver((entries, observer) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');  // Ajoute la classe 'visible' à la section visible
    } else {
      entry.target.classList.remove('visible'); // Retire la classe 'visible' lorsque la section sort de la vue
    }
  });
}, options);

// Observe chaque section
sections.forEach(section => {
  observer.observe(section);
});


// Pop-up
const welcomeModal = document.getElementById('welcomeModal');
const closeModalButton = document.getElementById('closeModal');

window.addEventListener('load', () => {
welcomeModal.style.display = 'flex';
});

closeModalButton.addEventListener('click', () => {
welcomeModal.style.display = 'none';
}); 

// Icone hamburger 
const btn = document.querySelector('.btn1');

btn.addEventListener('click', presentation)

function presentation(){

  btn.classList.toggle('active')

}

// Navigation
const btnMenu = document.querySelector('.logo-menu');

const menu = document.querySelector('.liste-nav');

btnMenu.addEventListener('click', () => {
    menu.classList.toggle('active')
})

const allLinks = document.querySelectorAll ('.item-nav');

allLinks.forEach(item => {

  item.addEventListener('click', () => {
    menu.classList.remove('active');
    btn.classList.remove('active');
  })

})
