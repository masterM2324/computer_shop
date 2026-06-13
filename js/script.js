let slideIndex = 0;
let slides = [];
let dots = [];
let timeoutId;

document.addEventListener("DOMContentLoaded", (event) => {
  slides = document.querySelectorAll(".slide");
  dots = document.querySelectorAll(".dot");
  if (slides.length > 0) {
    showSlides(); // Start the animation
  }
});

function showSlides() {
  // Stop any existing timer
  clearTimeout(timeoutId);

  // Hide all slides and remove active class from dots
  slides.forEach((slide) => (slide.style.display = "none"));
  dots.forEach((dot) => (dot.className = dot.className.replace(" active", "")));

  // Advance the index
  slideIndex++;
  if (slideIndex > slides.length) {
    slideIndex = 1;
  }

  // Show the current slide and activate the current dot
  slides[slideIndex - 1].style.display = "block";
  dots[slideIndex - 1].className += " active";

  // Set the timeout for the next slide transition (e.g., every 5 seconds)
  timeoutId = setTimeout(showSlides, 3000);
}

// Function for manual dot navigation
function currentSlide(n) {
  // Set the index to the clicked slide number
  slideIndex = n - 1;
  // Immediately run showSlides to display the selected slide and restart the timer
  showSlides();
}
