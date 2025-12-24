const images = [
  "/Chamahub/1.jpg",
  "/Chamahub/2.jpg",
  "/Chamahub/3.jpg"
];

let index = 0;
const body = document.body;

function updateBackground() {
  body.style.backgroundImage = `url('${images[index]}')`;
  index = (index + 1) % images.length;
}

setInterval(updateBackground, 5000); // every 5 seconds
updateBackground(); // Initial call
