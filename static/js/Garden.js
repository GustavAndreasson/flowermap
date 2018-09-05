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

    this.plants =  {};
    this.species = {};
    this.open_plant = null;
    this.is_plant_moving = false;

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
        $.each(self.plants, function(ix, plant) {
            plant.position();
        });
        //$(".garden .plant").each(self.position_plant);
        $(".garden").css("background-position", self.to_screen_x(0) + "px " + self.to_screen_y(0) + "px");
        $(".garden").css("background-size",
        (self.to_screen_x(GARDEN_WIDTH, true)) + "px " +
        (self.to_screen_y(GARDEN_HEIGHT, true)) + "px");
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
        if (self.is_plant_moving) {
            $(".garden").off("mousemove touchmove");
            self.is_plant_moving = false;
            var posX = e.pageX - $(".garden").offset().left;
            var posY = e.pageY - $(".garden").offset().top;
            self.plants[self.open_plant].coord_x = self.to_map_x(posX);
            self.plants[self.open_plant].coord_y = self.to_map_y(posY);
            self.plants[self.open_plant].position();
            self.plants[self.open_plant].save();
            self.open_plant = null;
        } else {
            if (!self.moving) {
                if (self.open_plant) {

                    self.plants[self.open_plant].close();
                    self.open_plant = null;
                } else {
                    var posX = e.pageX - $(".garden").offset().left;
                    var posY = e.pageY - $(".garden").offset().top;
                    $("[name=add_plant] [name=coord_x]").val(self.to_map_x(posX));
                    $("[name=add_plant] [name=coord_y]").val(self.to_map_y(posY));
                    $("[name=add_plant").show();
                }
            } else {
                self.moving = false;
            }
        }
    };

    this.plantclick = function(id, e) {
        if (self.is_plant_moving) {
            $(".garden").off("mousemove touchmove");
            self.is_plant_moving = false;
            var posX = e.pageX - $(".garden").offset().left;
            var posY = e.pageY - $(".garden").offset().top;
            self.plants[self.open_plant].coord_x = self.to_map_x(posX);
            self.plants[self.open_plant].coord_y = self.to_map_y(posY);
            self.plants[self.open_plant].position();
            self.plants[self.open_plant].save();
            self.open_plant = null;
        } else {
            if (id != self.open_plant) {
                if (self.open_plant) {
                    self.plants[self.open_plant].close();
                }
                self.open_plant = id;
                self.plants[id].open();
            }
        }
        return false;
    };

    this.load_plants = function() {
        $.getJSON(
            "controllers/plant.php",
            {action: "get_plants"},
            function (plants) {
                $.each(plants, function(plant_id, plant) {
                    self.plants[plant.id] = new Plant(plant, self);
                });
            }
        );
    };

    this.load_species = function() {
        $.getJSON(
            "controllers/species.php",
            {action: "get_species"},
            function (species) {
                self.species = species;
            }
        );
    };

    this.delete_plant = function() {
        self.plants[self.open_plant].delete();
        delete self.plants[self.open_plant];
    };

    this.move_plant = function() {
        self.plants[self.open_plant].close();
        self.is_plant_moving = true;
        $(".garden").on("mousemove touchmove", self.plant_move);
        return false;
    };

    this.plant_move = function(e) {
        var posX, posY;
        if (e.type == "touchmove") {
            posX = e.originalEvent.touches[0].screenX - $(".garden").offset().left;
            posY = e.originalEvent.touches[0].screenY - $(".garden").offset().top;
        } else {
            posX = e.pageX - $(".garden").offset().left;
            posY = e.pageY - $(".garden").offset().top;
        }
        $("#plant_" + self.open_plant).css("top", (posY - 12) + "px");
        $("#plant_" + self.open_plant).css("left", (posX - 12) + "px");
    };

    this.edit_plant = function() {
        $("[name=edit_plant] [name=plant_id]").val(self.open_plant);
        $("[name=edit_plant] .name").html(self.plants[self.open_plant].name);
        $("[name=edit_plant] [name=description]").val(self.plants[self.open_plant].description);
        $("[name=edit_plant]").show();
    };

    $(".garden").click(self.mapclick);
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
