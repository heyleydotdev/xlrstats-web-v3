<?php
/**
 * XLRstats : Real Time Player Stats (http://www.xlrstats.com)
 * (CC) BY-NC-SA 2005-2013, Mark Weirath, Özgür Uysal
 *
 * Licensed under the Creative Commons BY-NC-SA 3.0 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          http://www.xlrstats.com
 * @license       Creative Commons BY-NC-SA 3.0 License (http://creativecommons.org/licenses/by-nc-sa/3.0/)
 * @package       app.Config.games
 * @since         XLRstats v3.0
 * @version       0.1
 */

$config = array(
	'gameName' => 'Call of Duty 4: Modern Warfare',

	/**
	 * Team names
	 */
	'teams' => array(
		'2' => 'OpFor / Spetznaz',	// Red team
		'3' => 'Marines / S.A.S.',	// Blue team
		'-1' => 'Spectators'
	),

	'maps' => array(
		//Map Image Path (empty = don't render map icons)
		'image_path' => '',

		//*********************
		// Map names
		//*********************
		// Stock CoD4
		'mp_backlot' => array('Backlot', 'description', 'mp_backlot.jpg'),
		'mp_bloc' => array('Bloc', 'description', 'mp_bloc.jpg'),
		'mp_bog' => array('Bog', 'description', 'mp_bog.jpg'),
		'mp_cargoship' => array('Wet Work', 'description', 'mp_cargoship.jpg'),
		'mp_citystreets' => array('City Streets', 'description', 'mp_citystreets.jpg'),
		'mp_convoy' => array('Convoy', 'description', 'mp_convoy.jpg'),
		'mp_countdown' => array('Countdown', 'description', 'mp_countdown.jpg'),
		'mp_crash' => array('Crash', 'description', 'mp_crash.jpg'),
		'mp_crossfire' => array('Crossfire', 'description', 'mp_crossfire.jpg'),
		'mp_farm' => array('Down Pour', 'description', 'mp_farm.jpg'),
		'mp_overgrown' => array('Overgrown', 'description', 'mp_overgrown.jpg'),
		'mp_pipeline' => array('Pipeline', 'description', 'mp_pipeline.jpg'),
		'mp_shipment' => array('Shipment', 'description', 'mp_shipment.jpg'),
		'mp_showdown' => array('Showdown', 'description', 'mp_showdown.jpg'),
		'mp_strike' => array('Strike', 'description', 'mp_strike.jpg'),
		'mp_vacant' => array('Vacant', 'description', 'mp_vacant.jpg'),
		'mp_crash_snow' => array('Winter Crash', 'description', 'mp_crash_snow.jpg'),
		'mp_carentan' => array('China Town', 'description', 'mp_carentan.jpg'),
		'mp_creek' => array('Creek', 'description', 'mp_creek.jpg'),
		'mp_broadcast' => array('Broadcast', 'description', 'mp_broadcast.jpg'),
		'mp_killhouse' => array('Killhouse', 'description', 'mp_killhouse.jpg'),

		// Custom Maps
		'mp_backlot_night' => array('Backlot (Night)', 'description', 'mp_backlot_night.jpg'),
		'mp_village' => array('Village', 'description', 'mp_village.jpg'),
		'mp_village_night' => array('Village (Night)', 'description', 'mp_village_night.jpg'),
		'mp_qmx_matmata' => array('Matmata', 'description', 'mp_qmx_matmata.jpg'),

		'unknown' => array('Custom Map', 'description', 'unknown.jpg'),
		'None' => array('-Unknown-', 'description', 'None.jpg'),
	),

	'weapons' => array(
		//Weapon Image Path (empty = don't render weapon icons)
		'image_path' => '',

		//*********************
		// Weapons names
		//*********************
		//Stock CoD4
		'ac130_25mm_mp' => array('AC 130 25mm', 'description', 'ac130_25mm_mp.png'),
		'ac130_40mm_mp' => array('AC 130 40mm', 'description', 'ac130_40mm_mp.png'),
		'ac130_105mm_mp' => array('AC 130 105mm', 'description', 'ac130_105mm_mp.png'),
		'airstrike_mp' => array('Air Strike', 'description', 'airstrike_mp.png'),
		'ak47_acog_mp' => array('AK-47 ACOG', 'description', 'ak47_acog_mp.png'),
		'ak47_gl_mp' => array('AK-47 Grenade Launcher', 'description', 'ak47_gl_mp.png'),
		'ak47_mp' => array('AK-47', 'description', 'ak47_mp.png'),
		'ak47_reflex_mp' => array('AK-47 Reflex', 'description', 'ak47_reflex_mp.png'),
		'ak74u_acog_mp' => array('AK-74u ACOG', 'description', 'ak74u_acog_mp.png'),
		'ak74u_mp' => array('AK-74u', 'description', 'ak74u_mp.png'),
		'ak47_silencer_mp' => array('AK-47 Silencer', 'description', 'ak47_silencer_mp.png'),
		'ak74u_reflex_mp' => array('AK-74u Reflex', 'description', 'ak74u_reflex_mp.png'),
		'ak74u_silencer_mp' => array('AK74u Silencer', 'description', 'ak74u_silencer_mp.png'),
		'artillery_mp' => array('Artillery', 'description', 'artillery_mp.png'),
		'at4_mp' => array('AT4', 'description', 'at4_mp.png'),
		'aw50_acog_mp' => array('AW-50 ACOG', 'description', 'aw50_acog_mp.png'),
		'aw50_mp' => array('AW-50', 'description', 'aw50_mp.png'),
		'barrett_acog_mp' => array('Barrett-50 ACOG', 'description', 'barrett_acog_mp.png'),
		'barrett_mp' => array('Barrett-50', 'description', 'barrett_mp.png'),
		'beretta_mp' => array('Beretta', 'description', 'beretta_mp.png'),
		'beretta_silencer_mp' => array('Beretta Silencer', 'description', 'beretta_silencer_mp.png'),
		'binoculars_mp' => array('Binoculars', 'description', 'binoculars_mp.png'),
		'brick_blaster_mp' => array('Brick Blaster', 'description', 'brick_blaster_mp.png'),
		'brick_bomb_mp' => array('Brick Bomb', 'description', 'brick_bomb_mp.png'),
		'briefcase_bomb_defuse_mp' => array('Bomb Defuse', 'description', 'briefcase_bomb_defuse_mp.png'),
		'briefcase_bomb_mp' => array('Bomb Set', 'description', 'briefcase_bomb_mp.png'),
		'c4_mp' => array('C-4', 'description', 'c4_mp.png'),
		'claymore_mp' => array('Claymore Mine', 'description', 'claymore_mp.png'),
		'cobra_20mm_mp' => array('Cobra 20mm', 'description', 'cobra_20mm_mp.png'),
		'cobra_ffar_mp' => array('Cobra Rocket', 'description', 'cobra_ffar_mp.png'),
		'colt45_mp' => array('Colt .45', 'description', 'colt45_mp.png'),
		'colt45_silencer_mp' => array('Colt .45 Silencer', 'description', 'colt45_silencer_mp.png'),
		'concussion_grenade_mp' => array('Concussion Grenade', 'description', 'concussion_grenade_mp.png'),
		'defaultweapon_mp' => array('Default Weapon', 'description', 'defaultweapon_mp.png'),
		'deserteagle_mp' => array('Colt Desert Eagle', 'description', 'deserteagle_mp.png'),
		'deserteaglegold_mp' => array('Colt Desert Eagle Gold', 'description', 'deserteaglegold_mp.png'),
		'destructible_car' => array('Exploding Vehicle', 'description', 'destructible_car.png'),
		'dragunov_acog_mp' => array('Dragunov ACOG', 'description', 'dragunov_acog_mp.png'),
		'dragunov_mp' => array('Dragunov', 'description', 'dragunov_mp.png'),
		'flash_grenade_mp' => array('Flash Grenade', 'description', 'flash_grenade_mp.png'),
		'frag_grenade_mp' => array('Frag Grenade', 'description', 'frag_grenade_mp.png'),
		'frag_grenade_short_mp' => array('Short Fuse Frag Grenade', 'description', 'frag_grenade_short_mp.png'),
		'g3_acog_mp' => array('G3 ACOG', 'description', 'g3_acog_mp.png'),
		'g3_gl_mp' => array('G3 Grenade Launcher', 'description', 'g3_gl_mp.png'),
		'g3_mp' => array('G3', 'description', 'g3_mp.png'),
		'g3_reflex_mp' => array('G3 Reflex', 'description', 'g3_reflex_mp.png'),
		'g3_silencer_mp' => array('G3 Silencer', 'description', 'g3_silencer_mp.png'),
		'g36c_acog_mp' => array('G36c ACOG', 'description', 'g36c_acog_mp.png'),
		'g36c_gl_mp' => array('G36c Grenade Launcher', 'description', 'g36c_gl_mp.png'),
		'g36c_mp' => array('G36c', 'description', 'g36c_mp.png'),
		'g36c_reflex_mp' => array('G36c Reflex', 'description', 'g36c_reflex_mp.png'),
		'g36c_silencer_mp' => array('G36c Silencer', 'description', 'g36c_silencer_mp.png'),
		'gl_ak47_mp' => array('Grenade Launcher AK-47', 'description', 'gl_ak47_mp.png'),
		'gl_g3_mp' => array('Grenade Launcher G3', 'description', 'gl_g3_mp.png'),
		'gl_g36c_mp' => array('Grenade Launcher G36c', 'description', 'gl_g36c_mp.png'),
		'gl_m4_mp' => array('Grenade Launcher M4', 'description', 'gl_m4_mp.png'),
		'gl_m14_mp' => array('Grenade Launcher M14', 'description', 'gl_m14_mp.png'),
		'gl_m16_mp' => array('Grenade Launcher M16', 'description', 'gl_m16_mp.png'),
		'gl_mp' => array('Grenade Launcher', 'description', 'gl_mp.png'),
		'helicopter_mp' => array('Helicopter', 'description', 'helicopter_mp.png'),
		'hind_ffar_mp' => array('HIND Rocket', 'description', 'hind_ffar_mp.png'),
		'humvee_50cal_mp' => array('Humvee .50 cal.', 'description', 'humvee_50cal_mp.png'),
		'TT30_mp' => array('TT 30', 'description', 'TT30_mp.png'),
		'location_selector_mp' => array('Location Selector', 'description', 'location_selector_mp.png'),
		'm4_acog_mp' => array('M4 ACOG', 'description', 'm4_acog_mp.png'),
		'm4_gl_mp' => array('M4 Grenade Launcher', 'description', 'm4_gl_mp.png'),
		'm4_mp' => array('M4', 'description', 'm4_mp.png'),
		'm4_reflex_mp' => array('M4 Reflex', 'description', 'm4_reflex_mp.png'),
		'm4_silencer_mp' => array('M4 Silencer', 'description', 'm4_silencer_mp.png'),
		'm14_acog_mp' => array('M14 ACOG', 'description', 'm14_acog_mp.png'),
		'landmine_mp' => array('Landmine', 'description', 'landmine_mp.png'),
		'm14_gl_mp' => array('M14 Grenade Launcher', 'description', 'm14_gl_mp.png'),
		'm14_mp' => array('M14', 'description', 'm14_mp.png'),
		'm14_reflex_mp' => array('M14 Reflex', 'description', 'm14_reflex_mp.png'),
		'm14_silencer_mp' => array('M14 Silencer', 'description', 'm14_silencer_mp.png'),
		'm16_acog_mp' => array('M16 ACOG', 'description', 'm16_acog_mp.png'),
		'm16_gl_mp' => array('M16 Grenade Launcher', 'description', 'm16_gl_mp.png'),
		'm16_mp' => array('M16', 'description', 'm16_mp.png'),
		'm16_reflex_mp' => array('M16 Reflex', 'description', 'm16_reflex_mp.png'),
		'm16_silencer_mp' => array('M16 Silencer', 'description', 'm16_silencer_mp.png'),
		'm21_acog_mp' => array('M21 ACOG', 'description', 'm21_acog_mp.png'),
		'm21_mp' => array('M21', 'description', 'm21_mp.png'),
		'm40a3_acog_mp' => array('M40A3 ACOG', 'description', 'm40a3_acog_mp.png'),
		'm40a3_mp' => array('M40A3', 'description', 'm40a3_mp.png'),
		'm60e4_acog_mp' => array('M60E4 ACOG', 'description', 'm60e4_acog_mp.png'),
		'm60e4_grip_mp' => array('M60E4 Grip', 'description', 'm60e4_grip_mp.png'),
		'm60e4_mp' => array('M60E4', 'description', 'm60e4_mp.png'),
		'm60e4_reflex_mp' => array('M60E4 Reflex', 'description', 'm60e4_reflex_mp.png'),
		'm1014_grip_mp' => array('M1014 Grip', 'description', 'm1014_grip_mp.png'),
		'm1014_mp' => array('M1014', 'description', 'm1014_mp.png'),
		'm1014_reflex_mp' => array('M1014 Reflex', 'description', 'm1014_reflex_mp.png'),
		'mp5_acog_mp' => array('MP5 ACOG', 'description', 'mp5_acog_mp.png'),
		'mp5_mp' => array('MP5', 'description', 'mp5_mp.png'),
		'mp5_reflex_mp' => array('MP5 Reflex', 'description', 'mp5_reflex_mp.png'),
		'mp5_silencer_mp' => array('MP5 Silencer', 'description', 'mp5_silencer_mp.png'),
		'mp44_mp' => array('MP44', 'description', 'mp44_mp.png'),
		'p90_acog_mp' => array('P90 ACOG', 'description', 'p90_acog_mp.png'),
		'p90_mp' => array('P90', 'description', 'p90_mp.png'),
		'p90_reflex_mp' => array('P90 Reflex', 'description', 'p90_reflex_mp.png'),
		'p90_silencer_mp' => array('P90 Silencer', 'description', 'p90_silencer_mp.png'),
		'radar_mp' => array('Radar', 'description', 'radar_mp.png'),
		'remington700_acog_mp' => array('Remington 700 ACOG', 'description', 'remington700_acog_mp.png'),
		'remington700_mp' => array('Remington 700', 'description', 'remington700_mp.png'),
		'rpd_acog_mp' => array('RPD ACOG', 'description', 'rpd_acog_mp.png'),
		'rpd_grip_mp' => array('RPD Grip', 'description', 'rpd_grip_mp.png'),
		'rpd_mp' => array('RPD', 'description', 'rpd_mp.png'),
		'rpd_reflex_mp' => array('RPD Reflex', 'description', 'rpd_reflex_mp.png'),
		'rpg_mp' => array('RPG', 'description', 'rpg_mp.png'),
		'saw_acog_mp' => array('SAW ACOG', 'description', 'saw_acog_mp.png'),
		'saw_bipod_crouch_mp' => array('SAW Bipod Crouched', 'description', 'saw_bipod_crouch_mp.png'),
		'saw_bipod_prone_mp' => array('SAW Bipod Prone', 'description', 'saw_bipod_prone_mp.png'),
		'saw_bipod_stand_mp' => array('SAW Bipod Standing', 'description', 'saw_bipod_stand_mp.png'),
		'saw_grip_mp' => array('SAW Grip', 'description', 'saw_grip_mp.png'),
		'saw_mp' => array('SAW', 'description', 'saw_mp.png'),
		'saw_reflex_mp' => array('SAW Reflex', 'description', 'saw_reflex_mp.png'),
		'skorpion_acog_mp' => array('Skorpion ACOG', 'description', 'skorpion_acog_mp.png'),
		'skorpion_mp' => array('Skorpion', 'description', 'skorpion_mp.png'),
		'skorpion_reflex_mp' => array('Skorpion Reflex', 'description', 'skorpion_reflex_mp.png'),
		'skorpion_silencer_mp' => array('Skorpion Silencer', 'description', 'skorpion_silencer_mp.png'),
		'smoke_grenade_mp' => array('Smoke Grenade', 'description', 'smoke_grenade_mp.png'),
		'usp_mp' => array('USP', 'description', 'usp_mp.png'),
		'usp_silencer_mp' => array('USP Silencer', 'description', 'usp_silencer_mp.png'),
		'uzi_acog_mp' => array('UZI ACOG', 'description', 'uzi_acog_mp.png'),
		'uzi_mp' => array('UZI', 'description', 'uzi_mp.png'),
		'uzi_reflex_mp' => array('UZI Reflex', 'description', 'uzi_reflex_mp.png'),
		'uzi_silencer_mp' => array('UZI Silencer', 'description', 'uzi_silencer_mp.png'),
		'winchester1200_grip_mp' => array('Winchester 1200 Grip', 'description', 'winchester1200_grip_mp.png'),
		'winchester1200_mp' => array('Winchester 1200', 'description', 'winchester1200_mp.png'),
		'winchester1200_reflex_mp' => array('Winchester 1200 Reflex', 'description', 'winchester1200_reflex_mp.png'),
		'mod_melee' => array('Knife', 'description', 'mod_melee.png'),
		'mod_falling' => array('Falling', 'description', 'mod_falling.png'),

		//No weapon?
		'none' => array('Bad luck...', 'description', 'image.png'),
	),

	'events' => array(

		//*********************
		// Event names
		//*********************
		'bomb_plant' => array('Bomb Plant', 'description', 'image.png'),
		'bomb_defuse' => array('Bomb Defuse', 'description', 'image.png'),
		're_pickup' => array('Pickup', 'description', 'image.png'),
		're_capture' => array('Capture', 'description', 'image.png'),
		're_drop' => array('Drop', 'description', 'image.png'),
	),

	/**
	 * Bodypart names
	 */
	'body_parts' => array(
		/**
		 * fixed_name => array ('console_name' => 'Easy Name')
		 * DO NOT CHANGE 'fixed_name's
		 */
		'head' => array('head' => 'Head'),
		'neck' => array('neck' => 'Neck'),
		'torso_lower' => array('torso_lower' => 'Lower torso'),
		'torso_upper' => array('torso_upper' => 'Upper torso'),
		'left_arm_upper' => array('left_arm_upper' => 'Upper left arm'),
		'left_arm_lower' => array('left_arm_lower' => 'Lower left arm'),
		'left_hand' => array('left_hand' => 'Left hand'),
		'right_arm_upper' => array('right_arm_upper' => 'Upper right arm'),
		'right_arm_lower' => array('right_arm_lower' => 'Lower right arm'),
		'right_hand' => array('right_hand' => 'Right hand'),
		'left_leg_upper' => array('left_leg_upper' => 'Upper left leg'),
		'left_leg_lower' => array('left_leg_lower' => 'Lower left leg'),
		'left_foot' => array('left_foot' => 'Left foot'),
		'right_leg_upper' => array('right_leg_upper' => 'Upper right leg'),
		'right_leg_lower' => array('right_leg_lower' => 'Lower right leg'),
		'right_foot' => array('right_foot' => 'Right foot'),
		'none' => array('none' => 'Total disruption'),
	),

);
