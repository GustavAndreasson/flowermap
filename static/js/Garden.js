var GARDEN_HEIGHT = 1000;
var GARDEN_WIDTH = 1000;

function Garden() {
    var self = this;

    function zoomIn() {
        self.scale *= 1.1;
        self.moved();
        return false;
    }

    function zoomOut() {
        self.scale /= 1.1;
        self.moved();
        return false;
    }

    function move(x, y) {
        self.posX += x;
        self.posY += y;
        self.moved();
        if (x == 0 && y == 0) {
            return false;
        }
        return true;
    }

    this.toScreenX = function(x, abs) {
        if (abs) return x * self.scale;
        var dx = (x - self.posX) * self.scale;
        return self.divWidth / 2 + dx;
    };

    this.toScreenY = function(y, abs) {
        if (abs) return y * self.scale;
        var dy = (y - self.posY) * self.scale;
        return self.divHeight / 2 + dy;
    };

    this.toMapX = function(x, abs) {
        if (abs) return x / self.scale;
        var dx = x - self.divWidth / 2;
        return dx / self.scale + self.posX;
    }

    this.toMapY = function(y, abs) {
        if (abs) return y / self.scale;
        var dy = y - self.divHeight / 2;
        return dy / self.scale + self.posY;
    }

    this.moved = function() {
        self.divWidth = $(".garden").width();
        self.divHeight = $(".garden").height();
        if (self.divWidth > self.divHeight) {
            self.width = self.divWidth / self.scale;
        } else {
            self.height = self.divHeight / self.scale;
        }
        $.each(self.plants, function(ix, plant) {
            plant.position();
        });
        $(".garden").css("background-position", self.toScreenX(0) + "px " + self.toScreenY(0) + "px");
        $(".garden").css("background-size",
        (self.toScreenX(GARDEN_WIDTH, true)) + "px " +
        (self.toScreenY(GARDEN_HEIGHT, true)) + "px");
    };

    function initMove (e) {
        var pageX, pageY;
        if (e.type == "touchstart") {
            pageX = e.originalEvent.touches[0].screenX;
            pageY = e.originalEvent.touches[0].screenY;
            if (e.originalEvent.touches[1] !== undefined) {
                var pageX2 = e.originalEvent.touches[1].screenX;
                var pageY2 = e.originalEvent.touches[1].screenY;
                var distX = pageX - pageX2;
                var distY = pageY - pageY2;
                var dist = Math.sqrt(distX * distX + distY * distY);
                self.startMoveZ = dist;
                //alert("init" + dist + " " + self.startMoveZ + " " + pageX + "," + pageY + " " + pageX2 + "," + pageY2);
            } else {
                self.startMoveZ = null;
            }
        } else {
            if (e.which != 1) {
                return;
            }
            pageX = e.pageX;
            pageY = e.pageY;
        }
        $(".garden").on("mousemove touchmove", duringMove);
        self.startMoveX = pageX;
        self.startMoveY = pageY;
    };

    function endMove(e) {
        $(".garden").off("mousemove touchmove");
        self.startMoveZ = null;
        setTimeout(function() {
            self.moving = false;
        }, 100)
        return true;
    };

    function duringMove(e) {
        e.preventDefault();
        var pageX, pageY;
        if (e.type == "touchmove") {
            pageX = e.originalEvent.touches[0].screenX;
            pageY = e.originalEvent.touches[0].screenY;
            if (e.originalEvent.touches[1] !== undefined) {
                var pageX2 = e.originalEvent.touches[1].screenX;
                var pageY2 = e.originalEvent.touches[1].screenY;
                var distX = pageX - pageX2;
                var distY = pageY - pageY2;
                pageX = (pageX + pageX2) / 2;
                pageY = (pageY + pageY2) / 2;
                var dist = Math.sqrt(distX * distX + distY * distY);
                if (self.startMoveZ) {
                    //alert("during " + dist + " " + self.startMoveZ + " " + pageX + "," + pageY + " " + pageX2 + "," + pageY2);
                    self.scale *= dist / self.startMoveZ;
                } else {
                    self.startMoveX = pageX;
                    self.startMoveY = pageY;
                }
                self.startMoveZ = dist;
            } else {
                if (self.startMoveZ) {
                    self.startMoveZ = null;
                    self.startMoveX = pageX;
                    self.startMoveY = pageY;
                }
            }
        } else {
            pageX = e.pageX;
            pageY = e.pageY;
        }
        if (move(self.toMapX(self.startMoveX - pageX, true), self.toMapY(self.startMoveY - pageY, true))) {
            self.moving = true;
            self.startMoveX = pageX;
            self.startMoveY = pageY;
        }
    };

    function mapclick(e) {
        if (self.isPlantMoving) {
            stopPlantMove(e.pageX, e.pageY);
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
            stopPlantMove(e.pageX, e.pageY);
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

    function stopPlantMove(pageX, pageY) {
        $(".garden").off("mousemove touchmove");
        self.isPlantMoving = false;
        var posX = pageX - $(".garden").offset().left;
        var posY = pageY - $(".garden").offset().top;
        self.plants[self.openPlant].coordX = self.toMapX(posX);
        self.plants[self.openPlant].coordY = self.toMapY(posY);
        self.plants[self.openPlant].position();
        self.plants[self.openPlant].save();
        self.openPlant = null;
    }

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

    function deletePlant() {
        self.plants[self.openPlant].delete();
        delete self.plants[self.openPlant];
    };

    function movePlant() {
        self.plants[self.openPlant].close();
        self.isPlantMoving = true;
        $(".garden").on("mousemove touchmove", plantMove);
        return false;
    };

    function plantMove(e) {
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

    function editPlant() {
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

    $(".header .menu .menu-button").click(function() {
        var menu = $(this).parent(".menu").toggleClass('open');
    });

    $("#plant_template .btn_remove").click(deletePlant);
    $("#plant_template .btn_move").click(movePlant);
    $("#plant_template .btn_edit").click(editPlant);

    $(".garden").click(mapclick);
    $(".garden").on("mousedown touchstart", initMove);
    $("body").on("mouseup touchend", endMove);
    $("#btn_zoom_in").click(zoomIn);
    $("#btn_zoom_out").click(zoomOut);
    $(".garden").on("wheel", function(e) {
        if (e.originalEvent.deltaY < 0) {
            zoomIn();
        } else {
            zoomOut();
        }
    });
    $(window).resize(self.moved);

    self.moving = false;
    self.divWidth = $(".garden").width();
    self.divHeight = $(".garden").height();
    self.posX = GARDEN_WIDTH / 2;
    self.posY = GARDEN_HEIGHT / 2;
    if (self.divWidth > self.divHeight) {
        self.scale = self.divWidth / GARDEN_WIDTH;
    } else {
        self.scale = self.divHeight / GARDEN_HEIGHT;
    }

    self.plants = {};
    self.species = {};
    self.openPlant = null;
    self.isPlantMoving = false;

    self.loadSpecies(self.loadPlants);
    self.moved();

    return this;
}
