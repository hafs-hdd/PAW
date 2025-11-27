// Student Attendance System - Complete JS

function calculateStats() {
  const rows = document.querySelectorAll("#attendance-body tr");
  rows.forEach(row => {
    const tds = row.querySelectorAll("td");
    if (tds.length < 18) return;

    let abs = 0, par = 0;
    for (let i = 3; i < 15; i++) {
      const v = tds[i].textContent.trim();
      if (i % 2 === 1) {
        if (v !== "✓") abs++;
      } else {
        if (v === "✓") par++;
      }
    }

    tds[15].textContent = abs;
    tds[16].textContent = par;

    let msg = "";
    if (abs >= 5) msg = "Excluded – too many absences";
    else if (abs >= 3) msg = "Warning – attendance low";
    else if (abs <= 1 && par >= 3) msg = "Good attendance – Excellent participation";
    else msg = "Good attendance – Keep going";

    tds[17].textContent = msg;

    if (!row.classList.contains("excellent-highlight")) {
      row.classList.remove("red-row","yellow-row","green-row");
      if (abs >= 4) row.classList.add("red-row");
      else if (abs >= 2) row.classList.add("yellow-row");
      else row.classList.add("green-row");
    }
  });
}

function activateEditableCells() {
  const rows = document.querySelectorAll("#attendance-body tr");
  rows.forEach(row => {
    const tds = row.querySelectorAll("td");
    for (let i = 3; i < 15; i++) {
      if (!tds[i]) continue;
      tds[i].style.cursor = "pointer";
      tds[i].onclick = function (e) {
        e.stopPropagation();
        this.textContent = this.textContent.trim() === "✓" ? "" : "✓";
        calculateStats();
      };
    }
  });
}

function createReport() {
  const allRows = Array.from(document.querySelectorAll("#attendance-body tr"));
  const total = allRows.length;
  let presentCount = 0, participationCount = 0;

  allRows.forEach(r => {
    const tds = r.querySelectorAll("td");
    let hasP = false, hasPa = false;
    
    for (let i = 3; i < 15; i += 2) {
      if (tds[i].textContent.trim() === "✓") hasP = true;
    }
    
    for (let i = 4; i < 15; i += 2) {
      if (tds[i].textContent.trim() === "✓") hasPa = true;
    }
    
    if (hasP) presentCount++;
    if (hasPa) participationCount++;
  });

  document.getElementById("totalStudents").textContent = "Total Students: " + total;
  document.getElementById("presentCount").textContent = "Present (students with ≥1 P): " + presentCount;
  document.getElementById("participationCount").textContent = "Participated (students with ≥1 Pa): " + participationCount;

  const maxH = 140;
  const totalH = total === 0 ? 0 : maxH;
  const presentH = total === 0 ? 0 : Math.round((presentCount / total) * maxH);
  const partH = total === 0 ? 0 : Math.round((participationCount / total) * maxH);

  document.getElementById("barTotal").style.height = totalH + "px";
  document.getElementById("barPresent").style.height = presentH + "px";
  document.getElementById("barParticipation").style.height = partH + "px";
}

// Event Listeners
document.getElementById("showReportBtn").addEventListener("click", function () {
  createReport();
  document.getElementById("reportArea").classList.remove("hidden");
  document.getElementById("reportArea").scrollIntoView({ behavior: "smooth" });
});

document.getElementById("highlightExcellentBtn").addEventListener("click", function() {
  const rows = document.querySelectorAll("#attendance-body tr");
  rows.forEach(row => {
    const absenceText = row.querySelector("td:nth-child(16)").textContent.trim();
    const absenceCount = parseInt(absenceText) || 0;
    
    if (absenceCount < 3) {
      row.classList.add("excellent-highlight");
    }
  });
  showNotification("Excellent students highlighted!", "success");
});

document.getElementById("resetColorsBtn").addEventListener("click", function() {
  const rows = document.querySelectorAll("#attendance-body tr");
  rows.forEach(row => {
    row.classList.remove("excellent-highlight");
  });
  calculateStats();
  showNotification("Colors reset successfully!", "info");
});

document.getElementById("nameSearch").addEventListener("input", function() {
  const searchTerm = this.value.toLowerCase().trim();
  const rows = document.querySelectorAll("#attendance-body tr");
  
  rows.forEach(row => {
    const lastName = row.querySelector("td:nth-child(1)").textContent.toLowerCase();
    const firstName = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
    row.style.display = (lastName.includes(searchTerm) || firstName.includes(searchTerm)) ? "" : "none";
  });
  createReport();
});

document.getElementById("sortAbsencesAsc").addEventListener("click", function() {
  const tbody = document.getElementById("attendance-body");
  const rows = Array.from(tbody.querySelectorAll("tr"));
  
  rows.sort((a, b) => {
    const absencesA = parseInt(a.querySelector("td:nth-child(16)").textContent) || 0;
    const absencesB = parseInt(b.querySelector("td:nth-child(16)").textContent) || 0;
    return absencesA - absencesB;
  });
  
  rows.forEach(row => tbody.appendChild(row));
  updateSortMessage("Sorted by absences (ascending)");
});

document.getElementById("sortParticipationDesc").addEventListener("click", function() {
  const tbody = document.getElementById("attendance-body");
  const rows = Array.from(tbody.querySelectorAll("tr"));
  
  rows.sort((a, b) => {
    const participationA = parseInt(a.querySelector("td:nth-child(17)").textContent) || 0;
    const participationB = parseInt(b.querySelector("td:nth-child(17)").textContent) || 0;
    return participationB - participationA;
  });
  
  rows.forEach(row => tbody.appendChild(row));
  updateSortMessage("Sorted by participation (descending)");
});

// jQuery Events
$(document).ready(function() {
  $("#attendance-body tr").hover(
    function() { $(this).addClass("hover-highlight"); },
    function() { $(this).removeClass("hover-highlight"); }
  );

  $("#attendance-body tr").on('click', function() {
    const firstName = $(this).find("td:eq(1)").text().trim();
    const lastName = $(this).find("td:eq(0)").text().trim();
    const group = $(this).find("td:eq(2)").text().trim();
    const absences = $(this).find("td:eq(15)").text().trim();
    
    $("#modalContent").html(`<strong>Name:</strong> ${firstName} ${lastName}<br>
                           <strong>Group:</strong> ${group}<br>
                           <strong>Absences:</strong> ${absences}`);
    $("#modalOverlay").fadeIn(300);
  });

  $("#closeModal, #modalOverlay").click(function(e) {
    if (e.target === this) $("#modalOverlay").fadeOut(300);
  });

  // Form validation
  $("#add-student-form").on("submit", function(e) {
    let isValid = true;
    $(".error-message").hide();

    if (!$("#studentId").val().trim()) {
      $("#studentIdError").text("Student ID required").show();
      isValid = false;
    }
    if (!$("#lastName").val().trim()) {
      $("#lastNameError").text("Last name required").show();
      isValid = false;
    }
    if (!$("#firstName").val().trim()) {
      $("#firstNameError").text("First name required").show();
      isValid = false;
    }
    if (!$("#group").val().trim()) {
      $("#groupError").text("Group required").show();
      isValid = false;
    }

    if (!isValid) e.preventDefault();
  });

  $("#studentId, #lastName, #firstName, #group").on("input", function() {
    $(this).siblings(".error-message").hide();
  });

  // Initialize
  calculateStats();
  activateEditableCells();
  updateSortMessage("");
});

function updateSortMessage(message) {
  document.getElementById("sortMessage").textContent = message;
}

function showNotification(message, type) {
  const notification = $("<div>").css({
    position: "fixed", top: "20px", right: "20px",
    background: type === "success" ? "#4caf50" : "#2196f3",
    color: "white", padding: "15px 25px", borderRadius: "8px",
    zIndex: 9999, fontWeight: "bold"
  }).text(message).hide().appendTo("body").fadeIn(300);
  
  setTimeout(() => notification.fadeOut(300, () => notification.remove()), 3000);
}