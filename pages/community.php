<div class="page" page="community" style="display:none;">
<!-- STATUS TAB ------------------------------------------------------->
<div class="pagesection" style="color:rgb(234,200,0)">
  <div style="height:10px; background-color:rgb(235,200,0)"></div>
  <div class="title"><?php echo t("Status"); ?></div>
  <div class="summary_bound"><div id="community_status_summary" class="panel-summary"></div></div>
  <div class="togglelang">CY</div>
</div>
<div class="panel" style="color:rgb(235,200,0)">
  <div class="panel-inner">
    <p><span id="community_score_text"><?php echo t("Over the last week we scored"); ?></span>: <b><span id="community_score"></span></b>/100</p>
    <img id="community_star1" src="images/star20yellow.png" style="width:45px">
    <img id="community_star2" src="images/star20yellow.png" style="width:45px">
    <img id="community_star3" src="images/star20yellow.png" style="width:45px">
    <img id="community_star4" src="images/star20yellow.png" style="width:45px">
    <img id="community_star5" src="images/star20yellow.png" style="width:45px">
    <p id="community_statusmsg"></p>
  </div>
</div>

<!-- SAVING TAB ------------------------------------------------------->
<div class="pagesection" style="color:rgb(255,117,0);">
  <div style="height:10px; background-color:rgb(255,117,0)"></div>
  <div class="title"><?php echo t("Value"); ?></div>
  <div class="summary_bound"><div id="community_value_summary" class="panel-summary"></div></div>
</div>
<div class="panel" style="color:rgb(255,117,0);">
  <div class="panel-inner">
    <p><?php echo t("Value of hydro power retained in the community"); ?> <b>£<span class="community_hydro_value"></span></b></p>
    <!--<p>We have saved <b>£<span class="community_costsaving"></span></b> compared to standard flat rate price</p>-->
  </div>
</div>

<!-- BREAKDOWN TAB ------------------------------------------------------->
<div class="pagesection" style="color:rgb(142,77,0);">
  <div style="height:10px; background-color:rgb(142,77,0)"></div>
  <div id="view-community-bargraph" style="float:right; margin:10px; padding-top:3px"><img src="images/bargraphiconbrown.png" style="width:24px" /></div>
  <div id="view-community-piechart" style="float:right; margin:10px; display:none; padding-top:3px"><img src="images/piechartbrown.png" style="width:24px" /></div>
  <div class="title"><?php echo t("Breakdown");?></div>
</div>
<div class="panel" style="color:rgb(142,77,0);">
  <div class="panel-inner">
    <div id="community_piegraph" style="text-align:left">
    <?php echo t("Time of use & hydro");?>:<br>
    <div style="text-align:center">
    <div id="community_piegraph_bound">
      <canvas id="community_piegraph_placeholder"></canvas>
    </div>
    </div>
    </div>

    <div id="community_bargraph" style="display:none; text-align:left">
    <div style="margin-bottom:5px"><?php echo t("Community Half-hourly Demand");?>: <span id="community-graph-date"></span></div>
    <div id="community_bargraph_bound">
      <canvas id="community_bargraph_placeholder"></canvas>
    </div>
    </div>

  </div>
</div>
</div>
<script language="javascript" type="text/javascript" src="js/community.js"></script>
