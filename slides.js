let images = [];
let current = 0;
let timer = null;

function fixCaption(caption) {
  if (caption) {
    caption = caption.replace(/_/g, " ").trim();
  }
  return caption;
}

function fetchImages() {
  fetch("./slides.php")
    .then((res) => res.json())
    .then((list) => {
      images = list.map((image) => ({
        src: image.src,
        caption: image.caption,
      })); // map the list to full image paths
      if (current >= images.length) {
        images = randomizeList();
        current = 0; // reset current index if out of bounds
      }
      showImage(); // display the first image
    })
    .catch((err) => console.error("Error fetching images:", err)); // handle errors
}

function showImage() {
  const img = document.getElementById("slideshow-img");
  const caption = document.createElement("p");
  caption.innerText =
    fixCaption(images[current].caption) || "No caption available";
  img.classList.remove("show");
  setTimeout(() => {
    img.src = images[current].src;
    img.classList.add("show");
    img.parentNode.replaceChild(
      caption,
      img.nextSibling || img.parentNode.querySelector("p")
    );
  }, 50);
}

function nextImage() {
  current = (current + 1) % images.length;
  showImage();
}

function randomizeList() {
  for (let i = images.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [images[i], images[j]] = [images[j], images[i]]; // swap elements
  }
}

window.onload = function () {
  fetchImages();
  randomizeList(); // randomize the list initially
  timer = setInterval(nextImage, 5000); // 5 seconds per slide
  setInterval(fetchImages, 10000); // refresh image list every 10 seconds (down from original 60)
};
