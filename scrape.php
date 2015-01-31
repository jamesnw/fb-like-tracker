<?php 

include('errorhandler.php');

$total_likes = 0;
$total_ptat = 0;

function query($id){
        global $total_likes, $total_ptat;
        // create curl resource 
        $ch = curl_init(); 

        // set url 
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/".$id); 

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        // $output contains the output string 
        $output = curl_exec($ch); 
        
        $output = json_decode($output);
        
       	$return = '';
        
        $myFile = 'data/'.$id.".csv";
        $fh = fopen($myFile, 'a');
         
        $lines = file($myFile);
        $daysOfData = count($lines);
        
        $dailyGrowth = '';
        $dailyPercentageGrowth ='';
        
        if($daysOfData > 0){
          // Get daily growth
          $yesterday = explode(",",$lines[count($lines)-1]);
          $yesterdayLikes = $yesterday[1];
          $dailyGrowth = $output->likes - $yesterdayLikes;
          $dailyPercentageGrowth = ($yesterdayLikes > 0)?round($dailyGrowth/$yesterdayLikes,4)*100 ."%":"n/a";
        }
        
        $weeklyGrowth = '';
        $weeklyPercentageGrowth ='';
        
        if($daysOfData >= 7){
          // Get weekly growth
          $yesterday = explode(",",$lines[count($lines)-7]);
          $yesterdayLikes = $yesterday[1];
          $weeklyGrowth = $output->likes - $yesterdayLikes;
          $weeklyPercentageGrowth = ($yesterdayLikes > 0)?round($weeklyGrowth/$yesterdayLikes,4)*100 ."%":"n/a";
        }
        
        $monthlyGrowth = '';
        $monthlyPercentageGrowth ='';
        
        if($daysOfData >= 30){
          // Get monthly growth
          $yesterday = explode(",",$lines[count($lines)-30]);
          $yesterdayLikes = $yesterday[1];
          $monthlyGrowth = $output->likes - $yesterdayLikes;
          $monthlyPercentageGrowth = ($yesterdayLikes > 0)?round($monthlyGrowth/$yesterdayLikes,4)*100 ."%":"n/a";
        }
        
        $halfyrGrowth = '';
        $halfyrPercentageGrowth ='';
        
        if($daysOfData >= 182){
          // Get half yr growth
          $yesterday = explode(",",$lines[count($lines)-182]);
          $yesterdayLikes = $yesterday[1];
          $halfyrGrowth = $output->likes - $yesterdayLikes;
          $halfyrPercentageGrowth = ($yesterdayLikes > 0)?round($halfyrGrowth/$yesterdayLikes,4)*100 ."%":"n/a";
        }

        $yrGrowth = '';
        $yrPercentageGrowth ='';
        
        if($daysOfData >= 365){
          // Get year growth
          $yesterday = explode(",",$lines[count($lines)-365]);
          $yesterdayLikes = $yesterday[1];
          $yrGrowth = $output->likes - $yesterdayLikes;
          $yrPercentageGrowth = ($yesterdayLikes > 0)?round($yrGrowth/$yesterdayLikes,4)*100 ."%":"n/a";
        }
        
        date_default_timezone_set('America/Indiana/Indianapolis');
         $date = date('Y-m-d-H');
       
        $return .= $date.','.$output->likes.','.$output->talking_about_count.','.$output->checkins.','.$output->were_here_count.','.$dailyGrowth.','.$dailyPercentageGrowth.','.$weeklyGrowth.','.$weeklyPercentageGrowth.','.$monthlyGrowth.','.$monthlyPercentageGrowth;
        $return .= "\n";
        
        fwrite($fh,$return);
        fclose($fh);
        
       	$email = array();
       	$email['page'] = $output->name;
        $email['likes'] = $output->likes;
        $email['dailyGrowth'] = $dailyGrowth;
        $email['dailyPercentageGrowth'] = $dailyPercentageGrowth;
        $email['weeklyGrowth'] = $weeklyGrowth;
        $email['weeklyPercentageGrowth'] = $weeklyPercentageGrowth;
        $email['monthlyGrowth'] = $monthlyGrowth;
        $email['monthlyPercentageGrowth'] = $monthlyPercentageGrowth;
        $email['halfyrGrowth'] = $halfyrGrowth;
        $email['halfyrPercentageGrowth'] = $halfyrPercentageGrowth;
        $email['yrGrowth'] = $yrGrowth;
        $email['yrPercentageGrowth'] = $yrPercentageGrowth;
        $email['talking_about_count'] = $output->talking_about_count;
        $email['id'] = $id;
        

        // close curl resource to free up system resources 
        curl_close($ch);    
        
        return $email;
}
include('pages.php');

// create data folder if needed
if (!is_dir('data')){
    mkdir('data');
}

$json = array();

foreach($sections as $section => $ids){
  print($section);
  foreach($ids as $id){
    $subjson= query($id);
    $subjson['category'] = $section;
    $json[] = $subjson;
  }
  
}

$myFile = 'data/current.js';
$fh = fopen($myFile, 'w');
if($fh){

$return = json_encode($json);
fwrite($fh,$return);
fclose($fh);
} else{
print("error");
}

// $date = date('Y-m-d-H');
// $file = 'data/'.$date.'.csv';
// file_put_contents($file, $towrite);
?>