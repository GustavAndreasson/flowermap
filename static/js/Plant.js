function Plant(plant, garden) {
    var self = this;
    this.id = plant.id;
    this.species = garden.species[plant.speciesId];
    this.species.plants.push(this);
    this.name = plant.name;
    this.description = plant.description;
    this.image = plant.image;
    this.coordX = plant.coordX;
    this.coordY = plant.coordY;
    this.garden = garden;
    this.isOpen = false;

    function addPlantHtml() {
        var plantElement = $("#plant_template").clone(true);
        plantElement.attr("id", "plant_" + self.id);
        plantElement.find(".name").html(self.name);
        plantElement.find(".description").html(self.description);
        plantElement.find("img").attr("src", self.image);

        plantElement.click(function (e) {
            self.garden.plantclick(self.id, e);
            e.stopPropagation();
        });

        $(".garden").append(plantElement.show());
        self.position();
    };

    this.getElement = function() {
        return $("#plant_" + self.id);
    }

    this.position = function() {
        var element = self.getElement();
        if (!self.isOpen) {
            element.css("left", (self.garden.toScreenX(self.coordX) - 12) + "px");
            element.css("top", (self.garden.toScreenY(self.coordY) - 12) + "px");
        } else {
            var left = self.garden.toScreenX(self.coordX) - 12;
            var top = self.garden.toScreenY(self.coordY) - 12;
            var width = element.outerWidth();
            var height = element.outerHeight();
            if (left + width > self.garden.divWidth) {
                element.css("left", (self.garden.divWidth - width) + "px");
            } else if (left < 0) {
                element.css("left", "0px");
            } else {
                element.css("left", left + "px");
            }
            if (top + height > self.garden.divHeight) {
                element.css("top", (self.garden.divHeight - height) + "px");
            } else if (top < 0) {
                element.css("top", "0px");
            } else {
                element.css("top", top + "px");
            }
        }
    }

    this.setName = function(name) {
        self.name = name;
        self.getElement().find(".name").html(name);
    }

    this.setDescription = function(description) {
        self.description = description;
        self.getElement().find(".description").html(description);
    }

    this.setImage = function(image) {
        self.image = image;
        self.getElement().find("img").attr("src", image);
    }

    this.update = function(plant) {
        self.setDescription(plant.description);
        self.setImage(plant.image);
        self.species = garden.species[plant.speciesId];
    }

    this.open = function() {
        self.isOpen = true;
        self.getElement().addClass("open");
        self.position();
    }

    this.close = function() {
        self.isOpen = false;
        self.getElement().removeClass("open");
        self.position()
    }

    this.filter = function(filter) {
        if (filter.length && filter.indexOf(self.species.id) <= -1) {
            self.getElement().addClass("filtered");
        } else {
            self.getElement().removeClass("filtered");
        }
    }

    this.delete = function() {
        $.post(
            "plant/delete",
            {plant_id: self.id},
            function () {
                self.getElement().remove();
            }
        );
    }

    this.save = function() {
        $.post(
            "plant/update",
            {
                plant_id: self.id,
                description: self.description,
                coord_x: self.coordX,
                coord_y: self.coordY
            }
        );
    }

    addPlantHtml();

    return this;
}
