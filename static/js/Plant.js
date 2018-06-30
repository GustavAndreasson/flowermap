function Plant(plant) {
    var self = this;
    this.plant_id = plant.plant_id;
    this.species_id = plant.species_id;
    this.name = plant.name;
    this.description = plant.description;
    this.image = plant.image;
    this.coord_x = plant.coord_x;
    this.coord_y = plant.coord_y;

    function add_plant_html() {
        plant_element = '<div class="plant" data-plant-id="' + plant.plant_id;
        plant_element += '" data-coord-x="' + plant.coord_x;
        plant_element += '" data-coord-y="' + plant.coord_y + '">';
        plant_element += '<div class="name"> + 'plant.name + '</div>';
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

    add_plant_html();
    
    return this;
}
