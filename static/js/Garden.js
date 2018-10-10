var GARDEN_HEIGHT = 1000;
var GARDEN_WIDTH = 1000;

function Garden() {
    var self = this;

    this.zoomIn = function() {
        if (self.width > 40 && self.height > 40) {
            if (this.divWidth > this.divHeight) {
                self.topX += 20;
                self.topY += 20 * (self.divHeight / self.divWidth);
            } else {
                self.topX += 20 * (self.divWidth / self.divHeight);
                self.topY += 20;
            }
            self.width -= 40;
            self.height -= 40;
            self.moved();
        }
        return false;
    }

    this.zoomOut = function() {
        if (this.divWidth > this.divHeight) {
            self.topX -= 20;
            self.topY -= 20 * (self.divHeight / self.divWidth);
        } else {
            self.topX -= 20 * (self.divWidth / self.divHeight);
            self.topY -= 20;
        }
        self.width += 40;
        self.height += 40;
        self.moved();
        return false;
    }

    this.move = function(x, y) {
        self.topX += x;
        self.topY += y;
        self.moved();
        if (x == 0 || y == 0) {
            return false;
        }
        return true;
    }

    this.toScreenX = function(x, abs) {
        var topX = self.topX;
        if (abs) topX = 0;
        return (x - topX) * self.scale;
    };

    this.toScreenY = function(y, abs) {
        var topY = self.topY;
        if (abs) topY = 0;
        return (y - topY) * self.scale;
    };

    this.toMapX = function(x, abs) {
        var topX = self.topX;
        if (abs) topX = 0;
        return x / self.scale + topX;
    }

    this.toMapY = function(y, abs) {
        var topY = self.topY;
        if (abs) topY = 0;
        return y / self.scale + topY;
    }

    this.moved = function() {
        self.divWidth = $(".garden").width();
        self.divHeight = $(".garden").height();
        if (self.divWidth > self.divHeight) {
            self.scale = self.divWidth / self.width;
        } else {
            self.scale = self.divHeight / self.height;
        }
        $.each(self.plants, function(ix, plant) {
            plant.position();
        });
        //$(".garden .plant").each(self.positionPlant);
        $(".garden").css("background-position", self.toScreenX(0) + "px " + self.toScreenY(0) + "px");
        $(".garden").css("background-size",
        (self.toScreenX(GARDEN_WIDTH, true)) + "px " +
        (self.toScreenY(GARDEN_HEIGHT, true)) + "px");
    };

    this.initMove = function(e) {
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
        $(".garden").on("mousemove touchmove", self.duringMove);
        self.startMoveX = pageX;
        self.startMoveY = pageY;
    };

    this.endMove = function(e) {
        $(".garden").off("mousemove touchmove");
        setTimeout(function() {
            self.moving = false;
        }, 100)
        return true;
    };

    this.duringMove = function(e) {
        var pageX, pageY;
        if (e.type == "touchmove") {
            pageX = e.originalEvent.touches[0].screenX;
            pageY = e.originalEvent.touches[0].screenY;
        } else {
            pageX = e.pageX;
            pageY = e.pageY;
        }
        if (self.move(self.toMapX(self.startMoveX - pageX, true), self.toMapY(self.startMoveY - pageY, true))) {
            self.moving = true;
            self.startMoveX = pageX;
            self.startMoveY = pageY;
        }
    };

    this.mapclick = function(e) {
        if (self.isPlantMoving) {
            $(".garden").off("mousemove touchmove");
            self.isPlantMoving = false;
            var posX = e.pageX - $(".garden").offset().left;
            var posY = e.pageY - $(".garden").offset().top;
            self.plants[self.openPlant].coordX = self.toMapX(posX);
            self.plants[self.openPlant].coordY = self.toMapY(posY);
            self.plants[self.openPlant].position();
            self.plants[self.openPlant].save();
            self.openPlant = null;
        } else {
            if (!self.moving) {
                if (self.openPlant) {
                    self.plants[self.openPlant].close();
                    self.openPlant = null;
                } else {
                    var marker = $("[name=add_plant] .marker");
                    marker.css("top", (e.pageY - 11) + "px");
                    marker.css("left", (e.pageX - 11) + "px");
                    var posX = e.pageX - $(".garden").offset().left;
                    var posY = e.pageY - $(".garden").offset().top;
                    $("[name=add_plant] [name=coord_x]").val(self.toMapX(posX));
                    $("[name=add_plant] [name=coord_y]").val(self.toMapY(posY));
                    $("[name=add_plant").show();
                }
            } else {
                self.moving = false;
            }
        }
    };

    this.plantclick = function(id, e) {
        if (self.isPlantMoving) {
            $(".garden").off("mousemove touchmove");
            self.isPlantMoving = false;
            var posX = e.pageX - $(".garden").offset().left;
            var posY = e.pageY - $(".garden").offset().top;
            self.plants[self.openPlant].coordX = self.toMapX(posX);
            self.plants[self.openPlant].coordY = self.toMapY(posY);
            self.plants[self.openPlant].position();
            self.plants[self.openPlant].save();
            self.openPlant = null;
        } else {
            if (id != self.openPlant) {
                if (self.openPlant) {
                    self.plants[self.openPlant].close();
                }
                self.openPlant = id;
                self.plants[id].open();
            }
        }
        return false;
    };

    this.filterPlants = function() {
        var filter = [];
        $.each(self.species, function(speciesId, species) {
            if (species.filtered) {
                filter.push(1*speciesId);
            }
        });
        $.each(self.plants, function(plantId, plant) {
            plant.filter(filter);
        });
    }

    this.loadPlants = function(callback) {
        $.getJSON(
            "plant/get",
            {},
            function (plants) {
                $.each(plants, function(plantId, plant) {
                    self.plants[plant.id] = new Plant(plant, self);
                });
                if (callback) callback();
            }
        );
    };

    this.loadSpecies = function(callback) {
        $.getJSON(
            "species/get",
            {},
            function (species) {
                $.each(species, function(speciesId, spec) {
                    self.species[speciesId] = new Species(spec, self).add();
                });
                if (callback) callback();
            }
        );
    };

    this.deletePlant = function() {
        self.plants[self.openPlant].delete();
        delete self.plants[self.openPlant];
    };

    this.movePlant = function() {
        self.plants[self.openPlant].close();
        self.isPlantMoving = true;
        $(".garden").on("mousemove touchmove", self.plantMove);
        return false;
    };

    this.plantMove = function(e) {
        var posX, posY;
        if (e.type == "touchmove") {
            posX = e.originalEvent.touches[0].screenX - $(".garden").offset().left;
            posY = e.originalEvent.touches[0].screenY - $(".garden").offset().top;
        } else {
            posX = e.pageX - $(".garden").offset().left;
            posY = e.pageY - $(".garden").offset().top;
        }
        $("#plant_" + self.openPlant).css("top", (posY - 12) + "px");
        $("#plant_" + self.openPlant).css("left", (posX - 12) + "px");
    };

    this.editPlant = function() {
        $("[name=edit_plant] [name=plant_id]").val(self.openPlant);
        $("[name=edit_plant] .name").html(self.plants[self.openPlant].name);
        $("[name=edit_plant] [name=description]").val(self.plants[self.openPlant].description);
        $("[name=edit_plant]").show();
    };

    $("[name=add_plant]")[0].success = function (plant) {
        self.plants[plant.id] = new Plant(plant, self);
        $("[name=add_plant]").hide();
    };

    $("[name=add_species]")[0].success = function (speciesData) {
        species = new Species(speciesData, self).add();
        self.species[species.id] = species;
        $("[name=add_species]").hide();
        species.getOption().click();
    };

    $("[name=edit_plant]")[0].success = function (plant) {
        self.plants[plant.id].update(plant);
        $("[name=edit_plant]").hide();
    };

    $("[name=edit_garden]")[0].success = function (garden) {
        $("#garden_name").html(garden.name);
        $("[name=edit_garden]").hide();
    };

    $("[name=edit_species]")[0].success = function (species) {
        self.species[species.id].update(species);
        $("[name=edit_species]").hide();
    };

    $("#btn_add_species").click(function() {
        var species = new Species();
        species.addToForm($("[name=add_species]").show());
    });

    $(".btn_load_species").click(function() {
        var element = $(this).closest("form");

        var url = element.find("[name=url]").val();
        var name = element.find("[name=name]").val();

        $.getJSON(
            "species/load-url",
            {url: url, name: name},
            function (speciesData) {
                species = new Species(speciesData, self);
                species.addToForm(element.show());
            }
        );
    });

    $("#btn_edit_garden").click(function() {
        $("[name=edit_garden]").show();
    });

    $("#btn_open_species_list").click(function() {
        $("#species_list").toggle();
    });

    $("#plant_template .btn_remove").click(self.deletePlant);
    $("#plant_template .btn_move").click(self.movePlant);
    $("#plant_template .btn_edit").click(self.editPlant);

    $(".garden").click(self.mapclick);
    $(".garden").on("mousedown touchstart", self.initMove);
    $("body").on("mouseup touchend", self.endMove);
    $("#btn_zoom_in").click(self.zoomIn);
    $("#btn_zoom_out").click(self.zoomOut);
    $(".garden").on("wheel", function(e) {
        if (e.originalEvent.deltaY < 0) {
            self.zoomIn();
        } else {
            self.zoomOut();
        }
    });
    $(window).resize(self.moved);

    self.moving = false;
    self.width = GARDEN_WIDTH;
    self.height = GARDEN_HEIGHT;
    self.divWidth = $(".garden").width();
    self.divHeight = $(".garden").height();
    if (self.divWidth > self.divHeight) {
        self.scale = self.divWidth / this.width;
        self.topX = 0;
        self.topY = (self.height - self.divHeight / self.scale) / 2;
    } else {
        self.scale = self.divHeight / self.height;
        self.topY = 0;
        self.topX = (self.width - self.divWidth / self.scale) / 2;
    }

    self.plants =  {};
    self.species = {};
    self.openPlant = null;
    self.isPlantMoving = false;

    self.loadSpecies(self.loadPlants);
    self.moved();

    return this;
}
