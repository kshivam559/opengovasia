let ENABLE_PAGE_PRELOADER = !1,
  DEFAULT_DARK_MODE = !1,
  USE_LOCAL_STORAGE = !0,
  USE_SYSTEM_PREFERENCES = !1,
  DEFAULT_BREAKPOINTS = {
    xs: 0,
    sm: 576,
    md: 768,
    lg: 992,
    xl: 1200,
    xxl: 1400,
  };

// body scroll width
const updateScrollWidth = () =>
  document.documentElement.style.setProperty(
    "--body-scroll-width",
    `${window.innerWidth - document.documentElement.clientWidth}px`
  );
window.addEventListener("resize", updateScrollWidth);
updateScrollWidth();

// default breakpoints classes
const html = document.documentElement,
  setupBp = (bp, bpSize, type = "min") => {
    const media = matchMedia(`(${type}-width: ${bpSize}px)`),
      cls = `bp-${bp}${type === "max" ? "-max" : ""}`,
      update = () => html.classList.toggle(cls, media.matches);
    media.onchange = update;
    update();
  };
Object.entries(DEFAULT_BREAKPOINTS).forEach(([bp, bpSize]) => {
  setupBp(bp, bpSize, "min");
  setupBp(bp, bpSize - 1, "max");
});

// auto darkmode feature
const isDarkMode = () => html.classList.contains("uc-dark"),
  setDarkMode = (enableDark) => {
    enableDark = !!enableDark;
    if (isDarkMode() === enableDark) return;
    html.classList.toggle("uc-dark", enableDark);
    window.dispatchEvent(new CustomEvent("darkmodechange"));
  },
  getInitialDarkMode = () =>
    USE_LOCAL_STORAGE && localStorage.getItem("darkMode") !== null
      ? localStorage.getItem("darkMode") === "1"
      : USE_SYSTEM_PREFERENCES
      ? matchMedia("(prefers-color-scheme: dark)").matches
      : DEFAULT_DARK_MODE;
setDarkMode(getInitialDarkMode());

document.addEventListener("DOMContentLoaded", function () {
  // add dom-ready class
  html.classList.add("dom-ready");
  console.log(
    "\n %c OpenGov Asia v1.0.0 %c https://opengovasia.com/ \n",
    "color: #fff; background: #0c50a8; padding:5px 0;",
    "color: #0c50a8; padding:5px 0;"
  );

  // GPDR popup

  const gdprNotification = document.getElementById("uc-gdpr-notification");
  const gdprAccepted = localStorage.getItem("gdprAccepted");

  // Show the GDPR notification if it has not been accepted
  if (!gdprAccepted) {
    setTimeout(function () {
      gdprNotification.classList.add("show");
    }, 5000); // 5000 milliseconds = 5 seconds
  }

  // Set event listener for the accept button
  document
    .getElementById("uc-accept-gdpr")
    .addEventListener("click", function () {
      gdprNotification.classList.remove("show");
      // Set the localStorage item to indicate GDPR has been accepted
      localStorage.setItem("gdprAccepted", "true");
    });

  // Set event listener for the close button
  document
    .getElementById("uc-close-gdpr-notification")
    .addEventListener("click", function () {
      gdprNotification.classList.remove("show");
    });
});

function sharePage() {
  if (navigator.share) {
    navigator
      .share({
        title: document.title,
        text: "Check out this amazing content:",
        url: window.location.href,
      })
      .catch((error) => console.error("Error sharing:", error));
  } else {
    alert("Web Share API is not supported on this browser.");
  }
}
if ("serviceWorker" in navigator && "PushManager" in window) {
  window.addEventListener("load", async function () {
    try {
      const registration = await navigator.serviceWorker.register(
        "/wp-content/themes/opengovasia/sw.js"
      );
      window.swRegistration = registration;
      const permission = Notification.permission;
      if (permission === "granted") {
        console.log("Notification permission already granted");
      }
    } catch (error) {
      console.error("Service Worker registration failed:", error);
    }
  });
}
async function subscribeToPushNotifications() {
  try {
    const permission = await Notification.requestPermission();
    if (permission !== "granted") {
      throw new Error("Notification permission denied");
    }
    const registration = window.swRegistration;
    let subscription = await registration.pushManager.getSubscription();
    if (!subscription) {
      const vapidPublicKey = "YOUR_PUBLIC_VAPID_KEY";
      const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);
      subscription = await registration.pushManager.subscribe({
        userVisibleOnly: !0,
        applicationServerKey: convertedVapidKey,
      });
      await sendSubscriptionToServer(subscription);
    }
    return subscription;
  } catch (error) {
    console.error("Error subscribing to push notifications:", error);
  }
}
window.addEventListener("beforeinstallprompt", (e) => {
  const t = document.getElementById("add-to-home"),
    n = document.getElementById("nav-add-to-home");
  if (!t || !n) return;
  n.classList.toggle("hide", !1),
    t.classList.toggle("hide", !1),
    (t.onclick = () => e.prompt()),
    (n.onclick = () => e.prompt());
});

// Newsletter Popup
document.addEventListener("DOMContentLoaded", function () {
  setTimeout(function () {
    // UniCore.modal("#opengov-country").show();
    // Set the localStorage item to indicate the modal has been shown
  }, 2000); // 10000 milliseconds = 10 seconds
});
