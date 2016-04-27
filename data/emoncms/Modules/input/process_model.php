<?php
/*
 All Emoncms code is released under the GNU Affero General Public License.
 See COPYRIGHT.txt and LICENSE.txt.

 ---------------------------------------------------------------------
 Emoncms - open source energy visualisation
 Part of the OpenEnergyMonitor project:
 http://openenergymonitor.org
 */

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');


class Process
{
    private $mysqli;
    private $input;
    private $feed;
    private $log;
    private $redis;
    private $mas;
    
    private $timezoneoffset = 0;


    public static function makeProcess($mysqli,$input,$feed,$redis,$mas)
    {
		    $obj= new Process($mysqli,$input,$feed);
		
            $obj->redis = $redis;
            $obj->mas = $mas;
            return $obj;
	}

    public function __construct($mysqli,$input,$feed)
    {
            $this->mysqli = $mysqli;
            $this->input = $input;
            $this->feed = $feed;
            $this->log = new EmonLogger(__FILE__);
    }
    
    public function set_timezone_offset($timezoneoffset)
    {
        $this->timezoneoffset = $timezoneoffset;
    }

    public function get_process_list()
    {
        
        $list = array();
        
        // Note on engine selection
        
        // The engines listed against each process are the recommended engines for each process - and is only used in the input and node config GUI dropdown selectors
        // By using the create feed api and add input process its possible to create any feed type and add any process to it - this needs to be improved so that only 
        // feeds capable of using a particular processor can be used. 

        // description | Arg type | function | No. of datafields if creating feed | Datatype | Engine

        $list[1] = array(_("Log to feed"),ProcessArg::FEEDID,"log_to_feed",1,DataType::REALTIME,"Main",array(Engine::PHPFIWA,Engine::PHPFINA,Engine::PHPTIMESERIES));                  
        $list[2] = array(_("x"),ProcessArg::VALUE,"scale",0,DataType::UNDEFINED,"Calibration");                           
        $list[3] = array(_("+"),ProcessArg::VALUE,"offset",0,DataType::UNDEFINED,"Calibration");                          
        $list[4] = array(_("Power to kWh"),ProcessArg::FEEDID,"power_to_kwh",1,DataType::REALTIME,"Power",array(Engine::PHPFINA,Engine::PHPTIMESERIES));               
        $list[5] = array(_("Power to kWh/d"),ProcessArg::FEEDID,"power_to_kwhd",1,DataType::DAILY,"Power",array(Engine::PHPTIMESERIES));               
        $list[6] = array(_("x input"),ProcessArg::INPUTID,"times_input",0,DataType::UNDEFINED,"Input");                   
        $list[7] = array(_("Input on-time"),ProcessArg::FEEDID,"input_ontime",1,DataType::DAILY,"Input",array(Engine::PHPTIMESERIES));                 
        $list[8] = array(_("Wh increments to kWh/d"),ProcessArg::FEEDID,"kwhinc_to_kwhd",1,DataType::DAILY,"Power",array(Engine::PHPTIMESERIES));      
        $list[9] = array(_("kWh to kWh/d (OLD)"),ProcessArg::FEEDID,"kwh_to_kwhd_old",1,DataType::DAILY,"Deleted",array(Engine::PHPTIMESERIES));       // need to remove
        $list[10] = array(_("update feed @time"),ProcessArg::FEEDID,"update_feed_data",1,DataType::DAILY,"Input",array(Engine::MYSQL));           
        $list[11] = array(_("+ input"),ProcessArg::INPUTID,"add_input",0,DataType::UNDEFINED,"Input");                    
        $list[12] = array(_("/ input"),ProcessArg::INPUTID,"divide_input",0,DataType::UNDEFINED,"Input");                 
        $list[13] = array(_("Phaseshift"),ProcessArg::VALUE,"phaseshift",0,DataType::UNDEFINED,"Deleted");                             // need to remove
        $list[14] = array(_("Accumulator"),ProcessArg::FEEDID,"accumulator",1,DataType::REALTIME,"Misc",array(Engine::PHPFINA,Engine::PHPTIMESERIES));                 
        $list[15] = array(_("Rate of change"),ProcessArg::FEEDID,"ratechange",1,DataType::REALTIME,"Misc",array(Engine::PHPFIWA,Engine::PHPFINA,Engine::PHPTIMESERIES));               
        $list[16] = array(_("Histogram"),ProcessArg::FEEDID,"histogram",2,DataType::HISTOGRAM,"Power",array(Engine::MYSQL));                   
        $list[17] = array(_("Daily Average"),ProcessArg::FEEDID,"average",2,DataType::HISTOGRAM,"Deleted",array(Engine::PHPTIMESERIES));               // need to remove
        
        // to be reintroduced in post-processing
        $list[18] = array(_("Heat flux"),ProcessArg::FEEDID,"heat_flux",1,DataType::REALTIME,"Deleted",array(Engine::PHPFIWA,Engine::PHPFINA,Engine::PHPTIMESERIES));                  
        
        // need to remove - result can be achieved with allow_positive & power_to_kwhd
        $list[19] = array(_("Power gained to kWh/d"),ProcessArg::FEEDID,"power_acc_to_kwhd",1,DataType::DAILY,"Deleted",array(Engine::PHPTIMESERIES));              
        
        // - look into implementation that doesnt need to store the ref feed
        $list[20] = array(_("Total pulse count to pulse increment"),ProcessArg::FEEDID,"pulse_diff",1,DataType::REALTIME,"Pulse",array(Engine::PHPFINA,Engine::PHPTIMESERIES));
        
        // fixed works now with redis - look into state implementation without feed
        $list[21] = array(_("kWh to Power"),ProcessArg::FEEDID,"kwh_to_power",1,DataType::REALTIME,"Power",array(Engine::PHPFIWA,Engine::PHPFINA,Engine::PHPTIMESERIES));
        
        $list[22] = array(_("- input"),ProcessArg::INPUTID,"subtract_input",0,DataType::UNDEFINED,"Input");               
        $list[23] = array(_("kWh to kWh/d"),ProcessArg::FEEDID,"kwh_to_kwhd",2,DataType::DAILY,"Power",array(Engine::PHPTIMESERIES));                  // fixed works now with redis
        $list[24] = array(_("Allow positive"),ProcessArg::NONE,"allowpositive",0,DataType::UNDEFINED,"Limits");           
        $list[25] = array(_("Allow negative"),ProcessArg::NONE,"allownegative",0,DataType::UNDEFINED,"Limits");           
        $list[26] = array(_("Signed to unsigned"),ProcessArg::NONE,"signed2unsigned",0,DataType::UNDEFINED,"Misc");       
        $list[27] = array(_("Max value"),ProcessArg::FEEDID,"max_value",1,DataType::DAILY,"Misc",array(Engine::PHPTIMESERIES));                        
        $list[28] = array(_("Min value"),ProcessArg::FEEDID,"min_value",1,DataType::DAILY,"Misc",array(Engine::PHPTIMESERIES));  
                              
        $list[29] = array(_(" + feed"),ProcessArg::FEEDID,"add_feed",0,DataType::UNDEFINED,"Feed");        // Klaus 24.2.2014
        $list[30] = array(_(" - feed"),ProcessArg::FEEDID,"sub_feed",0,DataType::UNDEFINED,"Feed");        // Klaus 24.2.
        $list[31] = array(_(" * feed"),ProcessArg::FEEDID,"multiply_by_feed",0,DataType::UNDEFINED,"Feed");
        $list[32] = array(_(" / feed"),ProcessArg::FEEDID,"divide_by_feed",0,DataType::UNDEFINED,"Feed");
        $list[33] = array(_("Reset to ZERO"),ProcessArg::NONE,"reset2zero",0,DataType::UNDEFINED,"Misc");
        
        $list[34] = array(_("Wh Accumulator"),ProcessArg::FEEDID,"wh_accumulator",1,DataType::REALTIME,"Main",array(Engine::PHPFINA,Engine::PHPTIMESERIES));
        
        // $list[29] = array(_("save to input"),ProcessArg::INPUTID,"save_to_input",1,DataType::UNDEFINED);
		
		// CoSSMic
		$list[35] = array(_("CoSSMic Sync"),ProcessArg::INPUTID,"cossmic_sync",0,DataType::UNDEFINED,"Misc");
		$list[36] = array(_(" Min feeds"),ProcessArg::FEEDID,"min_feeds",0,DataType::UNDEFINED,"Feed");
		$list[37] = array(_("PLearn"),ProcessArg::INPUTID,"p_learn",0,DataType::UNDEFINED,"Misc");
		$list[38] = array(_("Write to input"),ProcessArg::INPUTID,"write_to_input",0,DataType::UNDEFINED,"Input");
		$list[39] = array(_("Household"),ProcessArg::NONE,"household",0,DataType::UNDEFINED,"Misc");

        return $list;
    }
	
    
    public function input($time, $value, $processList)
    {
        $this->log->info("input() received time=$time, value=$value");
           
        $process_list = $this->get_process_list();
        $pairs = explode(",",$processList);
        foreach ($pairs as $pair)
        {
            $inputprocess = explode(":", $pair);                                // Divide into process id and arg
            $processid = (int) $inputprocess[0];                                    // Process id

            $arg = 0;
            if (isset($inputprocess[1]))
                $arg = $inputprocess[1];               // Can be value or feed id

            $process_public = $process_list[$processid][2];             // get process public function name

            $value = $this->$process_public($arg,$time,$value);           // execute process public function
        }
    }

    public function get_process($id)
    {
        $list = $this->get_process_list();
        if ($id>0 && $id<count($list)+1) return $list[$id];
    }

    public function scale($arg, $time, $value)
    {
        return $value * $arg;
    }

    public function divide($arg, $time, $value)
    {
        return $value / $arg;
    }

    public function offset($arg, $time, $value)
    {
        return $value + $arg;
    }

    public function allowpositive($arg, $time, $value)
    {
        if ($value<0) $value = 0;
        return $value;
    }

    public function allownegative($arg, $time, $value)
    {
        if ($value>0) $value = 0;
        return $value;
    }

    public function reset2zero($arg, $time, $value)
     {
         $value = 0;
         return $value;
     }

    public function signed2unsigned($arg, $time, $value)
    {
        if($value < 0) $value = $value + 65536;
        return $value;
    }

    public function log_to_feed($id, $time, $value)
    {
        $this->feed->insert_data($id, $time, $time, $value);

        return $value;
    }

    //---------------------------------------------------------------------------------------
    // Times value by current value of another input
    //---------------------------------------------------------------------------------------
    public function times_input($id, $time, $value)
    {
        return $value * $this->input->get_last_value($id);
    }

    public function divide_input($id, $time, $value)
    {
        $lastval = $this->input->get_last_value($id);
        if($lastval > 0){
            return $value / $lastval;
        } else {
            return null; // should this be null for a divide by zero?
        }
    }
    
	public function update_feed_data($id, $time, $value)
	{
		$time = mktime(0, 0, 0, date("m",$time), date("d",$time), date("Y",$time));

		$feedname = "feed_".trim($id)."";
		$result = $this->mysqli->query("SELECT * FROM $feedname WHERE `time` = '$time'");
		$row = $result->fetch_array();

		if (!$row)
		{
			$this->mysqli->query("INSERT INTO $feedname (time,data) VALUES ('$time','$value')");
		}
		else
		{
			$this->mysqli->query("UPDATE $feedname SET data = '$value' WHERE `time` = '$time'");
		}
		return $value;
	} 

    public function add_input($id, $time, $value)
    {
        return $value + $this->input->get_last_value($id);
    }

    public function subtract_input($id, $time, $value)
    {
        return $value - $this->input->get_last_value($id);
    }

    //---------------------------------------------------------------------------------------
    // Power to kwh
    //---------------------------------------------------------------------------------------
    public function power_to_kwh($feedid, $time_now, $value)
    {
        $new_kwh = 0;

        // Get last value
        $last = $this->feed->get_timevalue($feedid);

        $last['time'] = strtotime($last['time']);
        if (!isset($last['value'])) $last['value'] = 0;
        $last_kwh = $last['value']*1;
        $last_time = $last['time']*1;

        // only update if last datapoint was less than 2 hour old
        // this is to reduce the effect of monitor down time on creating
        // often large kwh readings.
        if ($last_time && (time()-$last_time)<7200)
        {
            // kWh calculation
            $time_elapsed = ($time_now - $last_time);
            $kwh_inc = ($time_elapsed * $value) / 3600000.0;
            $new_kwh = $last_kwh + $kwh_inc;
        } else {
            // in the event that redis is flushed the last time will
            // likely be > 7200s ago and so kwh inc is not calculated
            // rather than enter 0 we enter the last value
            $new_kwh = $last_kwh;
        }

        $this->feed->insert_data($feedid, $time_now, $time_now, $new_kwh);

        return $value;
    }

    public function power_to_kwhd($feedid, $time_now, $value)
    {
        $new_kwh = 0;

        // Get last value
        $last = $this->feed->get_timevalue($feedid);

        $last['time'] = strtotime($last['time']);
        if (!isset($last['value'])) $last['value'] = 0;
        $last_kwh = $last['value']*1;
        $last_time = $last['time']*1;
        
        //$current_slot = floor($time_now / 86400) * 86400;
        //$last_slot = floor($last_time / 86400) * 86400;
        $current_slot = $this->getstartday($time_now);
        $last_slot = $this->getstartday($last_time);    

        if ($last_time && ((time()-$last_time)<7200)) {
            // kWh calculation
            $time_elapsed = ($time_now - $last_time);
            $kwh_inc = ($time_elapsed * $value) / 3600000.0;
        } else {
            // in the event that redis is flushed the last time will
            // likely be > 7200s ago and so kwh inc is not calculated
            // rather than enter 0 we dont increase it
            $kwh_inc = 0;
        }
        
        if($last_slot == $current_slot) {
            $new_kwh = $last_kwh + $kwh_inc;
        } else {
            # We are working in a new slot (new day) so don't increment it with the data from yesterday
            $new_kwh = $kwh_inc;
        }
        
        $this->feed->update_data($feedid, $time_now, $current_slot, $new_kwh);

        return $value;
    }


    public function kwh_to_kwhd($feedid, $time_now, $value)
    {
        global $redis;
        if (!$redis) return $value; // return if redis is not available
        
        $currentkwhd = $this->feed->get_timevalue($feedid);
        $last_time = strtotime($currentkwhd['time']);
        
        //$current_slot = floor($time_now / 86400) * 86400;
        //$last_slot = floor($last_time / 86400) * 86400;
        $current_slot = $this->getstartday($time_now);
        $last_slot = $this->getstartday($last_time);

        if ($redis->exists("process:kwhtokwhd:$feedid")) {
            $lastkwhvalue = $redis->hmget("process:kwhtokwhd:$feedid",array('time','value'));
            $kwhinc = $value - $lastkwhvalue['value'];

            // kwh values should always be increasing so ignore ones that are less
            // assume they are errors
            if ($kwhinc<0) { $kwhinc = 0; $value = $lastkwhvalue['value']; }
            
            if($last_slot == $current_slot) {
                $new_kwh = $currentkwhd['value'] + $kwhinc;
            } else {
                $new_kwh = $kwhinc;
            }

            $this->feed->update_data($feedid, $time_now, $current_slot, $new_kwh);
        }
        
        $redis->hMset("process:kwhtokwhd:$feedid", array('time' => $time_now, 'value' => $value));

        return $value;
    }

    //---------------------------------------------------------------------------------------
    // input on-time counter
    //---------------------------------------------------------------------------------------
    public function input_ontime($feedid, $time_now, $value)
    {
        // Get last value
        $last = $this->feed->get_timevalue($feedid);
        $last_time = strtotime($last['time']);
        
        //$current_slot = floor($time_now / 86400) * 86400;
        //$last_slot = floor($last_time / 86400) * 86400;
        $current_slot = $this->getstartday($time_now);
        $last_slot = $this->getstartday($last_time);
        
        if (!isset($last['value'])) $last['value'] = 0;
        $ontime = $last['value'];
        $time_elapsed = 0;
        
        if ($value > 0 && (($time_now-$last_time)<7200))
        {
            $time_elapsed = $time_now - $last_time;
            $ontime += $time_elapsed;
        }
        
        if($last_slot != $current_slot) $ontime = $time_elapsed;

        $this->feed->update_data($feedid, $time_now, $current_slot, $ontime);

        return $value;
    }

    //--------------------------------------------------------------------------------
    // Display the rate of change for the current and last entry
    //--------------------------------------------------------------------------------
    public function ratechange($feedid, $time, $value)
    {
        global $redis;
        if (!$redis) return $value; // return if redis is not available
        
        if ($redis->exists("process:ratechange:$feedid")) {
            $lastvalue = $redis->hmget("process:ratechange:$feedid",array('time','value'));
            $ratechange = $value - $lastvalue['value'];
            $this->feed->insert_data($feedid, $time, $time, $ratechange);
        }
        $redis->hMset("process:ratechange:$feedid", array('time' => $time, 'value' => $value));

        // return $ratechange;
    }

    public function save_to_input($inputid, $time, $value)
    {
        $this->input->set_timevalue($inputid, $time, $value);
        return $value;
    }

    public function kwhinc_to_kwhd($feedid, $time_now, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        $last_time = strtotime($last['time']);
        
        //$current_slot = floor($time_now / 86400) * 86400;
        //$last_slot = floor($last_time / 86400) * 86400;
        $current_slot = $this->getstartday($time_now);
        $last_slot = $this->getstartday($last_time);
               
        $new_kwh = $last['value'] + ($value / 1000.0);
        if ($last_slot != $current_slot) $new_kwh = ($value / 1000.0);
        
        $this->feed->update_data($feedid, $time_now, $current_slot, $new_kwh);

        return $value;
    }

    public function accumulator($feedid, $time, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        $value = $last['value'] + $value;
        $this->feed->insert_data($feedid, $time, $time, $value);
        return $value;
    }
    /*
    public function accumulator_daily($feedid, $time_now, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        $value = $last['value'] + $value;
        $feedtime = $this->getstartday($time_now);
        $this->feed->update_data($feedid, $time_now, $feedtime, $value);
        return $value;
    }*/

    //---------------------------------------------------------------------------------
    // This method converts power to energy vs power (Histogram)
    //---------------------------------------------------------------------------------
    public function histogram($feedid, $time_now, $value)
    {

        ///return $value;

        $feedname = "feed_" . trim($feedid) . "";
        $new_kwh = 0;
        // Allocate power values into pots of varying sizes
        if ($value < 500) {
            $pot = 50;

        } elseif ($value < 2000) {
            $pot = 100;

        } else {
            $pot = 500;
        }

        $new_value = round($value / $pot, 0, PHP_ROUND_HALF_UP) * $pot;

        $time = $this->getstartday($time_now);

        // Get the last time
        $lastvalue = $this->feed->get_timevalue($feedid);
        $last_time = strtotime($lastvalue['time']);

        // kWh calculation
        if ((time()-$last_time)<7200) {
            $time_elapsed = ($time_now - $last_time);
            $kwh_inc = ($time_elapsed * $value) / 3600000;
        } else {
            $kwh_inc = 0;
        }

        // Get last value
        $result = $this->mysqli->query("SELECT * FROM $feedname WHERE time = '$time' AND data2 = '$new_value'");

        if (!$result) return $value;

        $last_row = $result->fetch_array();

        if (!$last_row)
        {
            $result = $this->mysqli->query("INSERT INTO $feedname (time,data,data2) VALUES ('$time','0.0','$new_value')");

            $this->feed->set_timevalue($feedid, $new_value, $time_now);
            $new_kwh = $kwh_inc;
        }
        else
        {
            $last_kwh = $last_row['data'];
            $new_kwh = $last_kwh + $kwh_inc;
        }

        // update kwhd feed
        $this->mysqli->query("UPDATE $feedname SET data = '$new_kwh' WHERE time = '$time' AND data2 = '$new_value'");

        $this->feed->set_timevalue($feedid, $new_value, $time_now);
        return $value;
    }

    public function pulse_diff($feedid,$time_now,$value)
    {
        $value = $this->signed2unsigned(false,false, $value);

        if($value>0)
        {
            $pulse_diff = 0;
            $last = $this->feed->get_timevalue($feedid);
            $last['time'] = strtotime($last['time']);
            if ($last['time']) {
                // Need to handle resets of the pulse value (and negative 2**15?)
                if ($value >= $last['value']) {
                    $pulse_diff = $value - $last['value'];
                } else {
                    $pulse_diff = $value;
                }
            }

            // Save to allow next difference calc.
            $this->feed->insert_data($feedid,$time_now,$time_now,$value);

            return $pulse_diff;
        }
    }

    public function kwh_to_power($feedid,$time,$value)
    {
        global $redis;
        if (!$redis) return $value; // return if redis is not available
        
        if ($redis->exists("process:kwhtopower:$feedid")) {
            $lastvalue = $redis->hmget("process:kwhtopower:$feedid",array('time','value'));
            $kwhinc = $value - $lastvalue['value'];
            $joules = $kwhinc * 3600000.0;
            $timeelapsed = ($time - $lastvalue['time']);
            $power = $joules / $timeelapsed;
            $this->feed->insert_data($feedid, $time, $time, $power);
        }
        $redis->hMset("process:kwhtopower:$feedid", array('time' => $time, 'value' => $value));

        return $power;
    }

    public function max_value($feedid, $time_now, $value)
    {
        // Get last values
        $last = $this->feed->get_timevalue($feedid);
        $last_val = $last['value'];
        $last_time = strtotime($last['time']);
        $feedtime = $this->getstartday($time_now);
        $time_check = $this->getstartday($last_time);

        // Runs on setup and midnight to reset current value - (otherwise db sets 0 as new max)
        if ($time_check != $feedtime) {
            $this->feed->insert_data($feedid, $time_now, $feedtime, $value);
        } else {
            if ($value > $last_val) $this->feed->update_data($feedid, $time_now, $feedtime, $value);
        }
        return $value;
    }

    public function min_value($feedid, $time_now, $value)
    {
        // Get last values
        $last = $this->feed->get_timevalue($feedid);
        $last_val = $last['value'];
        $last_time = strtotime($last['time']);
        $feedtime = $this->getstartday($time_now);
        $time_check = $this->getstartday($last_time);

        // Runs on setup and midnight to reset current value - (otherwise db sets 0 as new min)
        if ($time_check != $feedtime) {
            $this->feed->insert_data($feedid, $time_now, $feedtime, $value);
        } else {
            if ($value < $last_val) $this->feed->update_data($feedid, $time_now, $feedtime, $value);
        }
        return $value;

    }
    
    public function add_feed($feedid, $time, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        $value = $last['value'] + $value;
        return $value;
    }
    
      public function min_feeds($feedid, $time, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        if($value<0 or $value > $last['value']) 
			return $last['value'];
        else return $value;
    }

    public function sub_feed($feedid, $time, $value)
    {
        $last  = $this->feed->get_timevalue($feedid);
        $myvar = $last['value'] *1;
        return $value - $myvar;
    }
    
    public function multiply_by_feed($feedid, $time, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        $value = $last['value'] * $value;
        return $value;
    }

   public function divide_by_feed($feedid, $time, $value)
    {
        $last  = $this->feed->get_timevalue($feedid);
        $myvar = $last['value'] *1;
        
        if ($myvar!=0) {
            return $value / $myvar;
        } else {
            return 0;
        }
    }
    
    public function wh_accumulator($feedid, $time, $value)
    {
        $max_power = 25000;
        $totalwh = $value;
        
        global $redis;
        if (!$redis) return $value; // return if redis is not available

        if ($redis->exists("process:whaccumulator:$feedid")) {
            $last_input = $redis->hmget("process:whaccumulator:$feedid",array('time','value'));
    
            $last_feed  = $this->feed->get_timevalue($feedid);
            $totalwh = $last_feed['value'];
            
            $time_diff = $time - $last_feed['time'];
            $val_diff = $value - $last_input['value'];
            
            $power = ($val_diff * 3600) / $time_diff;
            
            if ($val_diff>0 && $power<$max_power) $totalwh += $val_diff;
            
            $this->feed->insert_data($feedid, $time, $time, $totalwh);
            
        }
        $redis->hMset("process:whaccumulator:$feedid", array('time' => $time, 'value' => $value));

        return $totalwh;
    }

    // No longer used
    public function average($feedid, $time_now, $value) { return $value; } // needs re-implementing    
    public function phaseshift($id, $time, $value) { return $value; }
    public function kwh_to_kwhd_old($feedid, $time_now, $value) { return $value; }
    public function power_acc_to_kwhd($feedid,$time_now,$value) { return $value; } // Process can now be achieved with allow positive process before power to kwhd

    //------------------------------------------------------------------------------------------------------
    // Calculate the energy used to heat up water based on the rate of change for the current and a previous temperature reading
    // See http://harizanov.com/2012/05/measuring-the-solar-yield/ for more info on how to use it
    //------------------------------------------------------------------------------------------------------
    public function heat_flux($feedid,$time_now,$value) { return $value; } // Removed to be reintroduced as a post-processing based visualisation calculated on the fly.
    
    // Get the start of the day
    private function getstartday($time_now)
    {
        // $midnight  = mktime(0, 0, 0, date("m",$time_now), date("d",$time_now), date("Y",$time_now)) - ($this->timezoneoffset * 3600);
        // $this->log->warn($midnight." ".date("Y-n-j H:i:s",$midnight)." [".$this->timezoneoffset."]");
        return mktime(0, 0, 0, date("m",$time_now), date("d",$time_now), date("Y",$time_now)) - ($this->timezoneoffset * 3600);
    }

	
	public function cossmic_sync($arg, $time, $value)
    {
		global $cossmic;
		
		$input = $this->input->get_name($arg);
		
		if ($input) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $cossmic['host'].'input/post.json?json={'.$input.':'.trim($value).'}&time='.$time.'&node='.$cossmic['node'].'&apikey='.$cossmic['apikey']);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$return = curl_exec($ch);
			curl_close($ch);
		}
		
		return $value;
    }

	
    public function p_learn($id, $time, $value)
	{
		//echo "learn";
		$power=0;
		$submitload=0;
		$updateprofile=0;
		
		
		
		if($this->redis)
		{
			//get automata(id);
			//echo "id:".$id."\n";
			if (!$this->redis->exists("profiles:$id"))
			{
					$automata = Array();
					$automata["status"]=0;
					$automata["profile_type"]="periodic";
					$automata["user"]="unknown";
					$automata["mode"]=0;
					$automata["available"]=0;
					
					$proclist=$this->input->get_processlist($id);     
					//echo $proclist;
					$temp=explode(",",$proclist);
				 $i=0;
				 $type='unknown';
				 while($i<count($temp))
				 {
					 
					$proc=explode(":",$temp[$i]);
					//echo $proc[0];
					if($proc[0]==4)
					  {
						  $automata["type"]="power";
						  $feedname=$this->feed->get_field($proc[1],'name');
						  $devicename= strstr($feedname,"_",true);
						  $automata["device"]=substr($devicename,6);
						  $i=count($temp);
						  $power=$value;
						  
						  }
					elseif($proc[0]==21 /*or $proc[0]==5*/)
					{
						
						  $automata["type"]="energy";
						  $feedname=$this->feed->get_field($proc[1],'name');
						  $devicename= strstr($feedname,"_",true);
						  $automata["device"]=substr($devicename,6);
						  $i=count($temp);
						  $automata["power"]=0;
						  $automata["end"]=$time;
						  $automata["lastvalue"]=$value;
						  $power=0;	
						}
					   $i=$i+1;
					 }
					if(array_key_exists("device", $automata))	
						    $automata["filename"] = "u1d".$automata["device"]."m".$automata["mode"].".prof";
					else 
						{
							$automata["filename"] = "u1d".$id."m".$automata["mode"].".prof";
							$automata["device_type"] = "input";
						}
					
					
				}
			else
				{
					$automata = $this->redis->hGetAll("profiles:$id");
					
					if (array_key_exists("type", $automata))
					  if($automata["type"]=="energy")
						  {
							  
							  $power=($value-$automata["lastvalue"])/($time-$automata["end"]);
							  $automata["lastvalue"]=$value;
							  
						  }
					else 
					   $power=$value;
						  
				}
				   
			//aggiungo ciclo
			
			if($automata["status"]==0 and  $power>0)
			{
				$automata["status"]=1;
				$automata["start"]=$time;
				$automata["end"]=$time;			
				$automata["lastvalue"]=$value;					
				$automata["power"]= $power;
				$automata["samples"]=1;
				$automata["ciclo"]=0;
				$automata["averageperiod"]=0;
				$automata["submittedload"]=0;
				}
			else if($automata["status"]==1 and  $power>0)
			{
				
				$automata["power"]=($automata["power"]*($automata["end"]-$automata["start"])+ $power*($time-$automata["end"]))/($time-$automata["start"]);
				$automata["end"]=$time;
				$automata["samples"]=$automata["samples"]+1;

			}
			else if($automata["status"]==1   and  $power==0)
			{
				$automata["status"]=2;
				$automata["duration"]=$time-$automata["start"];
				$automata["samples"]=$automata["samples"]+1;
				$automata["ciclo"]=$automata["ciclo"]+1;
				$automata["averageperiod"]=($automata["averageperiod"]*$automata["ciclo"]+$automata["duration"])/($automata["ciclo"]+1);
				$jsonString = "{'status' : 5}";		 
				$response= $this->mas->update($automata["submittedload"],$jsonString);
							
				$automata["submittedload"]=0;
				$updateprofile=1;
				$automata["available"]=1;
				}	
				
			else if($automata["status"]==2 and $power>0)
			{
				$automata["status"]=1;
				$automata["start"]=$time;
				$automata["power"]= $power;
				$automata["end"]=$time;
				$automata["samples"]=1;
				$submitload=1;
				}
				
		 	
			$this->redis->hMSet("profiles:$id",$automata);	
			

			//error_log(var_dump($automata));
			}
	
	//se e quando negoziare
	$tneg=0;
	
	//tneg è il tempo necessario x la negoziazione
	$dt = new DateTime("@$time");
	$est = $dt->format('Y-m-d H:i');
	//error_log("est:".$est);
	
	
	if($updateprofile)
	{
		
		
	   $temp=$automata["averageperiod"];	
	   $file = "/var/www/emoncms/profiles/".$automata["filename"];

		
		// apre il file, se non esiste lo crea
		$fp = fopen($file,"w");

		$temp=$temp-$tneg;
		// inserisce i valori ricevuti dal form in coda al file
		fputs($fp, $temp."\n0\n0\n". $automata["power"]."\n0\n");
		
		// chiude il file
		fclose($fp);
        //raw version for current implementation of taskscheduler
       $file =$file."_raw";
       $fp = fopen($file,"w");
       $energy = $automata["power"]*$temp;
		fputs($fp, "0 0\n".$temp." ". $energy);
       fclose($fp);
		}
	
	else if($submitload)
	 {
		 $temp=$automata["averageperiod"];//-$time+$automata["start"];
		 if($temp>$tneg && !$automata["submittedload"] )	
	     //modifico il power che mi serve considerando anche il tneg
		 {		
			
			
				//echo "sending load".$temp."\n0\n0\n". $automata["power"]."\n0\n";
				if($this->mas)
				  {
					$lst = round($time+$temp); //cambiare con $tneg
					$dt = new DateTime("@$lst");
					$lst=$dt->format('Y-m-d H:i');
					$dt = new DateTime("@$time");
					$est = $dt->format('Y-m-d H:i');
					$jsonString = "{'EST': '".$est."','LST': '".$lst."','deviceID': '".$automata["device"]."','execution_type':'periodic_run', 'mode' : 0}";		 
					$response= $this->mas->add(null, $jsonString); 
					if($response["result"]==="success")
					 {
					   $automata["submittedload"] = $response["taskID"];
					   $this->redis->hMSet("profiles:$id",$automata);	 	  
					 }
					}
					   
					  
				
				
			}
		}
				return $value;
				
	
	
	}	
	
	
	public function write_to_input($arg, $time, $value)
	{
		$this->input->set_timevalue($arg, $time, $value);
		if ($this->input->get_processlist($arg)) $this->input($time, $value, $this->input->get_processlist($arg));
		return $value;
	}
	
	private function write_household_input($input, $time, $value) {
		// Extract inputs log process from string, to avoid SQL query
		$processes = explode(',', $input['processList']);
		foreach ($processes as &$process) {
			$ids = explode(':', $process);
			// Look for log process id 1
			if ($ids[0] == '1') {
            	$last_value = $this->feed->get_timevalue($ids[1]);
				$this->write_to_input($input['id'], $time, $last_value['value'] + $value);
				break;
			}
		}
	}
	
	public function household($arg, $start, $value)
	{
		global $cossmic, $session;
		
		$userid = $session['userid'];

		$household_inputs = $this->input->get_inputs($userid)[0];		
		
		// get metered input values
		$grid_in = $this->input->get_timevalue($household_inputs['grid_in']['id']);
		$grid_out = $this->input->get_timevalue($household_inputs['grid_out']['id']);
		$storage_in = $this->input->get_timevalue($household_inputs['storage_in']['id']);
		$storage_out = $this->input->get_timevalue($household_inputs['storage_out']['id']);
		$pv = $this->input->get_timevalue($household_inputs['pv']['id']);
		
		
		$last = $this->redis->hMget("household:$userid", array('time', 'grid_in', 'grid_out', 'storage_in', 'storage_out', 'pv'));
				
		// only calculate energy flows, if all relevant metered values got updated
		if ($grid_out['time'] > $last['time'] and 
				($grid_in['time'] > $last['time'] or empty($grid_in['time'])) and
				($storage_out['time'] > $last['time'] or empty($storage_out['time'])) and
				($storage_in['time'] > $last['time'] or empty($storage_in['time'])) and
				($pv['time'] > $last['time'] or empty($pv['time']))) {

			$start = time();
			
			// Calculate differences
			$grid_out_diff = $grid_out['value'] - $last['grid_out'];
			$grid_in_diff = empty($grid_in['time']) ? 0 : $grid_in['value'] - $last['grid_in'];
			$storage_out_diff = empty($storage_out['time']) ? 0 : $storage_out['value'] - $last['storage_out'];
			$storage_in_diff = empty($storage_in['time']) ? 0 : $storage_in['value'] - $last['storage_in'];
			$pv_diff = empty($pv['time']) ? 0 : $pv['value'] - $last['pv'];
			
			if ($pv_diff > 0) {
				$time = $pv['time'];
				
				if ($cossmic['household_selfconsumption']) {
					// Generated energy will be consumed in the household first
					// and the grid feed-in gets metered as grid_in
					$pv2grid = $grid_in_diff;
					$pv2household = $pv_diff - $grid_in_diff;
					$grid2household = $grid_out_diff;
				}
				else {
					// Generated energy will always be fed in the grid.
					// Self-consumed pv production is part of grid_out
					if ($pv_diff >= $grid_out_diff) {
						$pv2grid = $pv_diff - $grid_out_diff;
						$pv2household = $grid_out_diff;
						$grid2household = 0;
					}
					else {
						$pv2grid = 0;
						$pv2household = $pv_diff;
						$grid2household = $grid_out_diff - $pv_diff;
					}
				}
				
				if ($storage_in_diff > 0) {
					
					// Storage systems will preferably be charged with pv energy
					if ($storage_in_diff > $pv2household) {
						// Battery gets charged more, than available through pv
						$diff = $storage_in_diff - $pv2household;
						
						$grid2household -= $diff;
						$grid2storage = $diff;

						$pv2storage = $pv2household;
						$pv2household = 0;
					}
					else {
						$grid2storage = 0;

						$pv2storage = $storage_in_diff;
						$pv2household -= $storage_in_diff;
					}
					
					$storage2household = 0;
					$storage2grid = 0;
				}
				else if ($storage_out_diff > 0) {
					
					// Battery gets decharged to feed-in the grid, additionally to PV
					if ($grid_in_diff > 0 and $pv2household < 0 ) {
						$storage2grid = abs($pv2household);
						$pv2household = 0;
					}
					else {
						$storage2grid = 0;
					}
					
					$storage2household = $storage_out_diff - $storage2grid;
					
					$pv2storage = 0;
					$grid2storage = 0;
				}
				else {
					$pv2storage = 0;
					$grid2storage = 0;
					$storage2household = 0;
					$storage2grid = 0;
				}
				
				
				if ($pv2household < 0) {
					$this->log->warn("Household: Offset for pv2household=$pv2household");
				}
				if ($pv_diff != $pv2grid + $pv2household + $pv2storage) {
					$this->log->warn("Household: Mismatch calculation for PV unequal pv2grid, pv2household, pv2storage");
				}
			}
			else {
				$time = $grid_out['time'];
				
				$pv2grid = 0;
				$pv2household = 0;
				$pv2storage = 0;

				$grid2household = $grid_out_diff;
								
				if ($storage_in_diff > 0) {
					$grid2storage = $storage_in_diff;
					$grid2household -= $grid2storage;
					
					$storage2household = 0;
					$storage2grid = 0;
				}
				else if ($storage_out_diff > 0) {
					$storage2grid = $grid_in_diff;
					$storage2household = $storage_out_diff - $storage2grid;
						
					$pv2storage = 0;
					$grid2storage = 0;
				}
				else {
					$grid2storage = 0;
					$storage2household = 0;
					$storage2grid = 0;
				}
			}
			
			// Write values only, if corresponding metered inputs were updated
			if (!empty($pv['time'])) {
				$this->write_household_input($household_inputs['pv2grid'], $time, $pv2grid);
				$this->write_household_input($household_inputs['pv2household'], $time, $pv2household);
				$this->write_household_input($household_inputs['pv2storage'], $time, $pv2storage);
			}
			if (!empty($storage_out['time'])) {
				$this->write_household_input($household_inputs['storage2grid'], $time, $storage2grid);
				$this->write_household_input($household_inputs['storage2household'], $time, $storage2household);
			}
			if (!empty($storage_in['time'])) {
				$this->write_household_input($household_inputs['grid2storage'], $time, $grid2storage);
			}
			$this->write_household_input($household_inputs['grid2household'], $time, $grid2household);
			
			$consumption = $grid2household + $pv2household + $storage2household;
			$this->write_household_input($household_inputs['consumption'], $time, $consumption);
			
			
			// Keep input values for next execution
			$this->redis->hMset("household:$userid", 
					array('time' => $start, 
							'grid_in' => $grid_in['value'], 
							'grid_out' => $grid_out['value'], 
							'storage_in' => $storage_in['value'], 
							'storage_out' => $storage_out['value'], 
							'pv' => $pv['value']));
		}
		
		return $value;
	}
}
