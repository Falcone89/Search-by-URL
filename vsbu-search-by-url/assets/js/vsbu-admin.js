window.addEventListener('load', function() {
    /* Preloader */
    document.getElementById("wrapper").classList.remove("hidden");
    document.getElementById("search-preloader").classList.add("hidden");
});

/* Start search */
document.querySelectorAll(".vsbu-search-form").forEach(function(form) {
    form.addEventListener("submit", function(event) {
        document.getElementById("wrapper").classList.add("hidden");
        document.getElementById("search-preloader").classList.remove("hidden");
    });
});