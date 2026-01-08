const openBtn = document.getElementById("openSidebar");
const closeBtn = document.getElementById("closeSidebar");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");

openBtn.addEventListener("click", () => {
  sidebar.classList.add("show");
  overlay.classList.add("show");
});

closeBtn.addEventListener("click", () => {
  sidebar.classList.remove("show");
  overlay.classList.remove("show");
});

overlay.addEventListener("click", () => {
  sidebar.classList.remove("show");
  overlay.classList.remove("show");
});
