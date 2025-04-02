/**
 * Optimized Infinite Scroll for OpenGovAsia
 */
document.addEventListener("DOMContentLoaded", function () {
    // Globals
    let loading = false;
    let allPostsLoaded = false;
    const originalPostId = infiniteScrollData.post_id;
    const originalPostDate = infiniteScrollData.post_date;
    const loadedPostIds = [originalPostId];
    const postsPerLoad = infiniteScrollData.posts_per_load || 1;
    const postsContainer = document.getElementById("infinite-scroll-posts");
    const spinner = document.querySelector(".loading-spinner");
    const ajaxUrl = infiniteScrollData.ajax_url;
    const nonce = infiniteScrollData.nonce;
    const loadingText = infiniteScrollData.loading_text || "Loading Articles for you...";
    const noMoreText = infiniteScrollData.no_more_text || "No more articles";
    
    // Initialize status container
    const statusContainer = document.createElement("div");
    statusContainer.className = "infinite-scroll-status";
    statusContainer.style.textAlign = "center";
    statusContainer.style.padding = "20px";
    statusContainer.style.display = "none";
    
    if (postsContainer) {
        postsContainer.after(statusContainer);
    } else {
        return; // Exit if container is missing
    }
    
    // Load more posts function
    function loadMorePosts() {
        if (loading || allPostsLoaded) return;
        
        loading = true;
        if (spinner) spinner.style.display = "block";
        statusContainer.style.display = "block";
        statusContainer.textContent = loadingText;
        
        // Create FormData for security
        const formData = new FormData();
        formData.append('action', 'load_more_single_posts');
        formData.append('post_id', originalPostId);
        formData.append('posts_per_load', postsPerLoad);
        
        // Send all loaded post IDs to prevent duplicates
        loadedPostIds.forEach(id => {
            formData.append('loaded_post_ids[]', id);
        });
        
        formData.append('nonce', nonce);
        
        // Fetch with rate limiting and timeout protection
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 second timeout
        
        fetch(ajaxUrl, {
            method: "POST",
            body: formData,
            credentials: 'same-origin',
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data.content && data.data.content.trim() !== "") {
                // Add the new content
                postsContainer.insertAdjacentHTML("beforeend", data.data.content);
                
                // Update tracking info
                if (data.data.ids && data.data.ids.length > 0) {
                    data.data.ids.forEach(id => {
                        if (!loadedPostIds.includes(parseInt(id))) {
                            loadedPostIds.push(parseInt(id));
                        }
                    });
                }
                
                // Initialize scripts on new content
                initializeNewContent();
                
                // Update loading status
                allPostsLoaded = !data.data.has_more;
                if (allPostsLoaded) {
                    statusContainer.textContent = noMoreText;
                    fadeOutElement(statusContainer, 3000);
                } else {
                    statusContainer.style.display = "none";
                }
                
                // Update browser history with the new post URL (optional)
                const newPosts = document.querySelectorAll(".single-post-content:not([data-history-updated])");
                if (newPosts.length > 0) {
                    newPosts.forEach(post => {
                        post.setAttribute('data-history-updated', 'true');
                    });
                }
            } else {
                allPostsLoaded = true;
                statusContainer.textContent = noMoreText;
                fadeOutElement(statusContainer, 3000);
            }
        })
        .catch(error => {
            console.error("Error loading posts:", error.message);
            statusContainer.textContent = "Error loading posts. Please try again.";
            
            // Recover from error state after delay
            setTimeout(() => {
                loading = false;
            }, 5000);
        })
        .finally(() => {
            loading = false;
            if (spinner) spinner.style.display = "none";
        });
    }
    
    // Helper to fade out elements
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
    
    // Efficient scroll detection with throttling
    let scrollTimeout;
    const throttleDelay = 200; // ms between scroll checks
    
    function isUserAtEndOfPost() {
        const postContainers = document.querySelectorAll(".single-post-content");
        if (!postContainers.length) return false;
        
        const viewportHeight = window.innerHeight;
        const scrollPosition = window.scrollY;
        const buffer = 300; // px before the end to trigger loading
        
        // Check last post container
        const lastPost = postContainers[postContainers.length - 1];
        const postBottom = lastPost.offsetTop + lastPost.offsetHeight;
        
        return (scrollPosition + viewportHeight + buffer) >= postBottom;
    }
    
    // Initialize scripts for newly loaded content
    function initializeNewContent() {
        document.querySelectorAll(".single-post-content:not([data-initialized])").forEach(post => {
            post.setAttribute('data-initialized', 'true');
            
            // Add lazy loading to images in new content
            post.querySelectorAll('img:not([loading])').forEach(img => {
                img.setAttribute('loading', 'lazy');
            });
        });
        
        // Hook for theme developers
        if (typeof window.opengovasiaAfterLoad === 'function') {
            window.opengovasiaAfterLoad();
        }
    }
    
    // Use more efficient Intersection Observer API
    if ('IntersectionObserver' in window) {
        // Create observer trigger element
        const loadTrigger = document.createElement('div');
        loadTrigger.className = 'load-trigger';
        loadTrigger.style.height = '1px';
        loadTrigger.style.width = '100%';
        postsContainer.after(loadTrigger);
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !loading && !allPostsLoaded) {
                    loadMorePosts();
                }
            });
        }, {
            rootMargin: '300px 0px', // Load 300px before reaching the end
            threshold: 0.1
        });
        
        observer.observe(loadTrigger);
        
        // Update trigger position
        function updateTriggerPosition() {
            const lastPost = document.querySelector('.single-post-content:last-child');
            if (lastPost) {
                postsContainer.after(loadTrigger);
            }
        }
        
        // Set a less frequent interval to update trigger position
        setInterval(updateTriggerPosition, 2000);
    } else {
        // Fallback for older browsers
        window.addEventListener("scroll", function() {
            if (scrollTimeout) return;
            scrollTimeout = setTimeout(function() {
                if (isUserAtEndOfPost()) {
                    loadMorePosts();
                }
                scrollTimeout = null;
            }, throttleDelay);
        });
        
        // Also handle resize events
        window.addEventListener("resize", function() {
            if (scrollTimeout) clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function() {
                if (isUserAtEndOfPost()) {
                    loadMorePosts();
                }
                scrollTimeout = null;
            }, throttleDelay);
        });
    }
});