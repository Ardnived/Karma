<?php
global $post;

$startdate = Karma_Events::get_startdate();
$enddate = Karma_Events::get_enddate();

$class = strtolower( date( "M", $startdate ) );
$class .= ( has_post_thumbnail() ? " img" : "" );
$class .= ( Karma_Events::$hero_slug == $post->post_name ? " hero" : "" );

$subscribed = Karma_Subscriber::is_subscribed( $post->ID );
$subscriber_count = Karma_Subscriber::get_subscriber_count( $post->ID );
?>
<article id="<?php echo $post->post_name; ?>" class="event event-<?php the_ID(); ?> <?php echo $class; ?>" data-permalink="<?php the_permalink(); ?>">
	<header>
		<?php the_post_thumbnail(); ?>
		<div class="date">
			<div class="startdate">
				<div class="month"><?php echo date( "M", $startdate ); ?></div>
				<div class="day"><?php echo date( "d", $startdate ); ?></div>
			</div>
			<div class="enddate">
				<div class="until">until</div>
				<div class="day"><?php echo date( "M d", $enddate ); ?></div>
			</div>
		</div>
		<div class="actions">
			<a class="close-action" href="<?php echo get_home_url(); ?>">go back</a>
			<span> - </span>
			<a class="subscribe-action" href="?subscribe=<?php echo ! $subscribed; ?>" data-id="<?php echo $post->ID; ?>"><?php echo $subscribed ? 'unsubscribe' : 'subscribe'; ?></a>
			<?php
			if ( current_user_can( 'edit_posts' ) ) {
				?>
				<span> - </span>
				<a class="edit-action" href="<?php echo get_edit_post_link(); ?>">edit</a>
				<?php
			}

			if ( $subscriber_count > 0 ) {
				?><br><?php
				echo $subscriber_count . ( $subscriber_count == 1 ? " subscriber" : " subscribers" );
			}
			?>
		</div>
		<h4 class="title"><?php the_title(); ?></h4>
	</header>
	<div class="content"><?php the_content(); ?></div>
	<footer>
		<ul class="links">
			<?php
			$links = Karma_Events::get_contacts();

			if ( ! empty( $links ) ) {
				foreach ( $links as $name => $link ) {
					?>
					<li><a href="<?php echo $link['link']; ?>"><?php echo $link['name']; ?></a></li>
					<?php
				}
			}
			?>
		</ul>
		<div class="tags">
			<?php the_tags(); ?>
		</div>
		<?php
		$news_list = get_posts( array(
			'posts_per_age'  => -1,
			'post_type'      => Karma_News::$slug,
			'meta_key'       => Karma_News::$parent_key,
			'meta_value'     => get_the_ID(),
		) );

		if ( ! empty( $news_list ) ) {
			?>
			<h4>Announcements</h4>
			<ul class="news">
				<?php
				foreach ( $news_list as $index => $news ) {
					?>
					<li>
						<a href="<?php echo get_the_permalink( $news->ID ); ?>">
							<?php echo get_the_title( $news->ID ); ?>
						</a>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}
		?>
	</footer>
	<noscript>
		<a href="<?php the_permalink(); ?>">See details</a>
	</noscript>
</article>