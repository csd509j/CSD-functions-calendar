<?php
/*
Plugin Name: CSD Functions - Calendar
Version: 1.16
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
	<div class="col-xs-4">
		<?php if( have_rows('calendars', 'options') ): ?>
			<div id="calendar-dropdown" class="margin-bottom-one">
				<button type="button" id="dropdown-menu" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span> Subscribe <span class="caret"></span></button>
	            <ul class="dropdown-menu" aria-labelledby="dropdown-menu" >
					<?php while( have_rows('calendars', 'options') ): the_row(); ?>
						<li><a href="<?php the_sub_field('calendar_ical'); ?>"><i class="fa fa-download"></i> <?php the_sub_field('calendar_name'); ?></a></li>
					<?php endwhile; ?>
	            </ul>
			</div>
		<?php endif; ?> 
	</div>
	<div class="col-xs-4 text-center">
		<h1 id="month"><?php echo date('F'); ?></h1>
	</div>
	<div class="col-xs-4 text-right">
		<button id="prev" class="btn btn-primary btn-sm"><i class="fa fa-caret-left"></i> Prev</button>
		<button id="next" class="btn btn-primary btn-sm">Next <i class="fa fa-caret-right "></i></button>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div id="calendar"></div>
	</div>
</div>
<script>
	$(function() {
		window.mobilecheck = function() {
			var check = false;
			(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
			return check;
		};
    
		var allEventSources = [];
		
		<?php if( have_rows('calendars', 'options') ): ?>
			<?php while( have_rows('calendars', 'options') ): the_row(); ?>
				<?php $calendar_address = get_sub_field('calendar_address'); ?>
				<?php $calendar_name = get_sub_field('calendar_name'); ?>
				<?php $calendar_color = get_sub_field('calendar_color'); ?>
				
				allEventSources.push(
				{
					id: '<?php echo str_replace(' ' , '_', $calendar_name); ?>', 
					googleCalendarId: '<?php echo $calendar_address; ?>', 
					textColor: '<?php echo $calendar_color; ?>',
					backgroundColor: '<?php echo $calendar_color; ?>',
					borderColor: '<?php echo $calendar_color; ?>',
				});

			<?php endwhile; ?>
		<?php endif; ?>
	
		$('#calendar').fullCalendar({

			header: false,
			
			defaultView: window.mobilecheck() ? "listMonth" : "month",
			
			displayEventTime: true, // show the time column in list view
			
			googleCalendarApiKey: 'AIzaSyCtn4VYI0llZ2sEGiMgezxWyBDTVuKaHds',
				
			eventSources: allEventSources,
			
			timezone: 'America/Los_Angeles',

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
		
		$('#prev').on('click', function() {
		    $('#calendar').fullCalendar('prev'); // call method
		    var moment = $('#calendar').fullCalendar('getDate');
		    $('#month').html(moment.format('MMMM'));
		});

		$('#next').on('click', function() {
			$('#calendar').fullCalendar('next'); // call method
			var moment = $('#calendar').fullCalendar('getDate');
			$('#month').html(moment.format('MMMM'));
		});
	});
</script>
<?php
}

function render_list_view() { 
?>
<div id='calendar-list'></div>
<script>
	$(function() {
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
		$('#calendar-list').fullCalendar({

			defaultView: 'list',
						
			displayEventTime: true, // show the time column in list view
			
			header: false,
			
			eventLimit: true,
			
			allDayText: "-",
						
			googleCalendarApiKey: 'AIzaSyCtn4VYI0llZ2sEGiMgezxWyBDTVuKaHds',
				
			eventSources: allEventSources,
			
			timezone: 'America/Los_Angeles',
            
            views: {
				list: {
					duration: { days: 5 },
					eventLimit: 1
				}
			},
			
			eventClick: function (event) {
				// opens events in a popup window
				window.open(event.url, '_blank', 'width=700,height=600');
				return false;
			},
			
		});
	});
</script>
<?php
}
function render_list_view_district() { 
?>
<div id='calendar-list-district'></div>
<script>
	$(function() {
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
		
		$('#calendar-list-district').fullCalendar({

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
	});
</script>
<?php
}

function render_page_builder_calendar($calendar_address) {
?>
	<div id='calendar-list-page'></div>
	<script>
		$(function() {
			var allEventSources = [];
			allEventSources.push(
			{
				id: 'calendar-list-page', 
				googleCalendarId: '<?php echo $calendar_address; ?>', 
				textColor: '#333333',
				backgroundColor: '#ffffff',
				borderColor: '#333333',
			});
			
			$('#calendar-list-page').fullCalendar({
	
				defaultView: 'list',
				
				displayEventTime: true, // show the time column in list view
				
				googleCalendarApiKey: 'AIzaSyCtn4VYI0llZ2sEGiMgezxWyBDTVuKaHds',
					
				eventSources: allEventSources,
				
				header: false,
				
				timezone: 'America/Los_Angeles',
				
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
		});
	</script>
<?php 
}

function render_block_calendar($calendar_address) {
?>
	<div id='calendar-block-header'></div>
	<script>
		$(function() {
			var allEventSources = [];
			allEventSources.push(
			{
				id: 'calendar-block-header', 
				googleCalendarId: '<?php echo $calendar_address; ?>', 
				textColor: '#ffffff',
				backgroundColor: '#ffffff',
				borderColor: '#333333',
			});
			
			$('#calendar-block-header').fullCalendar({
	
				defaultView: 'listDay',
				
				listDayFormat: false,
				
				listDayAltFormat: false,
				
				noEventsMessage: 'No school today',
				
				displayEventTime: false, // show the time column in list view
				
				googleCalendarApiKey: 'AIzaSyCtn4VYI0llZ2sEGiMgezxWyBDTVuKaHds',
					
				eventSources: allEventSources,
				
				header: false,
								
				timezone: 'America/Los_Angeles',
			
				eventRender: function (event, element) {
					content = '<ul id="calendar-block-wrap" class="list-inline"><li><i class="fa fa-calendar"></i> ' + event.title + '</li><li class="hidden-xs"><i class="fa fa-phone-square"></i> Attendance <a href="tel:<?php the_field('attendance_phone', 'options'); ?>"><?php the_field('attendance_phone', 'options'); ?></a></li></ul>';
					$('#calendar-block-header').html(content);
					$('#calendar-block-header').removeClass('fc fc-unthemed fc-ltr')
				}
			});
		});
	</script>
<?php 	
}

function calendar_enqueue_script() {
	$plugin = get_plugin_data( __FILE__, false, false );
	
	wp_enqueue_style( 'fullcalendar.min.css', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css' ); 
	wp_enqueue_style( 'fullcalendar-style.css', plugin_dir_url( __FILE__ ) . 'style.css', '', $plugin['Version'] ); 
	wp_enqueue_script( 'moment.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js', 'jquery', '', true );
	wp_enqueue_script( 'popper.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', 'jquery', '', true );
	wp_enqueue_script( 'full-calendar.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js', 'jquery', '', true );
	wp_enqueue_script( 'gcal.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/gcal.min.js', 'jquery', '', true );
}
add_action( 'wp_enqueue_scripts', 'calendar_enqueue_script' );