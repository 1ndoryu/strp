<?php 

class Events {
    
	public static function run() {
		$events = self::getEvents();

		foreach ($events as $event) 
		{
			if($event['data'] !== "")
				$data = json_decode($event['data'], true);
			else
				$data = null;

			call_user_func([self::class, $event['name']], $data);
			
			if($event['permanent'] == 1) 
				self::renovateEvent($event['ID_event']);
			else
				self::deleteEvent($event['ID_event']);
		}
	}

    public static function getEvents() {
		$events = rawQuerySQL("SELECT * FROM `sc_event` WHERE date <= NOW() ORDER BY date DESC");

        return $events;

	}

    public static function deleteEvent($id) {
		deleteSQL("sc_event",$w=array('ID_event'=>$id));
	}

	public static function renovateEvent($id)
	{
		rawQuerySQL("UPDATE `sc_event` SET `date` = DATE_ADD(date, INTERVAL 1 DAY) WHERE `ID_event` = $id");
	}

	public static function addEvent($name, $datetime, $data ) 
	{
		$data = json_encode($data);
		$row = array('name' => $name, 'date' => $datetime, 'data' => $data);
		insertSQL("sc_event", $row);
	}

	/*Events*/

	private static function validar($data) {
		$ads = $data['ads'];
		$nads = array();
		$limit = getConfParam('EVENTS_VALIDATE_LIMIT');
		user::insertEvent(0);
		foreach ($ads as $key => $ad) {
			if($key < $limit)
			{
				if(!validateChanges($ad))
				{
					$new_ref = getNewRef();
					$da=array('review'=>0, 'delay' => 0, "date_ad"=> time(), 'ref' => $new_ref);
					$data = getDataAd($ad);
					if($data['ad']['renovable'] == renovationType::Diario)
					{
						$limit = $data['user']['extras_limit'];
						if($limit == 0)
							$limit = time() + (60*60*24*30);
						$da['renovable_limit'] = $limit;
					}
					updateSQL("sc_ad", $da ,$w=array('ID_ad'=>$ad));
					mailNewAd($ad);
				}
			}else
				$nads[] = $ad;
		}

		if(count($nads) > 0)
		{
			self::addEvent('validar', 'NOW', array('ads' => $nads));
		}
	}

	private static function reset_delay() 
	{
		rawQuerySQL("UPDATE `sc_ad` SET `delay` = 0 WHERE `delay` > 0");
	}

}