<?php
/*
Plugin Name: CSD Functions - Calendar
Version: 1.2
Description: Custom Google calendar implementation for district websites
Author: Josh Armentano
Author URI: https://abidewebdesign.com
Plugin URI: https://abidewebdesign.com
*/
require WP_CONTENT_DIR . '/plugins/plugin-update-checker-master/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/csd509j/CSD-functions-calendar',
	__FILE__,
	'CSD-functions-calendar'
);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function render_calendar() {
?>
<div class="row">
	<div class="col-sm-12">
<!--
		<?php if( have_rows('calendars', 'options') ): ?>
			<?php while( have_rows('calendars', 'options') ): the_row(); ?>
				<label><input type="checkbox" id="<?php echo str_replace(' ', '_', the_sub_field('calendar_name')); ?>" checked="checked" value="0"/><?php the_sub_field('calendar_name'); ?></label>
			<?php endwhile; ?>
		<?php endif; ?> 
-->
		<div id='calendar'></div>
	</div>
</div>
<script>
	$(function() {
		var allEventSources = [];
		<?php if( have_rows('calendars') ): ?>
			<?php while( have_rows('calendars') ): the_row(); ?>
				<?php $calendar_address = get_sub_field('calendar_address'); ?>
				<?php $calendar_name = get_sub_field('calendar_name'); ?>
				<?php $calendar_color = get_sub_field('calendar_color'); ?>
				
				allEventSources.push(
				{
					id: '<?php echo $calendar_name; ?>', 
					googleCalendarId: '<?php echo $calendar_address; ?>', 
					textColor: '<?php echo $calendar_color; ?>',
					backgroundColor: '<?php echo $calendar_color; ?>',
					borderColor: '<?php echo $calendar_color; ?>',
				});

			<?php endwhile; ?>
		<?php endif; ?>
		
		$('input[type="checkbox"]').change(function () {
			if ($(this).is(":checked")) {
				$('#calendar').fullCalendar('addEventSource', allEventSources[$(this).val()]);
			}
			else {
				$('#calendar').fullCalendar('removeEventSource', allEventSources[$(this).val()]);
			}
			$('#calendar').fullCalendar('rerenderEvents');
		});

		$('#calendar').fullCalendar({

			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,listYear'
			},

			displayEventTime: true, // show the time column in list view
			
			googleCalendarApiKey: 'AIzaSyCtn4VYI0llZ2sEGiMgezxWyBDTVuKaHds',
				
			eventSources: allEventSources,

			eventClick: function (event) {
				// opens events in a popup window
				window.open(event.url, '_blank', 'width=700,height=600');
				return false;
			},
			eventRender: function(eventObj, el) {
				if (eventObj.description === undefined) {
					eventObj.description = "";
				} 
				$(el).popover({
					title: eventObj.title,
					content: eventObj.description,
					trigger: 'hover',
					placement: 'top',
					container: 'body'
				}); 
			}
			
		});

	});
</script>
<?php
}

function calendar_enqueue_script() {
	if (is_page_template('page-calendar.php')) {
		wp_enqueue_style( 'fullcalendar.min.css', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css' ); 
		wp_enqueue_style( 'fullcalendar-style.css', plugin_dir_url( __FILE__ ) . 'style.css' ); 
		wp_enqueue_script( 'moment.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js', 'jquery', '', true );
		wp_enqueue_script( 'popper.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', 'jquery', '', true );
		wp_enqueue_script( 'full-calendar.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js', 'jquery', '', true );
		wp_enqueue_script( 'gcal.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/gcal.min.js', 'jquery', '', true );
	}
}
add_action( 'wp_enqueue_scripts', 'calendar_enqueue_script' );