var GARDEN_HEIGHT = 1000;
var GARDEN_WIDTH = 1000;

function Garden() {
    var self = this;

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
            "plant/get",
            {},
            function (plants) {
                $.each(plants, function(plant_id, plant) {
                    self.plants[plant.id] = new Plant(plant, self);
                });
            }
        );
    };

    this.load_species = function() {
        $.getJSON(
            "species/get",
            {},
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

    $("[name=add_plant]")[0].success = function (data) {
        var plant = JSON.parse(data);
        self.plants[plant.id] = new Plant(plant, self);
        $("[name=add_plant]").hide();
    };

    $("[name=add_species]")[0].success = function (data) {
        var species = JSON.parse(data);
        self.species[species.id] = species;
        $("[name=add_species]").hide();
        $("[name=add_plant] #slct_species .option").removeClass("selected");
        var species_html = "<div class=\"option selected\" data-value=\"" + species.id + "\">" + species.name + "</div>";
        $("[name=add_plant] #slct_species").append(species_html);
        $("[name=add_plant] .species .name").html(self.species[species.id].name);
        $("[name=add_plant] .species img").attr("src", self.species[species.id].image);
        $("[name=add_plant] .species").show();
    };

    $("[name=edit_plant]")[0].success = function (data) {
        var plant = JSON.parse(data);
        self.plants[plant.id].set_description(plant.description);
        self.plants[plant.id].set_image(plant.image);
        $("[name=edit_plant]").hide();
    };

    $("[name=edit_garden]")[0].success = function (data) {
        var garden_data = JSON.parse(data);
        $("#garden_name").html(garden_data.name);
        $("[name=edit_garden]").hide();
    };

    $("#btn_add_species").click(function() {
        $("[name=add_species]").show();
    });

    $("#btn_load_species").click(function() {
        var url = $("#add_species_url").val();
        var name = $("#add_species_name").val();

        $.getJSON(
            "species/load_url",
            {url: url, name: name},
            function (species) {
                var species_string = '<input type="hidden" name="loaded_species_name" value="' + species['name'] + '">';
                species_string += '<input type="hidden" name="loaded_species_url" value="' + species['url'] + '">';
                $.each(species['data'], function(name, value) {
                    species_string += '<div class="row"><span class="data_name">' + name + '</span>';
                    species_string += '<span class="data_value">' + value + '</span>';
                    species_string += '<input type="hidden" name="data[' + name + ']" value="' + value + '"></div>';
                });
                species_string += '<div class="row">';
                species_string += '<input type="hidden" name="species_image" id="add_plant_image" value="' + species['image'] + '">';
                species_string += '<img src="' + species['image'] + '"></div>';
                $("[name=add_species] .species_data").html(species_string)
                $("#add_species_name").val(species['name']);
                $("#add_species_url").val(species['url']);
            }
        );
    });

    $("#btn_add_species_data").click(function() {
        var name = $("#add_species_data_name").val();
        var value = $("#add_species_data_value").val();
        var html = "<div class='row'><span class='data_name'>" + name;
        html += "</span><span class='data_value'>" + value + "</span>";
        html += "<input type='hidden' name='data[" + name + "]' value='" + value;
        html += "'></div>";
        $("[name=add_plant] .species_data").append(html);
    });

    $("#btn_edit_garden").click(function() {
        $("[name=edit_garden]").show();
    });

    $("#slct_species .option").click(function() {
        var species_id = $(this).data("value");
        if (species_id) {
            $("[name=add_plant] .species .name").html(self.species[species_id].name);
            $("[name=add_plant] .species img").attr("src", self.species[species_id].image);
            $("[name=add_plant] .species").show();
        } else {
            $("[name=add_plant] .species").hide();
        }
    });

    $("#plant_template .btn_remove").click(self.delete_plant);
    $("#plant_template .btn_move").click(self.move_plant);
    $("#plant_template .btn_edit").click(self.edit_plant);

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

    self.moving = false;
    self.width = GARDEN_WIDTH;
    self.height = GARDEN_HEIGHT;
    self.div_width = $(".garden").width();
    self.div_height = $(".garden").height();
    if (self.div_width > self.div_height) {
        self.scale = self.div_width / this.width;
        self.top_x = 0;
        self.top_y = (self.height - self.div_height / self.scale) / 2;
    } else {
        self.scale = self.div_height / self.height;
        self.top_y = 0;
        self.top_x = (self.width - self.div_width / self.scale) / 2;
    }

    self.plants =  {};
    self.species = {};
    self.open_plant = null;
    self.is_plant_moving = false;

    self.load_species();
    self.load_plants();
    self.moved();

    return this;
}
