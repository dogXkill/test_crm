﻿// when the dom is ready...
$(function() {
    var i = 0;
    $("colgroup").each(function() {
        i++;
        $(this).attr("id", "col"+i);

     });

    var totalCols = i;
   // alert(i)
    i = -2;
     $("td").each(function() {
         // alert(i)
         $(this).attr("rel", "col"+i);
        i++;
        if (i > totalCols) { i = 1; }
     });
    $("td").hover(function() {
        $(this).parent().addClass("hover");
        var curCol = $(this).attr("rel");
        $("#"+curCol).addClass("hover");
    }, function() {
        $(this).parent().removeClass("hover");
        var curCol = $(this).attr("rel");
        $("#"+curCol).removeClass("hover");
    });
});