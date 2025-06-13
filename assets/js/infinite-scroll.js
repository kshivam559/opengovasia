/**
 * Infinite Scroll for OpenGovAsia
 * Version: 1.0
 * Author: Shivam Kumar
 * Author URI: https://www.github.com/kshivam559/
 *
 * Text Domain: opengovasia
 *
 * Description: This script implements infinite scrolling for single post pages
 * in the OpenGovAsia theme. It loads more posts as the user scrolls down,
 * providing a seamless experience. The script is designed to be efficient
 * and user-friendly, ensuring that it does not interfere with the existing
 * functionality of the theme. It also includes lazy loading for images.
 *
 * It uses the Intersection Observer API for efficient scroll detection
 *
 */

document.addEventListener("DOMContentLoaded", function () {
  let loading = false;
  let allPostsLoaded = false;
  const originalPostId = infiniteScrollData.post_id;
  const loadedPostIds = [originalPostId];
  const postsPerLoad = infiniteScrollData.posts_per_load || 1;
  const country = infiniteScrollData.country || '';
  const postsContainer = document.getElementById("infinite-scroll-posts");
  const spinner = document.querySelector(".loading-spinner");
  const ajaxUrl = infiniteScrollData.ajax_url;
  const nonce = infiniteScrollData.nonce;
  const loadingText = infiniteScrollData.loading_text || "Loading Articles for you...";
  const noMoreText = infiniteScrollData.no_more_text || "No more articles";

  // Initialize status container
  const statusContainer = document.createElement("div");
  statusContainer.className = "infinite-scroll-status";
  statusContainer.style.cssText = "text-align: center; padding: 20px; display: none;";

  if (postsContainer) {
    postsContainer.after(statusContainer);
  } else {
    return;
  }

  function loadMorePosts() {
    if (loading || allPostsLoaded) return;

    loading = true;
    if (spinner) spinner.style.display = "block";
    statusContainer.style.display = "block";
    statusContainer.textContent = loadingText;

    const formData = new FormData();
    formData.append("action", "load_more_single_posts");
    formData.append("post_id", originalPostId);
    formData.append("posts_per_load", postsPerLoad);
    formData.append("country", country);
    formData.append("nonce", nonce);

    loadedPostIds.forEach((id) => {
      formData.append("loaded_post_ids[]", id);
    });

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 15000);

    fetch(ajaxUrl, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
      signal: controller.signal,
    })
      .then((response) => {
        clearTimeout(timeoutId);
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
      })
      .then((data) => {
        if (data.success && data.data.content && data.data.content.trim() !== "") {
          postsContainer.insertAdjacentHTML("beforeend", data.data.content);

          if (data.data.ids && data.data.ids.length > 0) {
            data.data.ids.forEach((id) => {
              if (!loadedPostIds.includes(parseInt(id))) {
                loadedPostIds.push(parseInt(id));
              }
            });
          }

          initializeNewContent();
          allPostsLoaded = !data.data.has_more;
          
          if (allPostsLoaded) {
            statusContainer.textContent = noMoreText;
            fadeOutElement(statusContainer, 3000);
          } else {
            statusContainer.style.display = "none";
          }
        } else {
          allPostsLoaded = true;
          statusContainer.textContent = noMoreText;
          fadeOutElement(statusContainer, 3000);
        }
      })
      .catch((error) => {
        console.error("Error loading posts:", error.message);
        statusContainer.textContent = "Error loading posts. Please try again.";
        setTimeout(() => { loading = false; }, 5000);
      })
      .finally(() => {
        loading = false;
        if (spinner) spinner.style.display = "none";
      });
  }

  function fadeOutElement(element, delay) {
    setTimeout(() => {
      element.style.transition = "opacity 0.5s ease";
      element.style.opacity = "0";
      setTimeout(() => {
        element.style.display = "none";
        element.style.opacity = "1";
      }, 500);
    }, delay);
  }

  function initializeNewContent() {
    document.querySelectorAll(".single-post-content:not([data-initialized])").forEach((post) => {
      post.setAttribute("data-initialized", "true");
      post.querySelectorAll("img:not([loading])").forEach((img) => {
        img.setAttribute("loading", "lazy");
      });
    });

    if (typeof window.opengovasiaAfterLoad === "function") {
      window.opengovasiaAfterLoad();
    }
  }

  // Use Intersection Observer for efficient scroll detection
  if ("IntersectionObserver" in window) {
    const loadTrigger = document.createElement("div");
    loadTrigger.className = "load-trigger";
    loadTrigger.style.cssText = "height: 1px; width: 100%;";
    postsContainer.after(loadTrigger);

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !loading && !allPostsLoaded) {
            loadMorePosts();
          }
        });
      },
      { rootMargin: "300px 0px", threshold: 0.1 }
    );

    observer.observe(loadTrigger);

    // Update trigger position periodically
    setInterval(() => {
      const lastPost = document.querySelector(".single-post-content:last-child");
      if (lastPost) postsContainer.after(loadTrigger);
    }, 2000);
  } else {
    // Fallback for older browsers
    let scrollTimeout;
    const throttleDelay = 200;

    function checkScroll() {
      const postContainers = document.querySelectorAll(".single-post-content");
      if (!postContainers.length) return false;

      const lastPost = postContainers[postContainers.length - 1];
      const postBottom = lastPost.offsetTop + lastPost.offsetHeight;
      const buffer = 300;

      return window.scrollY + window.innerHeight + buffer >= postBottom;
    }

    function handleScroll() {
      if (scrollTimeout) return;
      scrollTimeout = setTimeout(() => {
        if (checkScroll()) loadMorePosts();
        scrollTimeout = null;
      }, throttleDelay);
    }

    window.addEventListener("scroll", handleScroll);
    window.addEventListener("resize", handleScroll);
  }
});