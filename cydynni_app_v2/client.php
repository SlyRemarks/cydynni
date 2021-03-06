<?php 

global $path;
$v = 1; 

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Cyd Ynni</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    
    <script language="javascript" type="text/javascript" src="lib/jquery-1.11.3.min.js"></script>
    
    <script type="text/javascript" src="lib/flot/jquery.flot.min.js"></script> 
    <script type="text/javascript" src="lib/flot/jquery.flot.time.min.js"></script> 
    <script type="text/javascript" src="lib/flot/jquery.flot.selection.min.js"></script> 
    <script type="text/javascript" src="lib/flot/jquery.flot.stack.min.js"></script>
    <script type="text/javascript" src="lib/flot/date.format.js"></script> 
    <script type="text/javascript" src="lib/vis.helper.js"></script> 
    <script type="text/javascript" src="lib/feed.js"></script> 
    
    <link rel="stylesheet" type="text/css" href="style.css" />

  </head>
  <body>

<div class="wrap">
    <div class="app"><div class="app-inner">
            
        <div style="overflow:hidden">
            <div class="app-title">Energy<br>Dashboard</div>
            <img class="logo-full" src='images/EnergyLocalEnglish.png' style="height:80px; padding:10px">
            <img class="logo-mobile" src='images/logo.png' style="height:80px; padding:10px">
            <br>
        </div>
        
        <ul class="navigation">
            <li name="forecast"><div><img src="images/forecast.png"><div class="nav-text">CydYnni<br>Forecast</div></div></li>
            <li name="household"><div><img src="images/household.png"><div class="nav-text">Your<br>Score</div></div></li>
            <li name="community"><div><img src="images/community.png"><div class="nav-text">Community<br>Score</div></div></li>
            <li name="tips"><div><img src="images/tips.png"><div class="nav-text" style="padding-top:15px">Tips</div></div></li>
        </ul>
        
        <!--------------------------------------------------------->
        
        <div class="page" name="forecast">
            <div class="block expand" style="background-color:#39aa1a">
                <div class="block-title">Forecast</div>
                <div class="block-content">

                  <div style="padding-top:5px; padding-bottom:5px">
                      <div class="legend-label-box" style="background-color:#ffb401"></div>
                      <span class="legend-label"><?php echo t("Morning");?></span>
                      <div class="legend-label-box" style="background-color:#4dac34"></div>
                      <span class="legend-label"><?php echo t("Midday");?></span>
                      <div class="legend-label-box" style="background-color:#e6602b"></div>
                      <span class="legend-label"><?php echo t("Evening");?></span>
                      <div class="legend-label-box" style="background-color:#014c2d"></div>
                      <span class="legend-label"><?php echo t("Overnight");?></span>
                      <div class="legend-label-box" style="background-color:#29aae3"></div>
                      <span class="legend-label" >Hydro</span>
                  </div>
                  
                  <div id="community_bargraph_bound" style="width:100%; height:405px;">
                    <div id="community_bargraph_placeholder" style="height:405px"></div>
                  </div>

                </div>
            </div>
            <div class="block" style="background-color:#088400">
                <div class="block-title">Forecast</div>
                <div class="block-content">
                    <p>Test</p>
                </div>
            </div>
            <div class="block" style="background-color:#005b0b">
                <div class="block-title">Forecast</div>
                <div class="block-content">
                    <p>Test</p>
                </div>
            </div>
        </div>

        <!--------------------------------------------------------->
        
        <div class="page" name="household">
            <div class="block" style="background-color:#c20000">
                <div class="block-title">Household</div>
                <div class="block-content">
                    <p>Test</p>
                </div>
            </div>
            <div class="block" style="background-color:#e62f31">
                <div class="block-title">Household</div>
                <div class="block-content">
                    <p>Test</p>
                </div>
            </div>
            <div class="block" style="background-color:#f47677">
                <div class="block-title">Household</div>
                <div class="block-content">
                    <p>Test</p>
                </div>
            </div>
        </div>
        
        <!--------------------------------------------------------->
        
        <div class="page" name="community">
            <div class="block" style="background-color:#ffb401">
                <div class="block-title">Your score and savings</div>
                <div class="block-content" style="text-align:center">
                    <p><span id="community_score_text"><?php echo t("Over the last seven days we<br>scored"); ?></span>:<br><span id="community_score">50</span>/100</p>
                    <img id="community_star1" src="images/staryellow.png" style="width:45px">
                    <img id="community_star2" src="images/staryellow.png" style="width:45px">
                    <img id="community_star3" src="images/star20yellow.png" style="width:45px">
                    <img id="community_star4" src="images/star20yellow.png" style="width:45px">
                    <img id="community_star5" src="images/star20yellow.png" style="width:45px">
                    <p id="community_statusmsg"></p>
                </div>
            </div>
            <div class="block" style="background-color:#ff7900">
                <div class="block-title">Community breakdown</div>
                <div class="block-content">
                
                    <div class="box2">
                      <div id="hydro_droplet_bound" style="margin: 0 auto">
                        <canvas id="hydro_droplet_placeholder"></canvas>
                      </div>
                    </div>
                
                    <div class="box2">
                      <div id="community_piegraph_bound" style="width:100%; height:405px; margin: 0 auto">
                          <canvas id="community_piegraph_placeholder"></canvas>
                      </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <!--------------------------------------------------------->
        
        <div class="page" name="tips">
            <div class="block" style="background-color:#014656">Tips</div>
        </div>
        
        <div style="clear:both; height:85px"></div>

    </div></div>
</div>

<div class="app"><div class="app-inner">
    <div class="footer">
        <div style="float:right; font-weight:bold">Contact Us</div>
        <div>Energy Local</div>
    </div>
</div></div>

</body>
</html>

<script language="javascript" type="text/javascript" src="js/pie.js?v=<?php echo $v; ?>"></script>
<script language="javascript" type="text/javascript" src="js/community.js?v=<?php echo $v; ?>"></script>
    
<script>

$(".block").addClass("expand");

var path = "<?php echo $path; ?>";

show_page("forecast");

$(".navigation li").click(function() {
    var page = $(this).attr("name");
    show_page(page);
});

$(".block").click(function() {
    $(this).toggleClass("expand");
});

function show_page(page) {
    // Highlighted selected menu
    $(".navigation li > div").removeClass("active");
    $(".navigation li[name="+page+"] > div").addClass("active");
    // Show relevant page
    $(".page").hide();
    $(".page[name="+page+"]").show();
}

$(window).resize(function(){
    resize();
});

function resize() {
    window_height = $(window).height();
    window_width = $(window).width();
    community_bargraph_resize();   
    community_pie_draw();
}

// Flot
var flot_font_size = 12;
var previousPoint = false;

community_summary_load();
community_bargraph_load();

function t(c) {return c;}

</script>
