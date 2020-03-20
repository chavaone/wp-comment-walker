<?php
/**
 *  Custom comment walker for HTML5 friendly WordPress comment and threaded replies. To be inserted in functions.php.
 *  @link https://gist.github.com/georgiecel/9445357
 */

	class AQD_Walker_Comment extends Walker_Comment {

    private $parent_comment;

    function start_lvl( &$output, $depth = 0, $args = array() ) {}
    function end_lvl( &$output, $depth = 0, $args = array() ) {}

    function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
      $depth++;
      $GLOBALS['comment_depth'] = $depth;
      $GLOBALS['comment']       = $comment;

      if ( ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) && $args['short_ping'] ):
        $this->pingback( $comment, $depth, $args );
      else:
        $this->comment( $comment, $depth, $args );
      endif;
    }

    function end_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {}

    protected function comment( $comment, $depth, $args ) {
      ?>
      <article <?php comment_class("comment") ?> id="comment-<?php comment_ID() ?>" itemprop="comment" itemscope itemtype="http://schema.org/Comment">
      <section class="comment__meta commentmeta">
        <figure class="commentmeta__avatar">
          <?php echo get_avatar( $comment, 65 ); ?>
        </figure>
        <div class="commentmeta__info">
          <?php
            $parent = $comment->comment_parent;
            //Not showing "in reply to" if it's not a reply.
            if ($parent):
              echo '<p class="commentmeta__replyto">';
              echo __('In reply to ', 'rge-theme') . '<a href="#comment-' . $parent . '">#' . $parent . '</a>' . ' <i class="fas fa-reply"></i>';
              echo '</p>';
            endif;
          ?>
          <p class="commentmeta__author">
            <?php
            echo $comment->comment_author;
            if ($comment->comment_author_url):
              echo " (<a class='commentmeta__authorlink' href='". $comment->comment_author_url ."'>" . $comment->comment_author_url . "</a>)";
            endif;
            ?>
          </p>
          <p class="commentmeta__time">
            <?php
                /* translators: 1: comment date, 2: comment time */
                printf( __( '%1$s at %2$s' ), get_comment_date( '', $comment ), get_comment_time() );
            ?>
          </p>
          <?php if ($comment->comment_approved == '0') : ?>
					<p class="commentmeta__moderation"><?php _e('Your comment is awaiting moderation.', 'rge-theme');?></p>
					<?php endif; ?>
        </div>
      </section>
      <section class="comment__text">
        <?php
        comment_text(
            $comment,
            array_merge(
                $args,
                array(
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                )
            )
        );
        ?>
      </section>
      <section class="comment__tools btn-group">
        <?php
        comment_reply_link(
            array_merge(
                $args,
                array(
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'before' => '<button class="btn btn-issue">',
                    'after' => '</button>'
                )
            )
        );

        $edit_link = get_edit_comment_link();
        if ($edit_link) :
          echo '<a class="btn btn-issue" href="' . $edit_link . '">' . __('Edit', 'rge-theme') . "</a>";
        endif;
        ?>
      </section>
      </article> <!-- END comment-<?php comment_ID() ?> -->
      <?php
    }

    protected function pingback( $comment, $depth, $args ) {

    }
  }
