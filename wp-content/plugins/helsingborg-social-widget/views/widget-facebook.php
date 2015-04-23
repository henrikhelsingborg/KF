<!-- Facebook feed -->
<?php echo $before_widget; ?>
<h2><i class="fa fa-facebook-square"></i> Facebook</h2>
<div class="divider">
    <div class="upper-divider"></div>
    <div class="lower-divider"></div>
</div>
<div class="textwidget hbg-social-feed hbg-social-feed-facebook">
    <ul>
        <?php
            $int = 0;
            foreach ($feed as $post) :

            $date = new DateTime($post->created_time);
            $timeZone = new DateTimeZone('Europe/Stockholm');
            $date->setTimezone($timeZone);
        ?>
        <li>
            <?php
                if (isset($post->full_picture)) :
            ?>
                <div class="hbg-social-feed-facebook-image" style="background-image: url(<?php echo $post->full_picture; ?>);"></div>
            <?php endif; ?>
            <div class="hbg-social-feed-facebook-post-content">
                <span class="hbg-social-feed-facebook-post-date"><?php echo $date->format('Y-m-d H:i'); ?></span>
                <article>
                    <?php echo wp_trim_words($post->message, 30, $more = '…<br><a href="' . $post->link . '" target="_blank" class="read-more">Läs mer</a>'); ?>
                </article>
                <?php if ($post->status_type == 'shared_story') : ?>
                <a href="<?php echo $post->link; ?>" target="_blank" class="hbg-social-feed-facebook-post-attachment">
                    <span class="hbg-social-feed-facebook-post-attachment-title"><?php echo $post->name; ?></span>
                    <span class="hbg-social-feed-facebook-post-attachment-description"><?php echo wp_trim_words($post->description, 10, $more = '…'); ?></span>
                    <span class="hbg-social-feed-facebook-post-attachment-caption"><?php echo $post->caption; ?></span>
                </a>
                <?php endif; ?>
            </div>
            <div class="clearfix"></div>
        </li>
        <?php $int++; if ($int == $instance['show_count']) break; endforeach; ?>
    </ul>
    <div class="clearfix"></div>

    <?php if (isset($instance['show_visit_button']) && $instance['show_visit_button'] == 'on') : ?>
    <div class="text-center hbg-social-feed-actions">
        <a href="https://www.facebook.com/<?php echo $instance['username']; ?>" target="_blank" class="button button-hbg">Besök oss på Facebook</a>
    </div>
    <?php endif; ?>
</div>
<?php echo $after_widget; ?>