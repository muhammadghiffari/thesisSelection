// // Create a new file: resources/js/countdown.js
// document.addEventListener("livewire:initialized", () => {
//     initializeCountdowns();

//     // Listen for events to refresh countdowns
//     Livewire.on("refreshCountdowns", () => {
//         initializeCountdowns();
//     });
// });

// function initializeCountdowns() {
//     // Clear any existing intervals
//     document.querySelectorAll(".countdown-timer").forEach((timer) => {
//         const thesisId = timer.getAttribute("data-thesis-id");
//         if (window["countdownInterval_" + thesisId]) {
//             clearInterval(window["countdownInterval_" + thesisId]);
//         }

//         const expiresAt = parseInt(timer.getAttribute("data-expires-at"));
//         if (expiresAt) {
//             startCountdown(thesisId, expiresAt * 1000); // Convert to milliseconds
//         }
//     });
// }

// function startCountdown(thesisId, expiresAt) {
//     const countdownElement = document.getElementById("countdown-" + thesisId);
//     if (!countdownElement) return;

//     function updateCountdown() {
//         const now = new Date().getTime();
//         const timeLeft = expiresAt - now;

//         if (timeLeft <= 0) {
//             clearInterval(window["countdownInterval_" + thesisId]);
//             countdownElement.textContent = "00:00";

//             // Notify Livewire that the countdown has ended
//             Livewire.dispatch("countdownEnded", thesisId);
//             return;
//         }

//         const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
//         const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

//         countdownElement.textContent = `${minutes
//             .toString()
//             .padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;
//     }

//     // Update immediately and then every second
//     updateCountdown();
//     window["countdownInterval_" + thesisId] = setInterval(
//         updateCountdown,
//         1000
//     );
// }

// // Add a floating timer for step 3
// function initializeFloatingTimer() {
//     const floatingTimer = document.getElementById("floating-timer");
//     if (!floatingTimer) return;

//     const expiresAt = parseInt(floatingTimer.getAttribute("data-expires-at"));
//     if (!expiresAt) return;

//     startFloatingCountdown(expiresAt * 1000); // Convert to milliseconds
// }

// function startFloatingCountdown(expiresAt) {
//     const timerElement = document.getElementById("floating-timer-countdown");
//     if (!timerElement) return;

//     function updateFloatingTimer() {
//         const now = new Date().getTime();
//         const timeLeft = expiresAt - now;

//         if (timeLeft <= 0) {
//             clearInterval(window["floatingTimerInterval"]);
//             timerElement.textContent = "00:00";

//             // Notify Livewire that the floating timer has ended
//             Livewire.dispatch("floatingTimerEnded");
//             return;
//         }

//         const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
//         const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

//         timerElement.textContent = `${minutes
//             .toString()
//             .padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;
//     }

//     // Update immediately and then every second
//     updateFloatingTimer();
//     window["floatingTimerInterval"] = setInterval(updateFloatingTimer, 1000);
// }

// // Additional function to initialize the floating timer when step 3 is loaded
// document.addEventListener("livewire:navigated", () => {
//     if (document.getElementById("floating-timer")) {
//         initializeFloatingTimer();
//     }
// });
