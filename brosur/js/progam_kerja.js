// Toggle scroll-to-top button
window.addEventListener("scroll", function () {
  const scrollButton = document.querySelector(".scroll-top");
  if (window.scrollY > 300) {
    scrollButton.classList.add("show");
  } else {
    scrollButton.classList.remove("show");
  }

  // Animate sections on scroll
  const sections = document.querySelectorAll(".section");
  sections.forEach((section) => {
    const sectionTop = section.getBoundingClientRect().top;
    const sectionVisible = 150;
    if (sectionTop < window.innerHeight - sectionVisible) {
      section.classList.add("visible");
    }
  });

  // Add hover effect to cards
  document.querySelectorAll(".program-item").forEach((item) => {
    item.addEventListener("mouseenter", () => {
      item.style.boxShadow = "0 10px 25px rgba(0,0,0,0.1)";
      item.style.transform = "translateY(-5px)";
    });
    item.addEventListener("mouseleave", () => {
      item.style.boxShadow = "0 6px 15px rgba(0,0,0,0.05)";
      item.style.transform = "translateY(0)";
    });
  });
});

// Smooth scroll to top
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
}
// Toggle Hamburger Menu
const menuToggle = document.getElementById("menuToggle");
const mainNav = document.getElementById("mainNav");

menuToggle.addEventListener("click", () => {
  menuToggle.classList.toggle("active");
  mainNav.classList.toggle("active");
});

// Tutup menu saat klik di luar
document.addEventListener("click", (e) => {
  if (!menuToggle.contains(e.target) && !mainNav.contains(e.target)) {
    menuToggle.classList.remove("active");
    mainNav.classList.remove("active");
  }
});

// Toggle dropdown di mobile
document.querySelectorAll(".dropdown > a").forEach((link) => {
  link.addEventListener("click", (e) => {
    if (window.innerWidth <= 768) {
      e.preventDefault();
      const parent = link.parentElement;
      parent.classList.toggle("active");
    }
  });
});
