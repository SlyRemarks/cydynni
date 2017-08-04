<?php
/*
  1. Sum of all generation from the latest reading date.
  2. Generation values for the last two half-hours from the latest reading date.
  3. Minimum, maximum & average of all generation values from the latest reading date.
  4. Half-hourly data from the generation site for the latest day of data received.
  5. Same as above (under development)
  6. Domestic user’s gross consumption within the 4 defined tariff periods and their daily total.
  7. Domestic user’s net consumption following the allocation of generation output through the sharing algorithm
  8. Domestic user’s net charge to be billed.
  9. Equal share of generation per tariff period (generation/number of demand premises)
  10. Domestic user’s half-hourly demand for the latest 24 hour period.
  11. Community half-hourly demand for the latest 24 hour period.
  12. Not in use
  13. Community aggregate gross demand in each tariff period for the latest 24 hour period.
  14. Community aggregate net demand (after sharing algorithm) in each tariff period for the latest 24 hour period.
  15. Community aggregate demand provided by generation (after sharing algorithm) in each tariff period for the latest 24 hour period.
  16. Community data: generation offset, off-peak demand, other demand for latest 24-hour period (N.B. this data does not look right).
  17. Domestic User’s demand offset by generation for latest 24-hour period.
  18. User’s Monthly kWh import total.
  19. User’s Monthly kWh import allocated to hydro.
  20. User’s Monthly kWh import provided by supplier.
  21. User’s Monthly total cost of import.
  22. Community Monthly kWh import total.
  23. Community Monthly kWh import allocated to hydro.
  24. Community Monthly kWh import provided by supplier.
  25. Community Monthly total cost of import.
  26  Household historic daily summaries
  27. Household historic meter data
  28. Hydro history
  29. Community history
  30. Demand shaper signal
  31. User list
*/

function get_meter_data($baseurl,$token,$rid) {

    // Fetch data from data server
    $str = @file_get_contents($baseurl."1-$token-$rid");
    // print $str;
    // Decode JSON result remove present // at start of message.
    $result = json_decode(substr($str,2));
    
    // if json failed to decode return blank array
    if ($result==null) return array();

    if (count($result->DATA)==0) return array();

    $date = $result->DATA[0][0];
    $midnightstart = decode_date($date);

    $data = array();

    foreach ($result->DATA as $row) {
        if ($row[1]!=null) {
            $time = $midnightstart + (($row[1]-1) * 1800);
            // print $time." ".$row[2]."\n";
            $data[] = array($time*1000,(1*$row[2]));
        }
    }
    
    return $data;
}

function get_meter_data_history($baseurl,$token,$rid,$start,$end) {

    $start = time_to_date((int) $start*0.001);
    $end = time_to_date((int) $end*0.001);
   
    // Fetch data from data server
    $str = @file_get_contents($baseurl."1-$token-$rid?dateStart=$start&dateEnd=$end");
    
    // Decode JSON result remove present // at start of message.
    $result = json_decode(substr($str,2));
    
    // if json failed to decode return blank array
    if ($result==null) return array();
    if (count($result->DATA)==0) return array();

    $days = count($result->DATA); 
    $data = array();
    for ($day=0; $day<$days; $day++) 
    {
        $date = $result->DATA[$day][0];
        $midnightstart = decode_date($date);

        $hh = 0;
        for ($i=1; $i<count($result->DATA[$day]); $i++) {
            if ($hh<48) {
                $time = $midnightstart + ($hh * 1800);
                $value = $result->DATA[$day][$i];
                $data[] = array($time*1000,1*$value);
                $hh++;
            }
        }
    }
    
    return $data;
}

// -------------------------------------------------------------
// Last day household consumption summary
// -------------------------------------------------------------
function get_household_consumption($baseurl,$token) {

    // 1. Fetch gross community demand in each tariff period and the total
    if (!$gross = get_latest_day($baseurl,$token,6)) return "Invalid data";
    // 2. Fetch net/imported community demand in each tariff period and the total
    if (!$imported = get_latest_day($baseurl,$token,7)) return "Invalid data";
    // 3. Fetch net charge to be billed
    if (!$cost = get_latest_day($baseurl,$token,8)) return "Invalid data";
    
    $date1 = $gross["date"]; unset($gross["date"]);
    $date2 = $imported["date"]; unset($imported["date"]);
    $date3 = $cost["date"]; unset($cost["date"]);
    // Check that dates of latest day match
    if ($date1!=$date2) return "Date mismatch";
    if ($date1!=$date3) return "Date mismatch";
    
    // Build import + hydro consumption object
    // 1. start with imported data
    $kwh = $imported;
    // 2. hydro consumption = total - imported
    $kwh["hydro"] = $gross["total"] - $imported["total"];
    $kwh["total"] = $gross["total"];
   
    // -------------------------------------------------------------    
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone("Europe/London"));
    $date->setTimestamp(time());
    $date->modify("midnight");
    $time = $date->getTimestamp();
    
    $dayoffset = ($time - decode_date($date1))/(3600*24);
    
    $date1 = str_replace(",","",$date1);
    $date_parts = explode(" ",$date1);
    
    return array("kwh"=>$kwh,"cost"=>$cost,"month"=>$date_parts[0],"day"=>$date_parts[1],"dayoffset"=>$dayoffset);
}

// -------------------------------------------------------------
// Last day community consumption summary
// -------------------------------------------------------------
function get_community_consumption($baseurl,$token) {

    // 1. Fetch gross community demand in each tariff period and the total
    if (!$gross = get_latest_day($baseurl,$token,13)) return "Invalid data";
    // 2. Fetch net/imported community demand in each tariff period and the total
    if (!$imported = get_latest_day($baseurl,$token,14)) return "Invalid data";
    // 3. Fetch net charge to be billed
    if (!$cost = get_latest_day($baseurl,$token,15)) return "Invalid data";
    
    $date1 = $gross["date"]; unset($gross["date"]);
    $date2 = $imported["date"]; unset($imported["date"]);
    $date3 = $cost["date"]; unset($cost["date"]);
    // Check that dates of latest day match
    if ($date1!=$date2) return "Date mismatch";
    if ($date1!=$date3) return "Date mismatch";
    
    // Build import + hydro consumption object
    // 1. start with imported data
    $kwh = $imported;
    // 2. hydro consumption = total - imported
    $kwh["hydro"] = $gross["total"] - $imported["total"];
    $kwh["total"] = $gross["total"];
   
    // -------------------------------------------------------------    
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone("Europe/London"));
    $date->setTimestamp(time());
    $date->modify("midnight");
    $time = $date->getTimestamp();
    
    $dayoffset = ($time - decode_date($date1))/(3600*24);
    
    $date1 = str_replace(",","",$date1);
    $date_parts = explode(" ",$date1);
    
    return array("kwh"=>$kwh,"cost"=>$cost,"month"=>$date_parts[0],"day"=>$date_parts[1],"dayoffset"=>$dayoffset);
}

// -------------------------------------------------------------
// Used by the above functions to fetch the last day
// -------------------------------------------------------------
function get_latest_day($baseurl,$token,$api) {

    // Fetch data from data server
    $str = @file_get_contents($baseurl."1-$token-$api");
    
    // Decode JSON result remove present // at start of message.
    $result = json_decode(substr($str,2));
    
    // if json failed to decode return blank array
    if ($result==null) return false;
    if (!isset($result->DATA)) return false;
    if (!isset($result->DATA[0])) return false;

    // Scan through result for latest day (order returned is not always correct)
    $latest = 0; $latest_index = 0;
    for ($i=0; $i<count($result->DATA); $i++) {
        $timestamp = decode_date($result->DATA[$i][0]);
        if ($timestamp>$latest) { $latest = $timestamp; $latest_index = $i; }
    }
    
    $day = $result->DATA[$latest_index];
    return array("date"=>$day[0], "morning"=>$day[1], "midday"=>$day[2], "evening"=>$day[3], "overnight"=>$day[4], "total"=>$day[5]);
}

// -------------------------------------------------------------
// Convert date of form: November, 02 2016 00:00:00 to unix timestamp
// -------------------------------------------------------------
function decode_date($datestr) {
    $datestr = str_replace(",","",$datestr);
    $date_parts = explode(" ",$datestr);
    if (count($date_parts)!=4) return "invalid date string";
    $date2 = $date_parts[1]." ".$date_parts[0]." ".$date_parts[2];
    
    $day = $date_parts[1];
    $month = $date_parts[0];
    $year = $date_parts[2];
    
    $months = array("January"=>1,"February"=>2,"March"=>3,"April"=>4,"May"=>5,"June"=>6,"July"=>7,"August"=>8,"September"=>9,"October"=>10,"November"=>11,"December"=>12);
    
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone("Europe/London"));
    $date->setDate($year,$months[$month],$day);
    $date->setTime(0,0,0);
    
    //$date->modify("midnight");
    $time = $date->getTimestamp();
    // November, 02 2016 00:00:00
    // print $date2."\n";
    // Mid night start of day
    return $time; //strtotime($date2);
}

// -------------------------------------------------------------
// Convert unix timestamp to date format 01-Jul-2017
// -------------------------------------------------------------
function time_to_date($time) {
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone("Europe/London"));
    $date->setTimestamp($time);
    $year = $date->format('Y');
    $month = $date->format('F');
    $day = $date->format('d');
    return "$day-$month-$year";
}

// -------------------------------------------------------------
// Monthly household consumption for report
// -------------------------------------------------------------
function get_household_consumption_monthly($baseurl,$token) {

    $month = "JUN";
    $months = array("JAN"=>1,"FEB"=>2,"MAR"=>3,"APR"=>4,"MAY"=>5,"JUN"=>6,"JUL"=>7,"AUG"=>8,"SEP"=>9,"OCT"=>10,"NOV"=>11,"DEC"=>12);
    
    // API: 18 (User’s Monthly kWh import total)
    // "COLUMNS":["PERIOD1","PERIOD2","PERIOD3","PERIOD4","TOTAL","MONTH","MONTHDESC","YEAR","DAYSINMONTH"],
    // "DATA":[[41.8,54.1,81.1,103.9,280.9,1,"JAN",2017,31],
    //         [36.9,60.8,73.7,96.8,268.2,12,"DEC",2016,31]]
    $str = @file_get_contents($baseurl."1-$token-18");
    $result18 = json_decode(substr($str,2));
    if ($result18==null) return "Invalid data";
    if (!isset($result18->DATA)) return "Invalid data";
    if (!isset($result18->DATA[0])) return "Invalid data";
    
    // API: 19 (User’s Monthly kWh import allocated to hydro)
    // "COLUMNS":["PERIOD1","PERIOD2","PERIOD3","PERIOD4","TOTAL","MONTH","MONTHDESC","YEAR","DAYSINMONTH"],
    // "DATA":[[33.43,41.96,46.82,78.57,200.78,1,"JAN",2017,31],
    //         [12.18,20.57,12.11,30.64,75.5,12,"DEC",2016,31]]
    $str = @file_get_contents($baseurl."1-$token-19");
    $result19 = json_decode(substr($str,2));
    if ($result19==null) return "Invalid data";
    if (!isset($result19->DATA)) return "Invalid data";
    if (!isset($result19->DATA[0])) return "Invalid data";
    
    // API: 20 (User’s Monthly kWh import provided by supplier)
    // "COLUMNS":["PERIOD1","PERIOD2","PERIOD3","PERIOD4","TOTAL","MONTH","MONTHDESC","YEAR","DAYSINMONTH"],
    // "DATA":[[8.37,12.14,34.28,25.33,80.12,1,"JAN",2017,31],
    //         [24.72,40.23,61.59,66.16,192.7,12,"DEC",2016,31]] 
    $str = @file_get_contents($baseurl."1-$token-20");
    $result20 = json_decode(substr($str,2));
    if ($result20==null) return "Invalid data";
    if (!isset($result20->DATA)) return "Invalid data";
    if (!isset($result20->DATA[0])) return "Invalid data";

    // API: 21 (User’s Monthly total cost of import)
    // "COLUMNS":["PERIOD1","PERIOD2","PERIOD3","PERIOD4","TOTAL","MONTH","MONTHDESC","YEAR","DAYSINMONTH"],
    // "DATA":[[3.89,4.98,7.56,9.64,26.07,1,"JAN",2017,31],
    //         [3.81,6.29,8.19,9.85,28.14,12,"DEC",2016,31]]
    $str = @file_get_contents($baseurl."1-$token-21");
    $result21 = json_decode(substr($str,2));
    if ($result21==null) return "Invalid data";
    if (!isset($result21->DATA)) return "Invalid data";
    if (!isset($result21->DATA[0])) return "Invalid data";
    
    $month_num = $months[$month];
    
    $latest_month = $result18->DATA[0][5];
    
    $month_index_1 = $latest_month - $month_num;
    $month_index_2 = $month_index_1 + 1;
    
    $result = array(
        array(
            "month"=>$result18->DATA[$month_index_1][5],
            "year"=>$result18->DATA[$month_index_1][7],
            "kwh"=>array(
                "morning"=>$result20->DATA[$month_index_1][0],   // 20
                "midday"=>$result20->DATA[$month_index_1][1],    // 20
                "evening"=>$result20->DATA[$month_index_1][2],   // 20
                "overnight"=>$result20->DATA[$month_index_1][3], // 20
                "hydro"=>$result19->DATA[$month_index_1][4],     // 19
                "total"=>$result18->DATA[$month_index_1][4]      // 18
            ),
            "cost"=>array(
                "morning"=>$result21->DATA[$month_index_1][0],   // 21
                "midday"=>$result21->DATA[$month_index_1][1],    // 21
                "evening"=>$result21->DATA[$month_index_1][2],   // 21
                "overnight"=>$result21->DATA[$month_index_1][3], // 21
                "total"=>$result21->DATA[$month_index_1][4]      // 21
            )
        ),
        array(
            "month"=>$result18->DATA[$month_index_2][5],
            "year"=>$result18->DATA[$month_index_2][7],
            "kwh"=>array(
                "morning"=>$result20->DATA[$month_index_2][0],   // 20
                "midday"=>$result20->DATA[$month_index_2][1],    // 20
                "evening"=>$result20->DATA[$month_index_2][2],   // 20
                "overnight"=>$result20->DATA[$month_index_2][3], // 20
                "hydro"=>$result19->DATA[$month_index_2][4],     // 19
                "total"=>$result18->DATA[$month_index_2][4]      // 18
            ),
            "cost"=>array(
                "morning"=>$result21->DATA[$month_index_2][0],   // 21
                "midday"=>$result21->DATA[$month_index_2][1],    // 21
                "evening"=>$result21->DATA[$month_index_2][2],   // 21
                "overnight"=>$result21->DATA[$month_index_2][3], // 21
                "total"=>$result21->DATA[$month_index_2][4]      // 21
            )
        )
    );
    
    return $result;
}

// -------------------------------------------------------------
// Monthly community consumption for report
// -------------------------------------------------------------
function get_community_consumption_monthly($baseurl,$token) {

    // API: 22 (Community monthly kWh import total)
    // "COLUMNS":["PERIOD1","PERIOD2","PERIOD3","PERIOD4","TOTAL","MONTH","MONTHDESC","YEAR","DAYSINMONTH"],
    // "DATA":[[41.8,54.1,81.1,103.9,280.9,1,"JAN",2017,31],
    //         [36.9,60.8,73.7,96.8,268.2,12,"DEC",2016,31]]
    $str = @file_get_contents($baseurl."1-$token-22");
    $result22 = json_decode(substr($str,2));
    if ($result22==null) return "Invalid data";
    if (!isset($result22->DATA)) return "Invalid data";
    
    // API: 23 (Community monthly kWh import allocated to hydro)
    // "COLUMNS":["PERIOD1","PERIOD2","PERIOD3","PERIOD4","TOTAL","MONTH","MONTHDESC","YEAR","DAYSINMONTH"],
    // "DATA":[[33.43,41.96,46.82,78.57,200.78,1,"JAN",2017,31],
    //         [12.18,20.57,12.11,30.64,75.5,12,"DEC",2016,31]]
    $str = @file_get_contents($baseurl."1-$token-23");
    $result23 = json_decode(substr($str,2));
    if ($result23==null) return "Invalid data";
    if (!isset($result23->DATA)) return "Invalid data";
    
    // API: 24 (Community monthly kWh import provided by supplier)
    // "COLUMNS":["PERIOD1","PERIOD2","PERIOD3","PERIOD4","TOTAL","MONTH","MONTHDESC","YEAR","DAYSINMONTH"],
    // "DATA":[[8.37,12.14,34.28,25.33,80.12,1,"JAN",2017,31],
    //         [24.72,40.23,61.59,66.16,192.7,12,"DEC",2016,31]] 
    $str = @file_get_contents($baseurl."1-$token-24");
    $result24 = json_decode(substr($str,2));
    if ($result24==null) return "Invalid data";
    if (!isset($result24->DATA)) return "Invalid data";

    // API: 25 (Community monthly total cost of import)
    // "COLUMNS":["PERIOD1","PERIOD2","PERIOD3","PERIOD4","TOTAL","MONTH","MONTHDESC","YEAR","DAYSINMONTH"],
    // "DATA":[[3.89,4.98,7.56,9.64,26.07,1,"JAN",2017,31],
    //         [3.81,6.29,8.19,9.85,28.14,12,"DEC",2016,31]]
    $str = @file_get_contents($baseurl."1-$token-25");
    $result25 = json_decode(substr($str,2));
    if ($result25==null) return "Invalid data";
    if (!isset($result25->DATA)) return "Invalid data";
    
    $result = array(
        array(
            "month"=>$result22->DATA[0][0],
            "year"=>$result22->DATA[0][7],
            "kwh"=>array(
                "morning"=>$result24->DATA[0][1],   // 20
                "midday"=>$result24->DATA[0][2],    // 20
                "evening"=>$result24->DATA[0][3],   // 20
                "overnight"=>$result24->DATA[0][4], // 20
                "hydro"=>$result23->DATA[0][5],     // 19
                "total"=>$result22->DATA[0][5]      // 18
            ),
            "cost"=>array(
                "morning"=>$result25->DATA[0][1],   // 21
                "midday"=>$result25->DATA[0][2],    // 21
                "evening"=>$result25->DATA[0][3],   // 21
                "overnight"=>$result25->DATA[0][4], // 21
                "total"=>$result25->DATA[0][5]      // 21
            )
        ),
        array(
            "month"=>$result22->DATA[1][0],
            "year"=>$result22->DATA[1][7],
            "kwh"=>array(
                "morning"=>$result24->DATA[1][1],   // 20
                "midday"=>$result24->DATA[1][2],    // 20
                "evening"=>$result24->DATA[1][3],   // 20
                "overnight"=>$result24->DATA[1][4], // 20
                "hydro"=>$result23->DATA[1][5],     // 19
                "total"=>$result22->DATA[1][5]      // 18
            ),
            "cost"=>array(
                "morning"=>$result25->DATA[1][1],   // 21
                "midday"=>$result25->DATA[1][2],    // 21
                "evening"=>$result25->DATA[1][3],   // 21
                "overnight"=>$result25->DATA[1][4], // 21
                "total"=>$result25->DATA[1][5]      // 21
            )
        )
    );
    
    return $result;
}

function get_demand_shaper($baseurl,$token) {

    $str = @file_get_contents($baseurl."1-$token-30");
    $data = json_decode(substr($str,2));
    return $data;
}

