function Plant(plant, garden) {
    var self = this;
    this.id = plant.id;
    this.species = garden.species[plant.species_id];
    this.species.plants.push(this);
    this.name = plant.name;
    this.description = plant.description;
    this.image = plant.image;
    this.coord_x = plant.coord_x;
    this.coord_y = plant.coord_y;
    this.garden = garden;
    this.is_open = false;

    function add_plant_html() {
        var plant_element = $("#plant_template").clone(true);
        plant_element.attr("id", "plant_" + self.id);
        plant_element.find(".name").html(self.name);
        plant_element.find(".description").html(self.description);
        plant_element.find("img").attr("src", self.image);

        plant_element.click(function (e) {
            self.garden.plantclick(self.id, e);
            e.stopPropagation();
        });

        $(".garden").append(plant_element.show());
        self.position();
    };

    this.get_element = function() {
        return $("#plant_" + self.id);
    }

    this.position = function() {
        var element = self.get_element();
        if (!self.is_open) {
            element.css("left", (self.garden.to_screen_x(self.coord_x) - 12) + "px");
            element.css("top", (self.garden.to_screen_y(self.coord_y) - 12) + "px");
        } else {
            var left = self.garden.to_screen_x(self.coord_x) - 12;
            var top = self.garden.to_screen_y(self.coord_y) - 12;
            var width = element.outerWidth();
            var height = element.outerHeight();
            if (left + width > self.garden.div_width) {
                element.css("left", (self.garden.div_width - width) + "px");
            } else if (left < 0) {
                element.css("left", "0px");
            } else {
                element.css("left", left + "px");
            }
            if (top + height > self.garden.div_height) {
                element.css("top", (self.garden.div_height - height) + "px");
            } else if (top < 0) {
                element.css("top", "0px");
            } else {
                element.css("top", top + "px");
            }
        }
    }

    this.set_name = function(name) {
        self.name = name;
        self.get_element().find(".name").html(name);
    }

    this.set_description = function(description) {
        self.description = description;
        self.get_element().find(".description").html(description);
    }

    this.set_image = function(image) {
        self.image = image;
        self.get_element().find("img").attr("src", image);
    }

    this.update = function(plant) {
        self.set_description(plant.description);
        self.set_image(plant.image);
        self.species = garden.species[plant.species_id];
    }

    this.open = function() {
        self.is_open = true;
        self.get_element().addClass("open");
        self.position();
    }

    this.close = function() {
        self.is_open = false;
        self.get_element().removeClass("open");
        self.position()
    }

    this.filter = function(filter) {
        if (filter.length && filter.indexOf(self.species.id) <= -1) {
            self.get_element().addClass("filtered");
        } else {
            self.get_element().removeClass("filtered");
        }
    }

    this.delete = function() {
        $.post(
            "plant/delete",
            {plant_id: self.id},
            function () {
                self.get_element().remove();
            }
        );
    }

    this.save = function() {
        $.post(
            "plant/update",
            {
                plant_id: self.id,
                description: self.description,
                coord_x: self.coord_x,
                coord_y: self.coord_y
            }
        );
    }

    add_plant_html();

    return this;
}
