<?php
/*
Template Name: RSS
*/

    /**
     * Get the posts metadata
     * @var Array
     */
    $hbgMeta = get_post_meta($post->ID, '_helsingborg_meta', true);
    $seoMeta = get_post_meta($post->ID, '_aioseop_description', true);

    /**
     * Get child pages based on the rss_select_id in metadata
     */
    $args = array(
        'post_type'     => 'page',
        'post_status'   => 'publish',
        'post_parent'   => $hbgMeta['rss_select_id']
    );

    $pages = get_children($args);
    $numberOfPages = count($pages);
    $lastPage = $numberOfPages - 1;

    /**
     * Formats timestamp to RSS format
     * @param  String $timestamp Unformatted timestamp
     * @return String            Formatted timestamp
     */
    function helsingborg_rss_date($timestamp = null) {
        $timestamp = ($timestamp == null) ? time() : strtotime($timestamp);
        return date(DATE_RSS, $timestamp);
    }

    function helsingborg_rss_text_limit($string, $length, $replacer = '...') {
        $string = strip_tags($string);
        if(strlen($string) > $length) {
            return (preg_match('/^(.*)\W.*$/', substr($string, 0, $length+1), $matches) ? $matches[1] : substr($string, 0, $length)) . $replacer;
        } else {
            return $string;
        }
    }

    header('Content-Type: application/rss+xml; charset=utf-8;');
?>
<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Title</title>
        <link>http://www.helsingborg.se/</link>
        <description><![CDATA[<?php echo $seoMeta; ?>]]></description>
        <language>sv-se</language>
        <lastBuildDate><?php echo helsingborg_rss_date($pages[$lastPage]->post_modified_gmt); ?></lastBuildDate>
        <pubDate><?php echo helsingborg_rss_date($pages[$lastPage]->post_modified_gmt); ?></pubDate>
        <docs>http://www.rssboard.org/rss-specification</docs>
        <image>
            <url><?php echo get_stylesheet_directory_uri(); ?>/assets/img/images/hbg-logo-rss.jpg</url>
            <title><![CDATA[Helsingborg Stad]]></title>
            <link>http://www.helsingborg.se</link>
        </image>

        <atom:link href="http://<?php echo $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI]; ?>" rel="self" type="application/rss+xml" />

        <?php foreach($pages as $post) : ?>
        <item>
            <link><?php echo get_the_permalink($post->ID); ?></link>
            <guid isPermaLink="true"><?php echo get_the_permalink($post->ID); ?></guid>
            <title><![CDATA[<?php echo get_the_title($post->ID); ?>]]></title>
            <pubDate><?php echo helsingborg_rss_date($post->post_modified_gmt); ?></pubDate>
            <description><![CDATA[<?php echo $post->post_content; ?>]]></description>
        </item>
        <?php endforeach; ?>
    </channel>
</rss>