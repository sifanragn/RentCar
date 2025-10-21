document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ Admin theme aktif");

  /* ===================================================
     VARIABEL DASAR
  =================================================== */
  const body = document.body;
  const modeToggle = document.getElementById("modeToggle");
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebar = document.querySelector(".admin-sidebar");
  const wrapper = document.querySelector(".admin-wrapper");
  const navbar = document.querySelector(".admin-navbar");
  const modeText = document.querySelector(".mode-text");

  /* ===================================================
     THEME MODE (Light / Dark)
  =================================================== */
  const savedTheme = localStorage.getItem("theme");

  if (savedTheme === "light") {
    body.classList.add("light-mode");
    if (modeToggle) modeToggle.checked = true;
    if (modeText) modeText.textContent = "Light Mode";
  } else {
    if (modeText) modeText.textContent = "Dark Mode";
  }

  if (modeToggle) {
    modeToggle.addEventListener("change", () => {
      const isLight = modeToggle.checked;
      body.classList.toggle("light-mode", isLight);
      if (isLight) {
        localStorage.setItem("theme", "light");
        if (modeText) modeText.textContent = "Light Mode";
      } else {
        localStorage.removeItem("theme");
        if (modeText) modeText.textContent = "Dark Mode";
      }
    });
  }

  /* ===================================================
     SIDEBAR TOGGLE (Default collapsed)
  =================================================== */
  const overlay = document.createElement("div");
  overlay.classList.add("sidebar-overlay");
  document.body.appendChild(overlay);

  const SIDEBAR_STATE_KEY = "sidebarCollapsed";
  localStorage.setItem(SIDEBAR_STATE_KEY, "true");

  if (sidebar && wrapper) {
    const isCollapsed = localStorage.getItem(SIDEBAR_STATE_KEY) === "true";

    sidebar.classList.add("no-transition");
    sidebar.classList.toggle("collapsed", isCollapsed);
    wrapper.classList.toggle("sidebar-collapsed", isCollapsed);

    if (navbar) {
      navbar.style.left = isCollapsed ? "75px" : "200px";
      navbar.style.width = isCollapsed
        ? "calc(100% - 75px)"
        : "calc(100% - 200px)";
    }

    setTimeout(() => sidebar.classList.remove("no-transition"), 50);
  }

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener("click", () => {
      if (window.innerWidth <= 992) {
        sidebar.classList.toggle("active");
        overlay.classList.toggle("active");
      } else {
        const collapsed = !sidebar.classList.contains("collapsed");
        sidebar.classList.toggle("collapsed", collapsed);
        wrapper.classList.toggle("sidebar-collapsed", collapsed);
        localStorage.setItem(SIDEBAR_STATE_KEY, collapsed);

        if (navbar) {
          navbar.style.left = collapsed ? "75px" : "200px";
          navbar.style.width = collapsed
            ? "calc(100% - 75px)"
            : "calc(100% - 200px)";
        }
      }
    });
  }

  overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
  });

  window.addEventListener("resize", () => {
    if (!navbar) return;

    if (window.innerWidth > 992) {
      sidebar.classList.remove("active");
      overlay.classList.remove("active");

      const isCollapsed = sidebar.classList.contains("collapsed");
      navbar.style.left = isCollapsed ? "75px" : "200px";
      navbar.style.width = isCollapsed
        ? "calc(100% - 75px)"
        : "calc(100% - 200px)";
    } else {
      navbar.style.left = "0";
      navbar.style.width = "100%";
    }
  });

  /* ===================================================
     CUSTOM DROPDOWN BRAND DENGAN LOGO
  =================================================== */
  console.log("✅ JS dropdown merek aktif");

  const dropdown = document.getElementById("brandDropdown");
  const selected = document.getElementById("selectedBrand");
  const options = document.getElementById("brandOptions");
  const hiddenInput = document.getElementById("brand_input");

  if (dropdown && selected && options && hiddenInput) {
    selected.addEventListener("click", (e) => {
      e.stopPropagation();
      options.classList.toggle("active");
    });

    options.querySelectorAll("li").forEach((item) => {
      item.addEventListener("click", () => {
        const id = item.dataset.id;
        const name = item.querySelector("span").textContent;
        const img = item.querySelector("img")?.getAttribute("src") || "";

        hiddenInput.value = id;

        selected.innerHTML = img
          ? `<div style="display:flex;align-items:center;gap:8px;">
               <img src="${img}" style="width:22px;height:22px;object-fit:contain;">
               <span>${name}</span>
             </div>
             <i class='bi bi-chevron-down'></i>`
          : `<span>${name}</span><i class='bi bi-chevron-down'></i>`;

        options.classList.remove("active");
      });
    });

    document.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) {
        options.classList.remove("active");
      }
    });
  }
  document.querySelectorAll('.custom-select').forEach(select => {
  const selected = select.querySelector('.select-selected');
  const options = select.querySelector('.select-options');
  const hiddenInput = select.querySelector('input[type="hidden"]');

  selected.addEventListener('click', e => {
    e.stopPropagation();
    // tutup semua dropdown lain dulu
    document.querySelectorAll('.custom-select.active').forEach(s => {
      if (s !== select) s.classList.remove('active');
    });

    // toggle dropdown
    select.classList.toggle('active');
    if (select.classList.contains('active')) {
      const rect = selected.getBoundingClientRect();
      options.style.setProperty('--x', `${rect.left}px`);
      options.style.setProperty('--y', `${rect.bottom + window.scrollY + 4}px`);
    }
  });

  options.querySelectorAll('li').forEach(option => {
    option.addEventListener('click', () => {
      selected.textContent = option.textContent;
      hiddenInput.value = option.dataset.value;
      select.classList.remove('active');
    });
  });

  // klik luar area → tutup dropdown
  document.addEventListener('click', e => {
    if (!select.contains(e.target)) select.classList.remove('active');
  });
});

});
