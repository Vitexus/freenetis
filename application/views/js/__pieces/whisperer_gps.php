<?php
/**
 * GPS javascript view.
 * During adding of address point, try to add GPS for selected country,
 * street, street number and town and set it to GPS fields.
 * 
 * @author Michal Kliment, Ondřej Fibich, David Raška
 */

// IDE complementation
if (FALSE): ?><script type="text/javascript"><?php endif

?>
	var gps_get = null;
	var domicile_gps_get = null;
	// try to find GPS for address point and set it (esed ad devices and members)
	$("#country_id, #town, #district, #street, #zip").change(function ()
	{
		if (gps_get !== null)
		{
			gps_get.abort();
			gps_get = null;
		}
		
		gps_get = $.ajax({
			url: '<?php echo url_lang::base() ?>address_points/get_gps_by_address_string/',
			data: "country_id="+encodeURIComponent($("#country_id").val())+"&town="+encodeURIComponent($("#town").val())+"&district="+encodeURIComponent($("#district").val())+"&street="+encodeURIComponent($("#street").val())+"&zip="+encodeURIComponent($("#zip").val()),
			success: function( data ) {
				if (data !== '')
				{
					var s = data.split(" ");
					$("#gpsx").val(s[0]);
					$("#gpsy").val(s[1]);
					
					// show on map
					if ($("#ap_map").length)
					{
						$('#ap_map').html('<div class="ap_form_map_container no_map"></div>');
					
						var width = $('#ap_map .no_map').width();
						var height = $('#ap_map .no_map').height();
						
						var map = '<img src="http://maps.google.com/maps/api/staticmap?center='+s[0]+','+s[1]+'&zoom=13&maptype=normal&size='+width+'x'+height+'&markers=color:red%7C'+s[0]+','+s[1]+'&language<?php echo Config::get('lang')?>&sensor=false"></img>';
						map = '<div class="ap_form_map_container"><a href="http://maps.google.com/maps?f=q&hl=<?php echo Config::get('lang') ?>&geocode=&q='+s[0]+','+s[1]+'&z=18&t=h&ie=UTF8" target="_blank">'+map+'</a></div>';
						$("#ap_map").html(map);
						map_add_zoom_buttons($("#ap_map a"), 6, 20);
					}
				}
				else
				{
					$("#gpsx").val("");
					$("#gpsy").val("");
					$('#ap_map').html('<div class="ap_form_map_container no_map"></div>');
				}

				gps_get = null;
			}
		});
	});

	// try to find GPS for domicile address point and set it (used at members)
	$("#domicile_country_id, #domicile_town, #domicile_district, #domicile_street, #domicile_zip").change(function ()
	{
		if (domicile_gps_get !== null)
		{
			domicile_gps_get.abort();
			domicile_gps_get = null;
		}
		
		domicile_gps_get = $.ajax({
			url: '<?php echo url_lang::base() ?>address_points/get_gps_by_address_string/',
			data: "country_id="+encodeURIComponent($("#domicile_country_id").val())+"&town="+encodeURIComponent($("#domicile_town").val())+"&street="+encodeURIComponent($("#domicile_street").val())+"&zip="+encodeURIComponent($("#domicile_zip").val()),
			success: function( data ) {
				if (data !== '')
				{
					var s = data.split(" ");
					$("#domicile_gpsx").val(s[0]);
					$("#domicile_gpsy").val(s[1]);
					
					// show on map
					if ($("#domicile_ap_map").length)
					{
						$('#domicile_ap_map').html('<div class="ap_form_map_container no_map"></div>');
					
						var width = $('#domicile_ap_map .no_map').width();
						var height = $('#domicile_ap_map .no_map').height();
						
						var map = '<img src="http://maps.google.com/maps/api/staticmap?center='+s[0]+','+s[1]+'&zoom=13&maptype=normal&size='+width+'x'+height+'&markers=color:red%7C'+s[0]+','+s[1]+'&language<?php echo Config::get('lang')?>&sensor=false"></img>';
						map = '<div class="ap_form_map_container"><a href="http://maps.google.com/maps?f=q&hl=<?php echo Config::get('lang') ?>&geocode=&q='+s[0]+','+s[1]+'&z=18&t=h&ie=UTF8" target="_blank">'+map+'</a></div>';
						$("#domicile_ap_map").html(map);
						map_add_zoom_buttons($("#domicile_ap_map a"), 6, 20);
					}
				}
				else
				{
					$("#domicile_gpsx").val("");
					$("#domicile_gpsy").val("");
					$('#domicile_ap_map').html('<div class="ap_form_map_container no_map"></div>');
				}

				domicile_gps_get = null;
			}
		});
	});
	
	$('#street, #domicile_street').keyup(function(){
		$(this).trigger('change');
	});
