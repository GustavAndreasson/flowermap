function Plant(plant, garden) {
    var self = this;
    this.id = plant.id;
    this.species_id = plant.species_id;
    this.name = plant.name;
    this.description = plant.description;
    this.image = plant.image;
    this.coord_x = plant.coord_x;
    this.coord_y = plant.coord_y;
    this.garden = garden;
    this.is_open = false;

    function add_plant_html() {
        plant_element = '<div class="plant" id="plant_' + plant.id;
        plant_element += '" data-plant-id="' + plant.id + '">';
        plant_element += '<div class="name">' + plant.name + '</div>';
        plant_element += '<div class="description">' + plant.description + '</div>';
        plant_element += '<img src="' + plant.image + '">';
        plant_element += '<div class="data">';
        /*plant_element += '<?php foreach($plant->species->get_data() as $name => $value): ?>';
        plant_element += '<div class="field">';
        plant_element += '<span class="name"><?= $name ?></span>';
        plant_element += '<span class="value"><?= $value ?></span>';
        plant_element += '</div>';
        plant_element += '<?php endforeach; ?>';*/
        plant_element += '</div>';
        plant_element += '</div>';

        $(".garden").append(plant_element);
    };

    function get_element() {
        return $("#plant_" + self.id);
    }

    this.position = function() {
        var element = get_element();
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

    this.open = function() {
        self.is_open = true;
        get_element().addClass("open");
        self.position();
    }

    this.close = function() {
        self.is_open = false;
        get_element().removeClass("open");
        self.position()
    }

    add_plant_html();
    self.position();
    get_element().click(function (e) {
        self.garden.plantclick(self.id);
        e.stopPropagation();
    });

    return this;
}
