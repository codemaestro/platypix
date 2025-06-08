let images = [];
let current = 0;
let imagecount = 0;
let timer = null;
let fetchtimer = null;
const siteurl = location.href.indexOf('127.') > -1 ?
  "https://replace-localhost-with-final-url" :
  location.href;

function fixCaption(caption) {
  if (caption) {
    caption = caption.replace(/_+/g, " ").trim();
  }
  return caption;
}

function fetchImages() {
  fetch("./slides.php")
    .then((res) => res.json())
    .then((list) => {
      if (Array.isArray(list) && list.length > 0) {
        if (imagecount !== list.length) {
          // Won't run this function if the number of fetched
          // images matches the current image count
          console.log("Updating image list with new images.");
          images = list.map((image) => ({
            src: image.src,
            caption: image.caption,
          }));
          randomizeList(); // randomize the list of images
          imagecount = list.length; // update image count
        }
        if (current >= images.length) {
          current = 0; // reset current index if out of bounds
        }
        showImage(); // display the first image
      }
    })
    .catch((err) => console.error("Error fetching images:", err)); // handle errors
}

function showImage() {
  const img = document.getElementById("slideshow-img");
  const caption = document.createElement("p");
  const glink = `To see these images again, visit the gallery at <a href="${siteurl}">${siteurl}</a>`;
  caption.innerHTML =
    // hide the next two lines to not display filename as caption
    images[current].caption ?
      `${fixCaption(images[current].caption)}<br>${glink}` :
    `${glink}`;
  img.classList.remove("show");
  caption.classList.remove("show");
  setTimeout(() => {
    img.src = images[current].src;
    img.parentNode.parentNode.replaceChild(
      caption,
      img.parentNode.nextSibling || img.parentNode.parentNode.querySelector("p")
    );
    img.classList.add("show");
    caption.classList.add("caption");
  }, 50);
}

function nextImage() {
  current = (current + 1) % images.length;
  showImage();
}

function randomizeList() {
  if (images.length === 0) {
    return;
  }
  for (let i = images.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [images[i], images[j]] = [images[j], images[i]]; // swap elements
  }
}

function contactSheet() {
  // stop the slideshow
  [timer, fetchtimer].forEach((t) => {
    if (t) {
      clearInterval(t); // stop the slideshow
    }
  });

  // page heading
  const title = document.createElement("h1");
  title.innerText =
    images.length > 0
      ? `Gallery of ${images.length} images`
      : `No Images Available`;

  // build the gallery
  const gallery = document.createElement("div");

  // sort gallery images by filename
  images.sort((a, b) => {
    const aName = a.src.split("/").pop().toLowerCase();
    const bName = b.src.split("/").pop().toLowerCase();
    return aName.localeCompare(bName);
  });

  if (images.length > 0) {
    images.forEach((image) => {
      // create image element
      const img = document.createElement("img");
      img.src = image.src;
      img.alt = image.caption || "Image from the gallery";

      // create caption element
      const caption = document.createElement("p");
      const captionLink = document.createElement("a");
      captionLink.href = img.src;
      captionLink.innerText = img.alt;
      captionLink.target = "_blank"; // open in new tab
      caption.appendChild(captionLink);

      // wrap image and caption in a div
      const div = document.createElement("div");
      div.appendChild(img);
      div.appendChild(caption);

      // build the gallery
      gallery.appendChild(div);
    });

    gallery.classList.add("gallery");
  }

  // construct gallery page
  const wrapper = document.getElementById("container");
  wrapper.innerHTML = title.outerHTML + gallery.outerHTML;
  galleryButton("close"); // add close button
}

function galleryButton(fn = "open", el = "") {
  const button = document.createElement("button");
  button.className = "gallery-button";
  if (fn === "open") {
    button.innerText = "View Gallery";
    button.addEventListener("click", contactSheet);
  } else {
    button.innerText = "Close Gallery";
    button.addEventListener("click", () => {
      location.href = "/"; // redirect to the homepage
    });
  }
  if (el !== "") {
    // if an element is specified, insert the button before it
    el = document.querySelector(el);
    document.body.insertBefore(button, el || document.body.firstChild);
  } else {
    // append the button to the #container
    document.getElementById("container").appendChild(button);
  }
}

window.onload = function () {
  fetchImages();
  timer = setInterval(nextImage, 4000); // 5 seconds per slide
  fetchtimer = setInterval(fetchImages, 10000); // refresh image list every 10 seconds (down from original 60)
  galleryButton(); // create the gallery button
};
