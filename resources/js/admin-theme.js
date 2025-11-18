document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("themeToggle"); // toggle dark mode

    // Set awal dari localStorage
    if (localStorage.getItem("theme") === "dark") {
        document.body.classList.add("dark-mode");
        if (toggle) toggle.checked = true;
    }

    // Kalau tombolnya diubah
    if (toggle) {
        toggle.addEventListener("change", function () {
            if (toggle.checked) {
                document.body.classList.add("dark-mode");
                localStorage.setItem("theme", "dark");
            } else {
                document.body.classList.remove("dark-mode");
                localStorage.setItem("theme", "light");
            }
        });
    }
});
