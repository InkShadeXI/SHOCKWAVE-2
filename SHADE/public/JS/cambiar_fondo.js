$(document).ready(function () {
    var color = "black";
    $("button").click(function () {
        if (color == "black") {
            color = "white";
            $("body").attr("background-color", color);
        }
        else {
            color = "black";
            $("body").attr("background-color", color);
        }
    });
});