let ENABLE_PAGE_PRELOADER=!1,DEFAULT_DARK_MODE=!1,USE_LOCAL_STORAGE=!0,USE_SYSTEM_PREFERENCES=!1,DEFAULT_BREAKPOINTS={xs:0,sm:576,md:768,lg:992,xl:1200,xxl:1400};document.addEventListener("DOMContentLoaded",(()=>{html.classList.add("dom-ready")}));const updateScrollWidth=()=>document.documentElement.style.setProperty("--body-scroll-width",window.innerWidth-document.documentElement.clientWidth+"px");window.addEventListener("resize",updateScrollWidth),updateScrollWidth();const html=document.documentElement,setupBp=(e,t,d="min")=>{const n=matchMedia(`(${d}-width: ${t}px)`),o=`bp-${e}${"max"===d?"-max":""}`,c=()=>html.classList.toggle(o,n.matches);n.onchange=c,c()};Object.entries(DEFAULT_BREAKPOINTS).forEach((([e,t])=>{setupBp(e,t,"min"),setupBp(e,t-1,"max")}));const isDarkMode=()=>html.classList.contains("uc-dark"),setDarkMode=e=>{e=!!e,isDarkMode()!==e&&(html.classList.toggle("uc-dark",e),window.dispatchEvent(new CustomEvent("darkmodechange")))},getInitialDarkMode=()=>USE_LOCAL_STORAGE&&null!==localStorage.getItem("darkMode")?"1"===localStorage.getItem("darkMode"):USE_SYSTEM_PREFERENCES?matchMedia("(prefers-color-scheme: dark)").matches:DEFAULT_DARK_MODE;setDarkMode(getInitialDarkMode());const dark=new URLSearchParams(location.search).get("dark");dark&&html.classList.toggle("uc-dark","1"===dark),document.addEventListener("DOMContentLoaded",(function(){})),document.addEventListener("DOMContentLoaded",(function(){const e=document.getElementById("uc-gdpr-notification");localStorage.getItem("gdprAccepted")||setTimeout((function(){e.classList.add("show")}),5e3),document.getElementById("uc-accept-gdpr").addEventListener("click",(function(){e.classList.remove("show"),localStorage.setItem("gdprAccepted","true")})),document.getElementById("uc-close-gdpr-notification").addEventListener("click",(function(){e.classList.remove("show")}))}));


function sharePage() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            text: "Check out this amazing post!",
            url: window.location.href
        }).catch((error) => console.error('Error sharing:', error));
    } else {
        alert("Web Share API is not supported on this browser.");
    }
}