// Get the button
        const backToTopButton = document.getElementById("backToTopBtn");

        // Show/hide button based on scroll position
        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                backToTopButton.style.display = "block";
            } else {
                backToTopButton.style.display = "none";
            }
        });

        // Scroll to top when button is clicked
        backToTopButton.addEventListener("click", () => {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });