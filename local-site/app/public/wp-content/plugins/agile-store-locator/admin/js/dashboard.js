var asl_engine = window['asl_engine'] || {};

(function($, app_engine) {
  'use strict';


  /**
   * [toastIt toast it based on the error or message]
   * @param  {[type]} _response [description]
   * @return {[type]}           [description]
   */
  var toastIt = function(_response) {

    if(_response.success) {
      atoastr.success(_response.msg || _response.message);
    }
    else {
      atoastr.error(_response.error || _response.message || _response.msg);
    }
  };

  app_engine['pages'] = {
    /**
     * [dashboard Main Dashboard page]
     * @return {[type]} [description]
     */
    dashboard: function() {

      var current_date  = 0,
        date_           = new Date();

      var day_arr = [];
      var months  = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        month     = months[date_.getMonth()],
        data_arr  = [];

      //  Tabs switch
      $('.asl-p-cont .nav-tabs a').click(function(e) {
        e.preventDefault()
        $(this).tab('show');
      });
      
      //  add dummy data
      for (var a = 1; a <= date_.getDate(); a++) {

        day_arr.push(a + ' ' + month);
        data_arr.push(0);
      }

      var lineChartData = {
        labels: day_arr,
        datasets: [{
          tension: 0.1,
          lineTension: 0.1,
          backgroundColor: "rgba(75, 192, 192, 0.4)",
          borderColor: "rgba(75, 192, 192, 1",
          borderCapStyle: 'butt',
          borderDash: [],
          borderDashOffset: 0.0,
          borderJoinStyle: 'miter',
          pointBorderColor: "rgba(75, 192, 192, 1)",
          pointBackgroundColor: "#fff",
          pointBorderWidth: 1,
          pointHoverRadius: 5,
          pointHoverBackgroundColor: "rgba(75, 192, 192, 1)",
          pointHoverBorderColor: "rgba(220, 220, 220, 1)",
          pointHoverBorderWidth: 2,
          pointRadius: 1,
          pointHitRadius: 10,
          label: 'Searches',
          backgroundColor: "#57C8F2",
          data: data_arr
        }]

      };

      asl_initialize_chart();

      //  Datetime
      var $datepicker = $('#sl-datetimepicker');


      /////////////////////////////////
      //  Change the expertise level //
      /////////////////////////////////
      var $sl_level = $('#asl-level-swtch');

      /**
       * [update_level description]
       * @param  {[type]} _status [description]
       */
      function update_level(_status, _callback) {
        
        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=expertise_level", {status: $sl_level[0].checked? '1': '0'}, function(_response) {

          toastIt(_response);

          if(_callback) {
            _callback(_response);
          }

          window.setTimeout(function(){
            
            window.location.reload();

          }, 1500);


        }, 'json');
      }; 

      //  Cache Switch Event
      $sl_level.bind('change', function(e) {

        var chk_ctrl = e.target,
            status   = (chk_ctrl.checked)? '1': '0';

        update_level(status);
      });


      ///////////bar chart
      var ctx = document.getElementById("asl_search_canvas").getContext("2d"),
        charts_option = {
          type: 'line',
          data: lineChartData,
          options: {
            bezierCurve: true,
            animation: true,
            responsive: true,
            maintainAspectRatio: false,
            title: {
              display: true,
              text: '#Searches'
            },
            scales: {
              y: {
                suggestedMin: 0,
                ticks: {
                  beginAtZero: true
                }
              }
            }
          }
        };
      var myBar = new Chart(ctx, charts_option);

      Chart.defaults.scales.linear.min = 0;

      /**
       * [updateChart Get the Stats Chart]
       * @return {[type]}   [description]
       */
      function updateChart(_chart_data) {

        var temp_keys = [],
            temp_vals = [];

        for (var k in _chart_data) {

          temp_keys.push(_chart_data[k]['label']);
          temp_vals.push(_chart_data[k]['data']);
        }
        
        myBar.config.data.labels           = temp_keys;
        myBar.config.data.datasets[0].data = temp_vals;
        myBar.update();
      };


      //getViews(temp[0], temp[1]);
      
      //  datetime options
      var date_time_options = {
        "timePicker": false,
        "parentEl": '.asl-p-cont',
        "alwaysShowCalendars": false,
        "startDate": moment().subtract(6, 'days'),
        "endDate": moment().startOf('hour'),
         "ranges": {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      };
    
     


      //  Add datetimepicker
      $datepicker.daterangepicker(date_time_options, 
        function(start, end, label) {
          
          //console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')')
      });
    }
  };


  asl_engine.pages.dashboard();

})(jQuery, asl_engine);