var GARDEN_HEIGHT = 1000;
var GARDEN_WIDTH = 1000;

function Garden() {
  var self = this;
  this.moving = false;
  this.width = GARDEN_WIDTH;
  this.height = GARDEN_HEIGHT;
  this.div_width = $(".garden").width();
  this.div_height = $(".garden").height();
  if (this.div_width > this.div_height) {
    this.scale = this.div_width / this.width;
    this.top_x = 0;
    this.top_y = (this.height - this.div_height / this.scale) / 2;
  } else {
    this.scale = this.div_height / this.height;
    this.top_y = 0;
    this.top_x = (this.width - this.div_width / this.scale) / 2;
  }

    this.plants = [];
    this.species = [];

  this.zoom_in = function() {
    if (self.width > 40 && self.height > 40) {
      if (this.div_width > this.div_height) {
        self.top_x += 20;
        self.top_y += 20 * (self.div_height / self.div_width);
      } else {
        self.top_x += 20 * (self.div_width / self.div_height);
        self.top_y += 20;
      }
      self.width -= 40;
      self.height -= 40;
      self.moved();
    }
    return false;
  }

  this.zoom_out = function() {
    if (this.div_width > this.div_height) {
      self.top_x -= 20;
      self.top_y -= 20 * (self.div_height / self.div_width);
    } else {
      self.top_x -= 20 * (self.div_width / self.div_height);
      self.top_y -= 20;
    }
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

  this.to_map_x = function(x, abs) {
    var top_x = self.top_x;
    if (abs) top_x = 0;
    return x / self.scale + top_x;
  }

  this.to_map_y = function(y, abs) {
    var top_y = self.top_y;
    if (abs) top_y = 0;
    return y / self.scale + top_y;
  }

  this.moved = function() {
    self.div_width = $(".garden").width();
    self.div_height = $(".garden").height();
    if (self.div_width > self.div_height) {
      self.scale = self.div_width / self.width;
    } else {
      self.scale = self.div_height / self.height;
    }
    $(".garden .plant").each(self.position_plant);
    $(".garden").css("background-position", self.to_screen_x(0) + "px " + self.to_screen_y(0) + "px");
    $(".garden").css("background-size",
    (self.to_screen_x(GARDEN_WIDTH, true)) + "px " +
    (self.to_screen_y(GARDEN_HEIGHT, true)) + "px");
  };

  this.position_plant = function() {
    if (!$(this).hasClass("open")) {
      this.style.left = (self.to_screen_x($(this).data("coordX")) - 12) + "px";
      this.style.top = (self.to_screen_y($(this).data("coordY")) - 12) + "px";
    } else {
      var left = self.to_screen_x($(this).data("coordX")) - 12;
      var top = self.to_screen_y($(this).data("coordY")) - 12;
      var width = $(this).outerWidth();
      var height = $(this).outerHeight();
      if (left + width > self.div_width) {
        this.style.left = (self.div_width - width) + "px";
      } else if (left < 0) {
        this.style.left = "0px";
      } else {
        this.style.left = left + "px";
      }
      if (top + height > self.div_height) {
        this.style.top = (self.div_height - height) + "px";
      } else if (top < 0) {
        this.style.top = "0px";
      } else {
        this.style.top = top + "px";
      }
    }
  };

  this.init_move = function(e) {
    var pageX, pageY;
    if (e.type == "touchstart") {
      pageX = e.originalEvent.touches[0].screenX;
      pageY = e.originalEvent.touches[0].screenY;
    } else {
      if (e.which != 1) {
        return;
      }
      pageX = e.pageX;
      pageY = e.pageY;
    }
    $(".garden").on("mousemove touchmove", self.during_move);
    self.start_move_x = pageX;
    self.start_move_y = pageY;
  };

  this.end_move = function(e) {
    $(".garden").off("mousemove touchmove");
    setTimeout(function() {
      self.moving = false;
    }, 100)
    return true;
  };

  this.during_move = function(e) {
    var pageX, pageY;
    if (e.type == "touchmove") {
      pageX = e.originalEvent.touches[0].screenX;
      pageY = e.originalEvent.touches[0].screenY;
    } else {
      pageX = e.pageX;
      pageY = e.pageY;
    }
    self.moving = true;
    self.move(self.to_map_x(self.start_move_x - pageX, true), self.to_map_y(self.start_move_y - pageY, true))
    self.start_move_x = pageX;
    self.start_move_y = pageY;
  };

  this.mapclick = function(e) {
    if (!self.moving) {
      if ($(".garden .plant.open").length) {
        $(".garden .plant.open").removeClass("open").each(self.position_plant);
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
    $(".garden .plant.open").removeClass("open").each(self.position_plant);
    $(this).addClass("open").each(self.position_plant);
    return false;
  };

    this.load_plants = function() {
        $.getJSON(
            "controller/plant.php",
            {action: "get_plants"},
            function (plants) {
                $.each(plants, function(plant_id, plant) {
                    self.plants[plant_id] = new Plant(plant);
                });
            });
    };

    this.load_species = function() {
        $.getJSON(
            "controller/species.php",
            {action: "get_species"},
            function (species) {
                self.species = species;
            });
    };

  $(".garden").click(self.mapclick);
  $(".garden .plant").click(self.plantclick);
  $(".garden").on("mousedown touchstart", self.init_move);
  $("body").on("mouseup touchend", self.end_move);
  $("#btn_zoom_in").click(self.zoom_in);
  $("#btn_zoom_out").click(self.zoom_out);
  $(".garden").on("wheel", function(e) {
    if (e.originalEvent.deltaY < 0) {
      self.zoom_in();
    } else {
      self.zoom_out();
    }
  });
  $(window).resize(self.moved);
  self.moved();

  return this;
}