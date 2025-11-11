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
});

// Smooth scroll to top
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
}

// Auto slide for news (optional enhancement)
let currentNewsIndex = 0;
const newsItems = document.querySelectorAll(".berita-item");

setInterval(() => {
  newsItems.forEach((item, index) => {
    item.style.opacity = index === currentNewsIndex ? 1 : 0.5;
    item.style.transform =
      index === currentNewsIndex ? "scale(1.02)" : "scale(1)";
    item.style.zIndex = index === currentNewsIndex ? 10 : 1;
  });
  currentNewsIndex = (currentNewsIndex + 1) % newsItems.length;
}, 3000);

// Add hover effect to cards
document.querySelectorAll(".unggul-item, .jurusan-item").forEach((item) => {
  item.addEventListener("mouseenter", () => {
    item.style.boxShadow = "0 8px 20px rgba(0,0,0,0.1)";
    item.style.transform = "scale(1.03)";
  });
  item.addEventListener("mouseleave", () => {
    item.style.boxShadow = "0 4px 12px rgba(0,0,0,0.05)";
    item.style.transform = "scale(1)";
  });
});

// Optional: Add click to expand or redirect
document.querySelectorAll(".berita-item").forEach((item) => {
  item.addEventListener("click", () => {
    alert("Anda klik berita: " + item.querySelector("h3").innerText);
  });
});

// Add smooth scroll to anchor links (if needed)
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const targetId = this.getAttribute("href");
    const targetSection = document.querySelector(targetId);
    if (targetSection) {
      window.scrollTo({
        top: targetSection.offsetTop - 80,
        behavior: "smooth",
      });
    }
  });
});
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
