var GARDEN_HEIGHT = 1000;
var GARDEN_WIDTH = 1000;

function Garden() {
    var self = this;
    self.moving = false;
    this.width = GARDEN_WIDTH;
    this.height = GARDEN_HEIGHT;
    this.div_width = $(".garden").width();
    this.div_height = $(".garden").height();
    this.scale = this.div_width / this.width;
    this.top_x = 0;
    this.top_y = (this.height - this.div_height / this.scale) / 2;

    this.zoom_in = function() {
        self.top_x += 20;
        self.top_y += 20 * (self.div_height / self.div_width);
        self.width -= 40;
        self.height -= 40;
        self.moved();
        return false;
    }
    
    this.zoom_out = function() {
        self.top_x -= 20;
        self.top_y -= 20 * (self.div_height / self.div_width);
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
    
    this.to_screen_x = function(x, abs) {
        var top_x = self.top_x;
        if (abs) top_x = 0;
        return (x - top_x) * self.scale;
    };
        
    this.to_screen_y = function(y, abs) {
        var top_y = self.top_y;
        if (abs) top_y = 0;
        return (y - top_y) * self.scale;
    };

    this.to_map_x = function(x) {
        return x / self.scale + self.top_x;
    }

    this.to_map_y = function(y) {
        return y / self.scale + self.top_y;
    }
        
    this.moved = function() {
        self.div_width = $(".garden").width();
        self.div_height = $(".garden").height();
	      self.scale = self.div_width / self.width;
        $(".garden .plant").each(function() {
	          this.style.left = self.to_screen_x($(this).data("coordX") - 12) + "px";
	          this.style.top = self.to_screen_y($(this).data("coordY") - 12) + "px";
        });
        $(".garden").css("background-position", self.to_screen_x(0) + "px " + self.to_screen_y(0) + "px");
        $(".garden").css("background-size",
                            (self.to_screen_x(GARDEN_WIDTH, true)) + "px " +
                            (self.to_screen_y(GARDEN_HEIGHT, true)) + "px");
    };

    this.init_move = function(e) {
	      if (e.witch == 1) {
            $(".garden").mousemove(self.during_move);
            self.start_move_x = e.pageX;
            self.start_move_y = e.pageY;
	      }
    };

    this.end_move = function(e) {
        $(".garden").off("mousemove");
        return false;
    };
    
    this.during_move = function(e) {
        self.moving = true;
        self.move(self.start_move_x - e.pageX, self.start_move_y - e.pageY)
        self.start_move_x = e.pageX;
        self.start_move_y = e.pageY;
    };
    
    this.mapclick = function(e) {
        if (!self.moving) {
	          if ($(".garden .plant.open").length) {
	              $(".garden .plant").removeClass("open");
	          } else {
	              var posX = e.pageX - $(this).offset().left;
                var posY = e.pageY - $(this).offset().top;
	              $("[name=add_plant] [name=coord_x]").val(self.to_map_x(posX));
	              $("[name=add_plant] [name=coord_y]").val(self.to_map_y(posY));
	              $("[name=add_plant").show();
	          }
        } else {
            self.moving = false;
        }
    };
    
    this.plantclick = function() {
	      $(".garden .plant").removeClass("open");
	      $(this).addClass("open");
        if ($(this).position().left + 400 > self.div_width) {
            this.style.left = (self.div_width - 400) + "px";
        }
        if ($(this).position().top + 200 > self.div_height) {
            this.style.top = (self.div_height - 200) + "px";
        }
	      return false;
    };
    
    $(".garden").click(self.mapclick);
    $(".garden .plant").click(self.plantclick);
    $(".garden").mousedown(self.init_move);
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
	      var name = $("#add_plant_name").val();
	      $("[name=add_plant] .species_data").load(
	          "controllers/garden.php",
	          {action: "load_species_url", url: url, name: name},
	          function () {
		            $("#add_plant_name").val($("[name=add_plant] .species_data [name=loaded_species_name]").val());
                $("#add_plant_url").val($("[name=add_plant] .species_data [name=loaded_species_url]").val());
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
