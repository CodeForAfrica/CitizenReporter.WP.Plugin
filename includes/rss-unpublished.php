<?php
/**
 * Created by PhpStorm.
 * User: Ahereza
 * Date: 5/9/17
 * Time: 10:48
 */

$args = array(
    'post_type'        => 'post',
    'post_status'      => 'draft',
);

$posts = query_posts($args);

// Setting up content type and charset headers
header('Content-Type: '.feed_content_type('rss-http').';charset='.get_option('blog_charset'), true);

// Setting up valid XML encoding
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>

<!-- Declaring XML Validators namespaces -->
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    <?php do_action('rss2_ns'); ?>>
    <!-- Declaring channel with articles data -->
    <?php $dateTimeFormat = 'D, M d Y H:i:s'; ?>
    <channel>
        <title><?php bloginfo_rss('name'); ?> - Feed</title>
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date($dateTimeFormat, get_lastpostmodified(), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'daily' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>
        <?php while (have_posts()) : the_post(); ?>
            <item>
                <title><?php the_title_rss() ?></title>
                <link><?php the_permalink_rss() ?></link>
                <?php if ( get_comments_number() || comments_open() ) : ?>
                    <comments><?php comments_link_feed(); ?></comments>
                <?php endif; ?>
                <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                <dc:creator><![CDATA[<?php the_author() ?>]]></dc:creator>
                <?php the_category_rss('rss2') ?>

                <guid isPermaLink="false"><?php the_guid(); ?></guid>
                <?php if (get_option('rss_use_excerpt')) : ?>
                    <description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
                <?php else : ?>
                    <description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
                    <?php $content = get_the_content_feed('rss2'); ?>
                    <?php if ( strlen( $content ) > 0 ) : ?>
                        <content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>
                    <?php else : ?>
                        <content:encoded><![CDATA[<?php the_excerpt_rss(); ?>]]></content:encoded>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ( get_comments_number() || comments_open() ) : ?>
                    <wfw:commentRss><?php echo esc_url( get_post_comments_feed_link(null, 'rss2') ); ?></wfw:commentRss>
                    <slash:comments><?php echo get_comments_number(); ?></slash:comments>
                <?php endif; ?>
                <?php rss_enclosure(); ?>
                <?php
                /**
                 * Fires at the end of each RSS2 feed item.
                 *
                 * @since 2.0.0
                 */
                do_action( 'rss2_item' );
                ?>
            </item>
        <?php endwhile; ?>
    </channel>
</rss>