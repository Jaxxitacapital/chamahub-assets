// Background image carousel
const images = [
  'bck/1.jpg',
  'bck/2.jpg',
  'bck/3.jpg',
  'bck/4.jpg',
  'bck/5.jpg'
];

let index = 0;

function updateBackground() {
  const imageUrl = images[index];
  document.body.style.backgroundImage = `url(${imageUrl})`;
  document.body.style.setProperty('--bg-image', `url(${imageUrl})`);

  const bodyBefore = document.querySelector('body::before');
  if (bodyBefore) {
    bodyBefore.style.backgroundImage = `url(${imageUrl})`;
  }

  index = (index + 1) % images.length;
}

setInterval(updateBackground, 5000);

window.addEventListener('load', () => {
  updateBackground();

  // Hide loading screen and show main content
  setTimeout(() => {
    const loader = document.querySelector('.loading-screen');
    const content = document.querySelector('.content');
    if (loader) loader.style.display = 'none';
    if (content) content.style.display = 'block';
  }, 2000);
});

// Register service worker
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('sw.js')
    .then(reg => console.log('✅ Service Worker registered:', reg))
    .catch(err => console.warn('❌ Service Worker registration failed:', err));
}
