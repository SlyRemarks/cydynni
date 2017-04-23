/*

Hydro section

*/

var hydro_data = [];
var hydroseries = [];
setInterval(hydro_load,60000);

function hydro_load() {

    // bargraph_loading("hydro_bargraph_placeholder","rgba(39,78,63,0.7)");
    $("#hydro_bargraph_placeholder").css("background-color","rgba(39,78,63,0.7)");
    var data = [];
    $.ajax({                                      
        url: path+"hydro",
        dataType: 'json',
        async: true,                      
        success: function(result) {
            $("#hydro_bargraph_placeholder").css("background","none");
            if (!result || result===null || result==="" || result.constructor!=Array) {
                console.log("ERROR","invalid response: "+result);
            } else {

                hydro_data = result;
                var forecast = [];
                
                if (hydro_data.length>0) {
                    
                    for (var z in hydro_data)
                        // hydro_data[z][1] = ((hydro_data[z][1] * 3600000) / 1800) * 0.001;
                        if (hydro_data[z][1]<0) hydro_data[z][1]=0;
                    
                    var last_power = hydro_data[hydro_data.length-2][1]*1;   
                    var power = hydro_data[hydro_data.length-1][1]*1;
                    var time = hydro_data[hydro_data.length-1][0]*1;
                    
                    $("#power").html(((power.toFixed(1)*3600000)/1800)*0.001);
                    $("#kWhHH").html(power.toFixed(1));
                    if (power>=50) {
                        $("#hydrostatus").html(t("HIGH"));
                        $("#hydro_summary").html(t("For next 12 hours: HIGH POWER"));
                    }
                    else if (power>=30) {
                        $("#hydrostatus").html(t("MEDIUM"));
                        $("#hydro_summary").html(t("For next 12 hours: MEDIUM"));
                    }
                    else if (power>=10) {
                        $("#hydrostatus").html(t("LOW"));
                        $("#hydro_summary").html(t("For next 12 hours: LOW"));
                    }
                    else {
                        $("#hydrostatus").html(t("VERY LOW"));
                        $("#hydro_summary").html(t("For next 12 hours: VERY LOW"));
                    }
                    
                    forecast = hydro_forecaster(time,power,last_power,24);
                    
                    // Show day instead of "last 24 hour"
                    var d1 = new Date();
                    var t1 = d1.getTime()*0.001;
                    var d2 = new Date(hydro_data[0][0]);
                    var t2 = d2.getTime()*0.001;
                    
                    var dayoffset = Math.floor((t1-t2)/(3600*24));
                    console.log("Days behind: "+dayoffset);
                    
                    // Time of last hydro datapoint
                    var d3 = new Date(hydro_data[hydro_data.length-1][0]);
                    var t3 = d3.getTime()*0.001;
                    
                    // Calculate hours behind
                    var half_hours_behind = Math.ceil((t1 - t3) / 1800);
                    console.log("Half hours behind: "+half_hours_behind);
                    
                    // Calculate forecast up to present hour
                    var forecastlive = hydro_forecaster(time,power,last_power,half_hours_behind);
                    var forecastlive_hydro = forecastlive[forecastlive.length-1][1];
                    console.log("Current hydro forecast: "+(forecastlive_hydro).toFixed(1)+" kWh/HH");
                    console.log("Current hydro forecast: "+(forecastlive_hydro*2).toFixed(1)+" kW");
                    
                    var hour = d2.getHours(); var month = d2.getMonth(); var day = d2.getDate();
                    if (hour>=12) hour=(hour-12)+"pm"; else hour=hour+"am";
                    var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
                    
                    if (dayoffset==1) {
                        $("#hydro-graph-date").html(t("Yesterday")+" ("+day+" "+t(months[month])+"):");
                    } else {
                        $("#hydro-graph-date").html(day+" "+months[month]);
                    }
                    
                    
                } else {
                    $("#hydrostatus").html(t("NO DATA"));
                }
                
                // hydroseries = [];
                // hydroseries.push({data:hydro_data, color:"rgba(39,78,63,0.7)"});
                // hydroseries.push({data:forecast, color:"rgba(39,78,63,0.2)"});
                // hydro_resize(panel_height);

                hydroseries = [];
                hydroseries.push({
                    data: forecast, color: "#d3dbd8",
                    bars: { show: true, align: "center", barWidth: 0.75*3600*0.5*1000, fill: 1.0, lineWidth:0}
                });
                hydroseries.push({
                    data: hydro_data, color: "#678278",
                    bars: { show: true, align: "center", barWidth: 0.75*3600*0.5*1000, fill: 1.0, lineWidth:0}
                });

                hydro_resize(panel_height);
            }
        }
    });
}

function hydro_draw() {

    var options = {
        xaxis: { 
            mode: "time", 
            timezone: "browser", 
            font: {size:flot_font_size, color:"#666"}, 
            // labelHeight:-5
            reserveSpace:false
        },
        yaxis: { 
            font: {size:flot_font_size, color:"#666"}, 
            // labelWidth:-5
            reserveSpace:false,
            min:0
        },
        selection: { mode: "x" },
        grid: {
            show:true, 
            color:"#aaa",
            borderWidth:0,
            hoverable: true, 
            clickable: true
        }
    }

    var plot = $.plot($('#hydro_bargraph_placeholder'),hydroseries,options);
    $('#hydro_bargraph_placeholder').append("<div id='bargraph-label' style='position:absolute;left:50px;top:30px;color:#666;font-size:12px'></div>");

    // bargraph("hydro_bargraph_placeholder",hydroseries," kWh","rgba(39,78,63,0.7)");
}

function hydro_resize(panel_height) {
    
    var window_width = $(window).width();
    flot_font_size = 12;
    if (window_width<450) flot_font_size = 10;
        
    // var h = panel_height-120;
    // width = $("#hydro_bargraph_placeholder_bound").width();
    // $("#hydro_bargraph_placeholder").attr('width',width);
    // $('#hydro_bargraph_placeholder_bound').attr("height",h);
    // $('#hydro_bargraph_placeholder').attr("height",h);
    // height = h
    // hydro_draw(); 

    var h = panel_height-120;
    width = $("#hydro_bargraph_placeholder_bound").width();
    $("#hydro_bargraph_placeholder").width(width);
    $('#hydro_bargraph_placeholder_bound').height(h);
    $('#hydro_bargraph_placeholder').height(h);
    height = h;
    hydro_draw();
}

function hydro_forecaster(time,power,lastpower,forecastlength) {

    var ThRising = 2.0; // kWh/HH
    var Gfloor = 1.0; // kWh/HH
    var Drate = -0.08; // kWh/HH/HH
    var Delta = 22; 
    var DeltaSquared = Delta * Delta;
    var Dratelin = -0.02;
    var timeinc = 1800000;

    time = time*1;
    power = power*1;
    lastpower = lastpower*1;
    
    DeltaSquared = Delta * Delta;
    var forecast = [];

    var forecast_case = 0;

    // shutdown
    if (power==0) {
        forecast_case = 5;
        // Forecast: zero output
        for (var z=0; z<forecastlength; z++) {
            var ft = time+z*timeinc;
            forecast.push([ft,0]);
        }
    }
    // Hydro output increasing: assume flat
    else if ((power-lastpower)>ThRising) {
        forecast_case = 4;
        // Forecast: increasing, assume flat
        for (var z=0; z<forecastlength; z++) {
            var ft = time+z*timeinc;
            forecast.push([ft,power]);
        }    
    }
    // Medium power
    else if (power>5) {
        forecast_case = 3;
        // Forecast: (Geni-Gfloor)/Delta^2*Drate+Geni
        forecast.push([time,power]);
        for (var z=1; z<forecastlength; z++) {
            var fv = ((forecast[z-1][1] - Gfloor) / (DeltaSquared * Drate)) + forecast[z-1][1];
            var ft = time+z*timeinc;
            forecast.push([ft,fv]);
        }    
    }
    // Low power
    else if (power>2) {
        forecast_case = 1;
        // Forecast: (Geni-Gfloor)/Delta^2*Drate+Geni
        forecast.push([time,power]);
        for (var z=1; z<forecastlength; z++) {
            var fv = ((forecast[z-1][1] - Gfloor) / (DeltaSquared * Drate)) + forecast[z-1][1];
            var ft = time+z*timeinc;
            forecast.push([ft,fv]);
        }
    }
    // Lowest power
    else if (power<=2) {
        forecast_case = 2;
        // Forecast: Geni + Dratelin
        for (var z=0; z<forecastlength; z++) {
            var ft = time+z*timeinc;
            forecast.push([ft,power + Dratelin]);
        }
    }
    
    return forecast;
}

$('#hydro_bargraph_placeholder').bind("plothover", function (event, pos, item) {
    if (item) {
        var z = item.dataIndex;
        
        if (previousPoint != item.datapoint) {
            previousPoint = item.datapoint;

            $("#tooltip").remove();
            var itemTime = item.datapoint[0];
            var elec_kwh = hydroseries[item.seriesIndex].data[z][1];
            var note = "";
            if (item.seriesIndex==0) note = "Forecast ";

            var d = new Date(itemTime);
            var days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
            var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
            var mins = d.getMinutes();
            if (mins==0) mins = "00";
            var date = d.getHours()+":"+mins+" "+days[d.getDay()]+", "+months[d.getMonth()]+" "+d.getDate();
            tooltip(item.pageX, item.pageY, date+"<br>"+note+(elec_kwh).toFixed(0)+" kWh", "#fff");
        }
    } else $("#tooltip").remove();
});
