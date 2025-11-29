document.addEventListener("DOMContentLoaded", () => {
  const dropdowns = document.querySelectorAll(".dropdown");

  dropdowns.forEach((dropdown) => {
    const button = dropdown.querySelector(".dropbtn");

    button.addEventListener("click", (e) => {
      e.preventDefault();

      // Tutup dropdown lain
      dropdowns.forEach((d) => {
        if (d !== dropdown) d.classList.remove("show");
      });

      // Toggle dropdown yang diklik
      dropdown.classList.toggle("show");
    });
  });

  // Klik di luar area dropdown untuk menutup semua
  window.addEventListener("click", (e) => {
    if (![...dropdowns].some((dropdown) => dropdown.contains(e.target))) {
      dropdowns.forEach((dropdown) => dropdown.classList.remove("show"));
    }
  });
});

