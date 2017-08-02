var GARDEN_HEIGHT = 1000;
var GARDEN_WIDTH = 1000;

function Garden() {
    var self = this;
    //this.zoom = 1;
    this.top_x = 0;
    this.top_y = 0;
    this.width = GARDEN_WIDTH;
    this.height = GARDEN_HEIGHT;
    this.div_width = 800;
    this.div_height = 800;

    this.transform_x = function(x) {
        return x * (this.div_width / this.width) - this.top_x;
    };
        
    this.transform_y = function(y) {
        return y * (this.div_height / this.height) - this.top_y;
    };
        
    this.moved = function() {
        $(".plant_map .plant").each(function() {
	          this.style.left = self.transform_x($(this).data("coordX") - 12) + "px";
	          this.style.top = self.transform_y($(this).data("coordY") - 12) + "px";
        });
        $(".plant_map").css("backgound-position", this.transform_x(0) + "px " + this.transform_y(0) + "px");
        $(".plant_map").css("backgound-size", this.transform_x(GARDEN_WIDTH) + "px " + this.transform_y(GARDEN_HEIGHT) + "px");
    };

    this.mapclick = function(e) {
	      if ($(".plant_map .plant.open").length) {
	          $(".plant_map .plant").removeClass("open");
	      } else {
	          var posX = e.pageX - $(this).offset().left;
            var posY = e.pageY - $(this).offset().top;
	          $("[name=add_plant] [name=coord_x]").val(posX);
	          $("[name=add_plant] [name=coord_y]").val(posY);
	          $("[name=add_plant").show();
	      }
    };

    this.plantclick = function() {
	      $(".plant_map .plant").removeClass("open");
	      $(this).addClass("open");
	      return false;
    };

    return this;
}

$(function() {
    var garden = new Garden();
    $(".plant_map").click(garden.mapclick);
    $(".plant_map .plant").click(garden.plantclick);
    garden.moved();

    $("#btn_load_species").click(function() {
	      var url = $("#add_plant_url").val();
	      if (!url) {
	          var name = $("#add_plant_name").val();
	          name = name.replace(" ", "-");
	          name = name.toLowerCase();
	          url = "http://floralinnea.se/" + name + ".html";
	          $("#add_plant_url").val(url);
	      }   
	      $("[name=add_plant] .species_data").load(
	          "controllers/garden.php",
	          {action: "load_species_url", url: url},
	          function () {
		            $("#add_plant_name").val($("[name=add_plant] .species_data [name=loaded_species_name]").val());
	          }
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
