<?php

/*
* Archive Page of Channels (Category)
*
* @package OpenGovAsia
*/

get_header();

?>

<?php opengovasia_breadcrumbs(); ?>

<div class="section py-3 sm:py-6 lg:py-9">
	<div class="container max-w-xl">
		<div class="panel vstack gap-3 sm:gap-6 lg:gap-6">


            <header class="page-header panel vstack text-center">
            <h1 class="h3 lg:h1"><?php echo single_cat_title('', false); ?></h1>

            <?php
    // Get all categories
    $categories = get_categories(array(
        'orderby' => 'name',
        'order'   => 'ASC',
        'hide_empty' => true // Only show categories that have posts
    ));
    
    // Loop through each category
    foreach ($categories as $category) {
        // Get posts for this category (7 posts - 1 for hero, 6 for regular display)
        $posts = get_posts(array(
            'posts_per_page' => 7,
            'category'       => $category->term_id,
            'post_type'      => array('post', 'events', 'ogtv'), // Include your custom post types
            'orderby'        => 'date',
            'order'          => 'DESC'
        ));
        
        // Only display the category section if it has posts
        if (count($posts) > 0) :
    ?>
        <section class="category-section" id="category-<?php echo $category->slug; ?>">
            <h2 class="category-title"><?php echo $category->name; ?></h2>
            
            <?php if (!empty($posts)) : ?>
                <!-- Hero Section -->
                <div class="hero-section">
                    <?php
                    // Use the first post as the hero
                    $hero_post = $posts[0];
                    setup_postdata($GLOBALS['post'] =& $hero_post);
                    ?>
                    <article class="hero-post">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large'); ?>
                            <?php endif; ?>
                            
                            <div class="hero-content">
                                <h3 class="hero-title"><?php the_title(); ?></h3>
                                <div class="hero-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                <span class="read-more">Read More</span>
                            </div>
                        </a>
                    </article>
                    <?php wp_reset_postdata(); ?>
                </div>
                
                <!-- Regular Posts Grid -->
                <div class="posts-grid">
                    <?php
                    // Skip the first post (already used as hero) and display the next 6
                    $count = 0;
                    for ($i = 1; $i < count($posts) && $count < 6; $i++) {
                        $post = $posts[$i];
                        setup_postdata($GLOBALS['post'] =& $post);
                        $count++;
                    ?>
                        <article class="grid-post">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="grid-thumbnail">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="grid-content">
                                    <h4 class="grid-title"><?php the_title(); ?></h4>
                                    <div class="grid-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php
                    }
                    wp_reset_postdata();
                    ?>
                </div>
                
                <!-- View All Link -->
                <div class="view-all-container">
                    <a href="<?php echo get_category_link($category->term_id); ?>" class="view-all-link">
                        View all <?php echo $category->name; ?> articles
                    </a>
                </div>
            <?php endif; ?>
        </section>
    <?php
        endif; // End if count($posts) > 0
    } // End foreach
    ?>

        
        </div>
    </div>
</div>

<?php
get_footer();