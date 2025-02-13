$(function() {
    var dp = $(".datepicke").datepicker({
        changeMonth: true,
        changeYear: true
      });
      $("#datepicker-2").datepicker();
      $(".ui-datepicker-month", dp).hide();
      $("<span>", {
        class: "ui-datepicker-month-btn btn"
      }).html($(".ui-datepicker-month option:selected", dp).text()).insertAfter($(".ui-datepicker-month", dp));
      $(".ui-datepicker-year", dp).hide();
      $("<span>", {
        class: "ui-datepicker-year-btn btn"
      }).html($(".ui-datepicker-year option:selected", dp).text()).insertAfter($(".ui-datepicker-year", dp));
    
      function selectMonth(ev) {
        var mObj = $(ev.target);
        $(".ui-datepicker-month", dp).val(mObj.data("value")).trigger("change");
        $(".ui-datepicker-month-btn", dp).html(mObj.text().trim());
        mObj.closest(".ui-datepicker").find(".ui-datepicker-calendar").show();
        mObj.closest(".ui-datepicker-month-calendar").remove();
      }
    
      function showMonths(trg) {
        $(".ui-datepicker-calendar", trg).hide();
        var mCal = $("<table>", {
          class: "ui-datepicker-month-calendar"
        }).insertAfter($(".ui-datepicker-calendar", trg));
        var row, cell;
        $(".ui-datepicker-month option").each(function(i, o) {
          if (i % 4 == 0) {
            row = $("<tr>").appendTo(mCal);
          }
          cell = $("<td>").appendTo(row);
          $("<span>", {
              class: "ui-widget-header circle btn"
            })
            .data("value", $(o).val())
            .html($(o).text().trim())
            .click(selectMonth)
            .appendTo(cell);
          if ($(o).is(":selected")) {
            $("span", cell).addClass("selected");
          }
        });
      }
    
      $(".ui-datepicker-month-btn").click(function() {
        console.log("Show Months");
        showMonths(dp);
      })
  
});
//select año fecha y hora
$(function () {
  var dp = $(".datepicker-time").datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: "mm/dd/yy",
    onSelect: function (datetext) {
      var d = new Date(); // for now
      var h = d.getHours();
      h = (h < 10) ? ("0" + h) : h;

      var m = d.getMinutes();
      m = (m < 10) ? ("0" + m) : m;

      var s = d.getSeconds();
      s = (s < 10) ? ("0" + s) : s;

      datetext = datetext + " " + h + ":" + m + ":" + s;
      $(this).val(datetext);
    },
  });

  $("#datepicker-2").datepicker();
  $(".ui-datepicker-month", dp).hide();
  $("<span>", {
    class: "ui-datepicker-month-btn btn"
  }).html($(".ui-datepicker-month option:selected", dp).text()).insertAfter($(".ui-datepicker-month", dp));
  $(".ui-datepicker-year", dp).hide();
  $("<span>", {
    class: "ui-datepicker-year-btn btn"
  }).html($(".ui-datepicker-year option:selected", dp).text()).insertAfter($(".ui-datepicker-year", dp));

  function selectMonth(ev) {
    var mObj = $(ev.target);
    $(".ui-datepicker-month", dp).val(mObj.data("value")).trigger("change");
    $(".ui-datepicker-month-btn", dp).html(mObj.text().trim());
    mObj.closest(".ui-datepicker").find(".ui-datepicker-calendar").show();
    mObj.closest(".ui-datepicker-month-calendar").remove();
  }

  function showMonths(trg) {
    $(".ui-datepicker-calendar", trg).hide();
    var mCal = $("<table>", {
      class: "ui-datepicker-month-calendar"
    }).insertAfter($(".ui-datepicker-calendar", trg));
    var row, cell;
    $(".ui-datepicker-month option").each(function (i, o) {
      if (i % 4 == 0) {
        row = $("<tr>").appendTo(mCal);
      }
      cell = $("<td>").appendTo(row);
      $("<span>", {
        class: "ui-widget-header circle btn"
      })
        .data("value", $(o).val())
        .html($(o).text().trim())
        .click(selectMonth)
        .appendTo(cell);
      if ($(o).is(":selected")) {
        $("span", cell).addClass("selected");
      }
    });
  }

  $(".ui-datepicker-month-btn").click(function () {
    console.log("Show Months");
    showMonths(dp);
  })

});

//fecha y hora
$(function () {
  var dp = $(".datepicker").datepicker({
    changeMonth: true,
    changeYear: true,
    onSelect: function (datetext) {
      var d = new Date(); // for now
      var h = d.getHours();
      h = (h < 10) ? ("0" + h) : h;

      var m = d.getMinutes();
      m = (m < 10) ? ("0" + m) : m;

      var s = d.getSeconds();
      s = (s < 10) ? ("0" + s) : s;

      datetext = datetext + " " + h + ":" + m + ":" + s;
      $(this).val(datetext);
    },
  });

});




