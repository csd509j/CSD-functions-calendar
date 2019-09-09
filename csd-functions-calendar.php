<?php
/*
Plugin Name: CSD Functions - Calendar
Version: 2.4
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

function get_language() {
	
	// Set default language
	$current_lang = apply_filters( 'wpml_current_language', NULL );
	
	if ( !$current_lang ) {
		
		$current_lang = 'default';
		
	} 
	
	return $current_lang;
}

function render_calendar() {	
	
	$current_lang = get_language();
	
?>

	<div class="row">
		<div class="col-lg-4 d-none d-lg-flex">
			<div class="calendar-dropdown">
				<button type="button" id="dropdown-menu" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-rss"></i> <?php _e('Subscribe','csdschools'); ?> </button>
	            <ul class="dropdown-menu" aria-labelledby="dropdown-menu" >
		            
		            <?php if ( have_rows('calendars', 'options') ): ?>
					
						<?php while ( have_rows('calendars', 'options') ): the_row(); ?>
					
							<li>
								<a href="<?php the_sub_field('calendar_ical'); ?>"><i class="fa fa-download"></i> <label><?php the_sub_field('calendar_name'); ?></label></a>
							</li>
					
						<?php endwhile; ?>		
					
					<?php endif; ?>			
					
					<?php if ( have_rows('school_calendars', 'options') ): ?>
						
						<li role="separator" class="divider"></li>
						<li class="dropdown-header"><?php _e('School Calendars','csdschools'); ?></li>
						
						<?php while ( have_rows('school_calendars', 'options') ): the_row(); ?>
						
							<li>
								<a href="<?php the_sub_field('calendar_ical'); ?>"><i class="fa fa-download"></i> <label><?php the_sub_field('calendar_name'); ?></label></a>
							</li>
						
						<?php endwhile; ?>
					
					<?php endif; ?>
	            
	            </ul>
			</div>
			<div id="calendar-dropdown-view" class="calendar-dropdown">
				<button type="button" id="dropdown-menu-view" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-filter"></i> <?php _e('Filter','csdschools'); ?> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdown-menu-view" >
					
					<?php if ( have_rows('calendars', 'options') ): ?>
					
						<?php $count = 0; ?>
					
						<?php while ( have_rows('calendars', 'options') ): the_row(); ?>
					
							<li>
							   <label class="checkbox"><input type="checkbox" id="<?php echo str_replace(' ', '_', get_sub_field('calendar_name')); ?>" <?php echo get_sub_field('visible') ? 'checked="checked"' : ''; ?> value="<?php echo $count; ?>" /><span class="label-text"><?php the_sub_field('calendar_name'); ?></label>
							</li>
					
							<?php $count++; ?>
					
						<?php endwhile; ?>
					
					<?php endif; ?>
					
					<?php if ( have_rows('school_calendars', 'options') ): ?>
					
						<li role="separator" class="divider"></li>
						<li class="dropdown-header"><?php _e('School Calendars','csdschools'); ?></li>	
					
					 	<?php while ( have_rows('school_calendars', 'options') ): the_row(); ?>
					
					 		<li>
								<label class="checkbox"><input type="checkbox" id="<?php echo str_replace(' ', '_', get_sub_field('calendar_name')); ?>" <?php echo get_sub_field('visible') ? 'checked="checked"' : ''; ?> value="<?php echo $count; ?>" /><span class="label-text"><?php the_sub_field('calendar_name'); ?></label>
					 		</li>
					
					 		<?php $count++; ?>
					
					 	<?php endwhile; ?>
					
					<?php endif; ?>    
					       
				</ul>
			</div>
		</div>
		<div class="col-12 col-md-7 col-lg-4 text-center text-md-left text-lg-center">
			<h1 id="month" class="mb-0"></h1>
		</div>
		<div id="calendar-buttons" class="col-12 col-md-5 col-lg-4 text-center text-md-right">
			<button id="prev" class="btn btn-primary btn-sm"><i class="fa fa-caret-left"></i> <?php _e('Prev','csdschools'); ?></button>
			<button id="next" class="btn btn-primary btn-sm"><?php _e('Next','csdschools'); ?> <i class="fa fa-caret-right "></i></button>
		</div>
	</div>
	<div class="row">
		<div class="col-12 mt-1 mt-lg-0">
			<div id="calendar"></div>
		</div>
	</div>

	<script>
		
		document.addEventListener( 'DOMContentLoaded', function() {
			
			window.mobilecheck = function() {
				var check = false;
				(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
				return check;
			};
		
			var initialEventSources = [];
			var allEventSources = [];
			
			// Get district calendars
			<?php if ( have_rows('calendars', 'options') ): ?>
				
				<?php while ( have_rows('calendars', 'options') ): the_row();  ?>
				
					<?php $calendar_address = get_sub_field('calendar_address'); ?>
					<?php $calendar_name = get_sub_field('calendar_name'); ?>
					<?php $calendar_color = get_sub_field('calendar_color'); ?>
					
					// Load calendars that are marked as visable
					<?php if ( get_sub_field('visible') ): ?>
						
						initialEventSources.push({
							id: '<?php echo str_replace(' ' , '_', $calendar_name); ?>', 
							googleCalendarId: '<?php echo $calendar_address; ?>', 
							textColor: '<?php echo $calendar_color; ?>',
							backgroundColor: '<?php echo $calendar_color; ?>',
							borderColor: '<?php echo $calendar_color; ?>',
						});
					
					<?php endif; ?>
					
					// Load all available calendars
					allEventSources.push({
						id: '<?php echo str_replace(' ' , '_', $calendar_name); ?>', 
						googleCalendarId: '<?php echo $calendar_address; ?>', 
						textColor: '<?php echo $calendar_color; ?>',
						backgroundColor: '<?php echo $calendar_color; ?>',
						borderColor: '<?php echo $calendar_color; ?>',
					});
					
				<?php endwhile; ?>
				
			<?php endif; ?>
		
			// If applicable, get school calendars
			<?php if ( have_rows('school_calendars', 'options') ): ?>
				
				<?php while ( have_rows('school_calendars', 'options') ): the_row(); ?>
				
					<?php $calendar_address = get_sub_field('calendar_address'); ?>
					<?php $calendar_name = get_sub_field('calendar_name'); ?>
					<?php $calendar_color = get_sub_field('calendar_color'); ?>
					
					// Load all available calendars
					allEventSources.push({
						id: '<?php echo str_replace(' ' , '_', $calendar_name); ?>', 
						googleCalendarId: '<?php echo $calendar_address; ?>', 
						textColor: '<?php echo $calendar_color; ?>',
						backgroundColor: '<?php echo $calendar_color; ?>',
						borderColor: '<?php echo $calendar_color; ?>',
					});
					
				<?php endwhile; ?>
				
			<?php endif; ?>
		
		    var calendarEl = document.getElementById('calendar');
		
		    var calendar = new FullCalendar.Calendar( calendarEl, {
				
				plugins: [ 'list' , 'dayGrid', 'googleCalendar', 'bootstrap' ],
				
				themeSystem: 'bootstrap',
							
				header: false,
							  
				googleCalendarApiKey: 'AIzaSyCtn4VYI0llZ2sEGiMgezxWyBDTVuKaHds',
				
				timezone: 'America/Los_Angeles',
						  
				eventSources: initialEventSources,
				
				defaultView: window.mobilecheck() ? 'listMonth' : 'dayGridMonth',
				
				<?php if ( $current_lang == 'es' ): ?>
				
					locale: 'es',
					
				<?php endif; ?>
		
				eventClick: function(arg) {
				
					// opens events in a popup window
					window.open(arg.event.url, '_blank', 'width=700,height=600');
					
					// prevents current tab from navigating
					arg.jsEvent.preventDefault();
				}
				
			});
			
			calendar.render();
			
			//Set current month/year string
			var moment = calendar.getDate();
			document.getElementById('month').innerHTML = moment.toLocaleString('<?php echo $current_lang; ?>', { month: 'long' }) + ' ' + moment.toLocaleString('default', { year: 'numeric' });
			
			// Handle previous and next buttons for months
			document.getElementById('prev').onclick = function() {
			    
			    calendar.prev();
			    var moment = calendar.getDate();
				document.getElementById('month').innerHTML = moment.toLocaleString('<?php echo $current_lang; ?>', { month: 'long' }) + ' ' + moment.toLocaleString('default', { year: 'numeric' });
			
			};
		
			document.getElementById('next').onclick = function() {
				
				calendar.next();
				var moment = new Date(calendar.getDate());
				document.getElementById('month').innerHTML = moment.toLocaleString('<?php echo $current_lang; ?>', { month: 'long' }) + ' ' + moment.toLocaleString('default', { year: 'numeric' });
			
			};
			
			// Toggle function for viewing specific calendars
			$( '#calendar-dropdown-view li' ).on( 'click', function( event ) {
		       
				var $checkbox = $(this).find('.checkbox');
				
				if ( !$checkbox.length ) {
				    
				    return;
				
				}
				
				var $input = $checkbox.find('input');
				
				if ( $input.is(':checked') ) {
				   
					// Calendar unchecked
					$input.prop('checked', false);
					var source_array = allEventSources[$input.val()];
					var source = calendar.getEventSourceById( source_array['id'] );
					source.remove();
				    
				} else {
				    
				    // Calendar checked
				    $input.prop('checked', true);
				    calendar.addEventSource( allEventSources[$input.val()] );
				
				}
				
				calendar.refetchEvents();
				
				return false;
				
			}); 
		});
	
	</script>
	
<?php 
	
}

function render_list_view() { 
	
?>

	<div id='calendar-list'></div>
	<script>
		
		document.addEventListener( 'DOMContentLoaded', function() {
			
			var allEventSources = [];
			
			<?php if( have_rows('calendars', 'options') ): ?>
			
				<?php while( have_rows('calendars', 'options') ): the_row(); ?>
				
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
	
			var calendarEl = document.getElementById('calendar-list');
			
			var calendar = new FullCalendar.Calendar( calendarEl, {
					
				plugins: [ 'list', 'googleCalendar', 'bootstrap' ],		
				
				themeSystem: 'bootstrap',
				
				defaultView: 'list',
							
				displayEventTime: true, // show the time column in list view
				
				header: false,
				
				eventLimit: true,
				
				allDayText: "-",
				
				<?php if ( get_language() == 'es' ):  ?>
				
					locale: 'es',
					
				<?php endif; ?>
							
				googleCalendarApiKey: 'AIzaSyCtn4VYI0llZ2sEGiMgezxWyBDTVuKaHds',
					
				eventSources: allEventSources,
				
				timezone: 'America/Los_Angeles',
	            
	            views: {
					list: {
						duration: { days: <?php echo (get_field('school_type', 'options') == 'Elementary' ? '10' : '5'); ?> },
						eventLimit: 1
					}
				},
				
				eventClick: function (event) {
					// opens events in a popup window
					window.open(event.url, '_blank', 'width=700,height=600');
					return false;
				},
				
			});
			
			calendar.render();
	
		});
		
	</script>

<?php

}

function render_list_view_district() { 

?>
	<div id='calendar-list-district'></div>
	<script>
		
		document.addEventListener( 'DOMContentLoaded', function() {
	
			var allEventSources = [];
			
			<?php if( have_rows('calendars', 'options') ): ?>
			
				<?php while( have_rows('calendars', 'options') ): the_row(); ?>
				
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
			
			var calendarEl = document.getElementById('calendar-list-district');
			
			var calendar = new FullCalendar.Calendar( calendarEl, {
					
				plugins: [ 'list', 'googleCalendar', 'bootstrap' ],		
				
				themeSystem: 'bootstrap',
				
				defaultView: 'list',
							
				googleCalendarApiKey: 'AIzaSyCtn4VYI0llZ2sEGiMgezxWyBDTVuKaHds',
					
				eventSources: allEventSources,
				
				header: false,
				
				timezone: 'America/Los_Angeles',
				
				views: {
	                list: {
	                    duration: { days: 30 },
	                    eventLimit: 1,
	                }
	            },
	
				eventClick: function (event) {
					// opens events in a popup window
					window.open(event.url, '_blank', 'width=700,height=600');
					return false;
				},
				
			});
			
			calendar.render();
			
		});
		
	</script>

<?php
	
}

function render_page_builder_calendar($calendar_address) {

?>
	<div id='calendar-list-page'></div>
	<script>
		document.addEventListener( 'DOMContentLoaded', function() {
			
			var allEventSources = [];
			allEventSources.push(
			{
				id: 'calendar-list-page', 
				googleCalendarId: '<?php echo $calendar_address; ?>', 
				textColor: '#333333',
				backgroundColor: '#ffffff',
				borderColor: '#333333',
			});
			
			var calendarEl = document.getElementById('calendar-list-page');
		
		    var calendar = new FullCalendar.Calendar( calendarEl, {
				
				plugins: [ 'list', 'googleCalendar', 'bootstrap' ],
				
				themeSystem: 'bootstrap',
								
				displayEventTime: true, 
				
				googleCalendarApiKey: 'AIzaSyCtn4VYI0llZ2sEGiMgezxWyBDTVuKaHds',
					
				eventSources: allEventSources,
				
				header: false,
				
				<?php if ( get_language() == 'es' ):  ?>
				
					locale: 'es',
					
				<?php endif; ?>
				
				timezone: 'America/Los_Angeles',
				
				defaultView: 'listMonth',
				
				views: {
	                list: {
	                    duration: { days: 120 },
	                }
	            },
	
				eventClick: function (event) {
					// opens events in a popup window
					window.open(event.url, '_blank', 'width=700,height=600');
					return false;
				},
				
			});
			
			calendar.render();
			
		});
		
	</script>
	
<?php 
	
}

function render_block_calendar($calendar_address) {

?>
	<div id='calendar-block-header'></div>
	<script>
		document.addEventListener( 'DOMContentLoaded', function() {
		
			var allEventSources = [];
			allEventSources.push(
			{
				id: 'calendar-block-header', 
				googleCalendarId: '<?php echo $calendar_address; ?>', 
				textColor: '#ffffff',
				backgroundColor: '#ffffff',
				borderColor: '#333333',
			});
					
		    var calendarEl = document.getElementById('calendar-block-header');
		
		    var calendar = new FullCalendar.Calendar( calendarEl, {
				
				plugins: [ 'list', 'googleCalendar', 'bootstrap' ],
				
				themeSystem: 'bootstrap',
							
				header: false,
				
				listDayFormat: false,
				
				listDayAltFormat: false,
				
				displayEventTime: false,
				
				noEventsMessage: '<?php _e('No School Today','csdschools'); ?>',
							  
				googleCalendarApiKey: 'AIzaSyCtn4VYI0llZ2sEGiMgezxWyBDTVuKaHds',
				
				timezone: 'America/Los_Angeles',
						  
				eventSources: allEventSources,
				
				defaultView: 'listDay',
				
				eventRender: function (info) {
					document.getElementById('calendar-block-header').innerHTML = '<ul id="calendar-block-wrap" class="list-inline"><li class="list-inline-item"><i class="fa fa-calendar-alt"></i> ' + info.event.title + '</li><li class="list-inline-item d-none d-md-inline-block"><i class="fa fa-phone-square"></i> <?php _e('Attendance','csdschools'); ?> <a href="tel:<?php the_field('attendance_phone', 'options'); ?>"><?php the_field('attendance_phone', 'options'); ?></a></li></ul>';
				}
				
			});
			
			calendar.render();
			
		});
	</script>
	
<?php
	 	
}

function calendar_enqueue_script() {
	
	$plugin = get_plugin_data( __FILE__, false, false );
	
	wp_enqueue_style( 'fullcalendar-core.min.css', plugin_dir_url( __FILE__ ) . '/assets/css/core.main.min.css', '', $plugin['Version'] ); 
	wp_enqueue_style( 'fullcalendar-daygrid.min.css', plugin_dir_url( __FILE__ ) . '/assets/css/daygrid.main.min.css', '', $plugin['Version'] );
	wp_enqueue_style( 'fullcalendar-list.min.css', plugin_dir_url( __FILE__ ) . '/assets/css/list.main.min.css', '', $plugin['Version'] );
	wp_enqueue_style( 'fullcalendar-bootstrap.min.css', plugin_dir_url( __FILE__ ) . '/assets/css/bootstrap.main.min.css', '', $plugin['Version'] ); 
	wp_enqueue_style( 'fullcalendar-style.css', plugin_dir_url( __FILE__ ) . '/assets/css/style.css', '', $plugin['Version'] ); 
	wp_enqueue_script( 'fullcalendar.min.js', plugin_dir_url( __FILE__ ) . '/assets/js/core.main.min.js', '', '', true );
	wp_enqueue_script( 'fullcalendar-daygrid.min.js', plugin_dir_url( __FILE__ ) . '/assets/js/daygrid.main.min.js', '', '', true );
	wp_enqueue_script( 'fullcalendar-list.min.js', plugin_dir_url( __FILE__ ) . '/assets/js/list.main.min.js', '', '', true );
	wp_enqueue_script( 'fullcalendar-locales.min.js', plugin_dir_url( __FILE__ ) . '/assets/js/locales-all.min.js', '', '', true );
	wp_enqueue_script( 'fullcalendar-google-calendar.min.js', plugin_dir_url( __FILE__ ) . '/assets/js/google-calendar.main.min.js', '', '', true );
	wp_enqueue_script( 'full-calendar-boostrap.min.js', plugin_dir_url( __FILE__ ) . '/assets/js/bootstrap.main.min.js', '', '', true );
	
}
add_action( 'wp_enqueue_scripts', 'calendar_enqueue_script' );