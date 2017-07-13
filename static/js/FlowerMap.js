

$(function() {
    $(".plant_list .plant").each(function() {
	this.style.left = ($(this).data("coordX") - 12) + "px";
	this.style.top = ($(this).data("coordY") - 12) + "px";
    });

    $(".plant_list").click(function(e) {
	if ($(".plant_list .plant.open").length) {
	    $(".plant_list .plant").removeClass("open");
	} else {
	    var posX = e.pageX - $(this).offset().left;
            var posY = e.pageY - $(this).offset().top;
	    $("[name=add_plant] [name=coord_x]").val(posX);
	    $("[name=add_plant] [name=coord_y]").val(posY);
	    $("[name=add_plant").show();
	}
    });

    $(".plant_list .plant").click(function() {
	$(".plant_list .plant").removeClass("open");
	$(this).addClass("open");
	return false;
    });

    $(".pop-up .cancel").click(function() {
	$(this).closest(".pop-up").hide();
    });
});
