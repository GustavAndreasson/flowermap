<h2>Lista på trädgårdens växter</h2>
<div class="plant_list">
<?php foreach ($fm->user->garden->plants as $plant): ?>
     <div class="plant" data-plant-id="<?= $plant->get_plant_id() ?>" data-coord-x="<?= $plant->get_coord_x() ?>" data-coord-y="<?= $plant->get_coord_y() ?>">
          <div class="name"><?= $plant->get_name() ?></div>
          <div class="description"><?= $plant->get_description() ?></div>
<?php if ($plant->get_image()): ?>
          <img src="<?= $plant->get_image() ?>">
<?php endif; ?>
     </div>
<?php endforeach; ?>
</div>
<form name="add_plant" class="pop-up" action="controllers/garden.php" method="POST" enctype="multipart/form-data" style="display:none;">
     <input type="hidden" name="action" value="add_plant" />
     <input type="hidden" name="coord_x" />
     <input type="hidden" name="coord_y" />
     <h2><?= $T->__("Add a plant") ?></h2>
     <div class="row"><label for="add_plant_name"><?= $T->__("Name") ?></label><input type="text" name="name" id="add_plant_name"></div>
     <div class="row"><label for="add_plant_description"><?= $T->__("Description") ?></label><input type="textfield" name="description" id="add_plant_description"></div>
     <div class="row"><label for="add_plant_image"><?= $T->__("Image") ?></label><input type="file" accept="image/jpeg" name="image" id="add_plant_image"></div>
     <div class="row">
          <button type="button" class="cancel"><?= $T->__("Cancel") ?></button>
          <button type="submit"><?= $T->__("Add plant") ?></button>
     </div>
</form>
<form name="logout" action="controllers/user.php" method="POST">
     <input type="hidden" name="action" value="logout" />
     <button type="submit"><?= $T->__("Logout") ?></button>
</form>