document.addEventListener("DOMContentLoaded", () => {
  // Function to initialize modals
  function initializeModals() {
    // Ensure all modals are properly initialized and functioning
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
      // Attach event listeners for modals to ensure they open/close correctly
      modal.addEventListener('show.bs.modal', function (e) {
        // Optional: Custom logic to handle modal content before show
      });
    });
  }

  // Pagination click event handler
  document.querySelectorAll(".page-link").forEach(link => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const page = this.getAttribute("href").split("=")[1];

      // Fetch the new page content
      fetch(`dashboard.php?page=${page}`)
        .then(response => response.text())
        .then(html => {
          // Parse the HTML of the new page
          const parser = new DOMParser();
          const newDoc = parser.parseFromString(html, "text/html");

          // Replace the table body and pagination with the new content
          document.querySelector("tbody").innerHTML = newDoc.querySelector("tbody").innerHTML;
          document.querySelector(".pagination").innerHTML = newDoc.querySelector(".pagination").innerHTML;

          // Reinitialize modals and any dynamic elements
          initializeModals();
        })
        .catch(err => console.error("Error loading page:", err));
    });
  });

  // Initialize modals on initial page load
  initializeModals();
});
