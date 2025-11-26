// ============================================
// Student Attendance System - Complete JS
// Tutorials 1 → 6 (UPDATED - Permanent Highlight)
// ============================================

// 1) Calculate Absences / Participation and Message + color rows
function calculateStats() {
  const rows = document.querySelectorAll("#attendance-body tr");
  rows.forEach(row => {
    const tds = row.querySelectorAll("td");
    // ensure row has expected columns
    if (tds.length < 17) return;

    let abs = 0, par = 0;
    for (let i = 2; i < 14; i++) {
      const v = (tds[i] && tds[i].textContent.trim()) || "";
      if (i % 2 === 0) {
        // P columns (even indices)
        if (v !== "✓") abs++;
      } else {
        // Pa columns (odd indices)
        if (v === "✓") par++;
      }
    }

    // Update Absences and Participation columns
    tds[14].textContent = abs + " Abs";
    tds[15].textContent = par + " Par";

    // Determine message based on absences and participation
    let msg = "";
    if (abs >= 5) msg = "Excluded – too many absences – You need to participate more";
    else if (abs >= 3) msg = "Warning – attendance low – You need to participate more";
    else if (abs <= 1 && par >= 3) msg = "Good attendance – Excellent participation";
    else msg = "Good attendance – Keep going";

    tds[16].textContent = msg;

    // Color rows based on absences (only if not highlighted as excellent)
    if (!row.classList.contains("excellent-highlight")) {
      row.classList.remove("red-row","yellow-row","green-row");
      if (abs >= 4) row.classList.add("red-row");
      else if (abs >= 2) row.classList.add("yellow-row");
      else row.classList.add("green-row");
    }
  });
}

// 2) Make P/Pa cells editable by click (toggle ✓)
function activateEditableCells() {
  const rows = document.querySelectorAll("#attendance-body tr");
  rows.forEach(row => {
    const tds = row.querySelectorAll("td");
    for (let i = 2; i < 14; i++) {
      if (!tds[i]) continue;
      tds[i].style.cursor = "pointer";
      tds[i].onclick = function (e) {
        e.stopPropagation(); // Prevent row click event
        this.textContent = this.textContent.trim() === "✓" ? "" : "✓";
        calculateStats();
      };
    }
  });
}

// 4) Add student — adds full row with empty sessions
document.getElementById("add-student-form").addEventListener("submit", function (e) {
  e.preventDefault();
  const last = e.target.lastLast.value.trim();
  const first = e.target.firstName.value.trim();
  const tbody = document.getElementById("attendance-body");
  const tr = document.createElement("tr");
  tr.innerHTML = `
    <td>${last}</td>
    <td>${first}</td>
    <td></td><td></td>
    <td></td><td></td>
    <td></td><td></td>
    <td></td><td></td>
    <td></td><td></td>
    <td></td><td></td>
    <td></td>
    <td></td>
    <td></td>
  `;
  tbody.appendChild(tr);
  document.getElementById("message").textContent = "Student added successfully!";
  setTimeout(() => document.getElementById("message").textContent = "", 3000);
  e.target.reset();
  calculateStats();
  activateEditableCells();
  attachJQueryEvents(); // Re-attach jQuery events to new row
});

// 5) Report (no external libraries) — safe and prevents NaN when no students
function createReport() {
  const allRows = Array.from(document.querySelectorAll("#attendance-body tr"))
    .filter(r => r.style.display !== "none"); // consider filtered rows only
  const total = allRows.length;
  let presentCount = 0, participationCount = 0;

  allRows.forEach(r => {
    const tds = r.querySelectorAll("td");
    let hasP = false, hasPa = false;
    
    // Check P columns
    for (let i = 2; i < 14; i += 2) {
      if ((tds[i] && tds[i].textContent.trim()) === "✓") hasP = true;
    }
    
    // Check Pa columns
    for (let i = 3; i < 14; i += 2) {
      if ((tds[i] && tds[i].textContent.trim()) === "✓") hasPa = true;
    }
    
    if (hasP) presentCount++;
    if (hasPa) participationCount++;
  });

  // Update report text
  document.getElementById("totalStudents").textContent = "Total Students: " + total;
  document.getElementById("presentCount").textContent = "Present (students with ≥1 P): " + presentCount;
  document.getElementById("participationCount").textContent = "Participated (students with ≥1 Pa): " + participationCount;

  // Chart bars: scale safely even if total is 0
  const maxH = 140;
  const totalH = total === 0 ? 0 : maxH;
  const presentH = total === 0 ? 0 : Math.round((presentCount / total) * maxH);
  const partH = total === 0 ? 0 : Math.round((participationCount / total) * maxH);

  document.getElementById("barTotal").style.height = totalH + "px";
  document.getElementById("barPresent").style.height = presentH + "px";
  document.getElementById("barParticipation").style.height = partH + "px";
}

// Show report button handler
document.getElementById("showReportBtn").addEventListener("click", function () {
  createReport();
  document.getElementById("reportArea").classList.remove("hidden");
  document.getElementById("reportArea").setAttribute("aria-hidden", "false");
  document.getElementById("reportArea").scrollIntoView({ behavior: "smooth" });
});

// 6) Sorting: attach to .sortable headers
(function attachSorting(){
  const headers = document.querySelectorAll(".sortable");
  const directions = {};
  headers.forEach((h, idx) => {
    directions[idx] = true;
    h.addEventListener("click", () => {
      const headerRow = h.parentElement.parentElement.querySelector("tr");
      const colIndex = Array.prototype.indexOf.call(headerRow.children, h);
      const tbody = document.getElementById("attendance-body");
      const rows = Array.from(tbody.querySelectorAll("tr"));
      
      // Sort rows
      rows.sort((a,b) => {
        const A = (a.children[colIndex] && a.children[colIndex].innerText.trim().toLowerCase()) || "";
        const B = (b.children[colIndex] && b.children[colIndex].innerText.trim().toLowerCase()) || "";
        if (A < B) return directions[idx] ? -1 : 1;
        if (A > B) return directions[idx] ? 1 : -1;
        return 0;
      });
      
      directions[idx] = !directions[idx];
      rows.forEach(r => tbody.appendChild(r));
    });
  });
})();

// ============================================
// jQuery Tutorial 5: Hover & Click Events
// ============================================
function attachJQueryEvents() {
  // 2. Highlight row on hover (mouseover/mouseout)
  $("#attendance-body tr").hover(
    function() {
      $(this).addClass("hover-highlight");
    },
    function() {
      $(this).removeClass("hover-highlight");
    }
  );

  // 4. Show modal with student info on row click
  $("#attendance-body tr").off('click').on('click', function() {
    const firstName = $(this).find("td:eq(1)").text().trim();
    const lastName = $(this).find("td:eq(0)").text().trim();
    const absences = $(this).find("td:eq(14)").text().trim();
    
    const fullName = `${firstName} ${lastName}`;
    const message = `<strong>Full Name:</strong> ${fullName}<br><strong>Absences:</strong> ${absences}`;
    
    $("#modalContent").html(message);
    $("#modalOverlay").fadeIn(300);
  });
}

// Close modal handlers
$("#closeModal, #modalOverlay").click(function(e) {
  if (e.target === this) {
    $("#modalOverlay").fadeOut(300);
  }
});

// ============================================
// Tutorial 6: Highlight Excellent Students
// UPDATED: Permanent highlight until reset
// ============================================

// Highlight Excellent Students Button
$("#highlightExcellentBtn").click(function() {
  // Find all rows with fewer than 3 absences
  $("#attendance-body tr").each(function() {
    const absenceText = $(this).find("td:eq(14)").text().trim();
    const absenceCount = parseInt(absenceText.split(" ")[0]) || 0;
    
    if (absenceCount < 3) {
      // Hide first, then fade in with PERMANENT highlight
      $(this).hide().fadeIn(1000, function() {
        $(this).addClass("excellent-highlight");
      });
    }
  });
  
  // Show success message
  showNotification("✨ Excellent students highlighted! (Press Reset to remove)", "success");
});

// Reset Colors Button
$("#resetColorsBtn").click(function() {
  // Remove excellent highlight from all rows
  $("#attendance-body tr").removeClass("excellent-highlight");
  
  // Stop any ongoing animations
  $("#attendance-body tr").stop(true, true).show();
  
  // Recalculate stats to restore original colors
  calculateStats();
  
  // Show reset message
  showNotification("🔄 Colors reset successfully!", "info");
});

// ============================================
// NEW FEATURES: Enhanced Search and Sorting
// ============================================

// Name Search Functionality
$("#nameSearch").on("keyup", function() {
  const searchTerm = $(this).val().toLowerCase().trim();
  
  if (searchTerm === "") {
    // Show all rows if search is empty
    $("#attendance-body tr").show();
    updateSortMessage(""); // Clear sort message when search is empty
  } else {
    // Filter rows based on first name and last name
    $("#attendance-body tr").hide().filter(function() {
      const lastName = $(this).find("td:eq(0)").text().toLowerCase();
      const firstName = $(this).find("td:eq(1)").text().toLowerCase();
      return lastName.includes(searchTerm) || firstName.includes(searchTerm);
    }).show();
  }
  
  // Update report to reflect filtered results
  createReport();
});

// Sort by Absences (Ascending)
$("#sortAbsencesAsc").click(function() {
  const rows = $("#attendance-body tr").get();
  
  rows.sort(function(a, b) {
    const absencesA = parseInt($(a).find("td:eq(14)").text()) || 0;
    const absencesB = parseInt($(b).find("td:eq(14)").text()) || 0;
    return absencesA - absencesB; // Ascending order
  });
  
  // Re-append sorted rows
  $.each(rows, function(index, row) {
    $("#attendance-body").append(row);
  });
  
  updateSortMessage("Currently sorted by absences (ascending)");
});

// Sort by Participation (Descending)
$("#sortParticipationDesc").click(function() {
  const rows = $("#attendance-body tr").get();
  
  rows.sort(function(a, b) {
    const participationA = parseInt($(a).find("td:eq(15)").text()) || 0;
    const participationB = parseInt($(b).find("td:eq(15)").text()) || 0;
    return participationB - participationA; // Descending order
  });
  
  // Re-append sorted rows
  $.each(rows, function(index, row) {
    $("#attendance-body").append(row);
  });
  
  updateSortMessage("Currently sorted by participation (descending)");
});

// Function to update sort message
function updateSortMessage(message) {
  $("#sortMessage").text(message);
}

// Notification function (helper)
function showNotification(message, type) {
  const colors = {
    success: "#4caf50",
    info: "#2196f3",
    warning: "#ff9800",
    error: "#f44336"
  };
  
  const notification = $("<div>")
    .css({
      position: "fixed",
      top: "20px",
      right: "20px",
      background: colors[type] || colors.info,
      color: "white",
      padding: "15px 25px",
      borderRadius: "8px",
      boxShadow: "0 4px 12px rgba(0,0,0,0.3)",
      zIndex: 9999,
      fontWeight: "bold",
      fontSize: "16px"
    })
    .text(message)
    .hide()
    .appendTo("body")
    .fadeIn(300);
  
  setTimeout(function() {
    notification.fadeOut(300, function() {
      $(this).remove();
    });
  }, 3000);
}

// Initial activation on page load
$(document).ready(function() {
  calculateStats();
  activateEditableCells();
  attachJQueryEvents();
  
  // Initialize with no sort message
  updateSortMessage("");
  
  console.log("✅ Enhanced Student Attendance System with search and sorting loaded successfully!");
  console.log("💡 New Features: Name search, Enhanced sorting buttons, Sort status messages");
});
