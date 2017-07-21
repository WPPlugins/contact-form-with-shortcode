<?php
if ( $news_query->have_posts() ) {
	while ( $news_query->have_posts() ) {
		$news_query->the_post();
		?>
		<div class="content">
			<table bgcolor="">
				<tr>
				<?php if ( has_post_thumbnail() && $featuredimage == 'yes') { ?>
					<td class="small" width="20%" style="vertical-align: top; padding-right:10px;"><?php echo get_the_post_thumbnail($news_query->post->ID,'thumbnail');?></td>
				<?php }	?>
					
				<td>
						<h4><?php the_title();?></h4>
						<p><?php the_excerpt();?></p>
						
						<?php if($readmore == 'yes'){
							echo '<a class="btn" href="'.get_permalink().'">Read More</a>';
						}
						?>
					</td>
				</tr>
			</table>
		</div>
	<?php }
} else {
	echo '<div class="content">Sorry. No data found!</div>';
}
?>