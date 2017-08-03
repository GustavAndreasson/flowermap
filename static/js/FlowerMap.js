var GARDEN_HEIGHT = 1000;
var GARDEN_WIDTH = 1000;

function Garden() {
    var self = this;
    //this.zoom = 1;
    this.top_x = 0;
    this.top_y = 0;
    this.width = GARDEN_WIDTH;
    this.height = GARDEN_HEIGHT;
    this.div_width = $(".plant_map").width();
    //this.div_height = $(".plant_map").height();

    this.zoom_in = function() {
        self.top_x += 20;
        self.top_y += 20;
        self.width -= 40;
        self.height -= 40;
        self.moved();
        return false;
    }
    
    this.zoom_out = function() {
        self.top_x -= 20;
        self.top_y -= 20;
        self.width += 40;
        self.height += 40;
        self.moved();
        return false;
    }

    this.move = function(x, y) {
        self.top_x += x;
        self.top_y += y;
        self.moved();
    }
    
    this.transform_x = function(x) {
        return x * (self.div_width / self.width) - self.top_x;
    };
        
    this.transform_y = function(y) {
        return y * (self.div_width / self.height) - self.top_y;
    };
        
    this.moved = function() {
        self.div_width = $(".plant_map").width();
        self.div_height = $(".plant_map").height();
        $(".plant_map .plant").each(function() {
	          this.style.left = self.transform_x($(this).data("coordX") - 12) + "px";
	          this.style.top = self.transform_y($(this).data("coordY") - 12) + "px";
        });
        $(".plant_map").css("background-position", self.transform_x(0) + "px " + self.transform_y(0) + "px");
        $(".plant_map").css("background-size",
                            (self.transform_x(GARDEN_WIDTH) + self.top_x) + "px " +
                            (self.transform_y(GARDEN_HEIGHT) + self.top_y) + "px");
    };

    this.init_move = function(e) {
        $(".plant_map").mousemove(self.during_move);
        self.start_move_x = e.pageX;
        self.start_move_y = e.pageY;
    };

    this.end_move = function(e) {
        $(".plant_map").off("mousemove");
    };

    this.during_move = function(e) {
        self.move(self.start_move_x - e.pageX, self.start_move_y - e.pageY)
        self.start_move_x = e.pageX;
        self.start_move_y = e.pageY;
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

    $(".plant_map").click(self.mapclick);
    $(".plant_map .plant").click(self.plantclick);
    $(".plant_map").mousedown(self.init_move);
    $("body").mouseup(self.end_move);
    $("#btn_zoom_in").click(self.zoom_in);
    $("#btn_zoom_out").click(self.zoom_out);
    $(window).resize(self.moved);
    self.moved();

    return this;
}

$(function() {
    var garden = new Garden();

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
