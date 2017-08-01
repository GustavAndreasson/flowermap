

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
    
    $("#btn_load_species").click(function() {
	      $("[name=add_plant] .species_data").load(
            "controllers/garden.php",
            {action: "load_species_url", url: $("#add_plant_url").val()}
        );
    });

    $("#slct_species .option").click(function() {
        $("[name=add_plant] .species_data").html("");
        if ($(this).data("value")) {
            $("[name=add_plant] .species_data").load(
                "controllers/garden.php",
                {action: "load_species_id", id: $(this).data("value")}
            );
            $("[name=add_plant] .new_species").hide();
            $("#add_plant_name").attr("name", "name_disabled");            
        } else {
            $("[name=add_plant] .new_species").show();
            $("#add_plant_name").attr("name", "name");
        }
    });
    
    

    $(".pop-up .cancel").click(function() {
	      $(this).closest(".pop-up").hide();
    });
    
    $(".select .option").click(function() {
        $(this).siblings().removeClass("selected");
        $(this).addClass("selected");
        $(this).siblings("input").val($(this).data("value"));
    });
});
