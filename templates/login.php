<div class="login">
     <form name="login" action="controllers/user.php" method="POST">
          <input type="hidden" name="action" value="login" />
          <label for="login_name"><?= $T->__("Username") ?></label><input type="text" name="name" id="login_name" />
          <label for="login_password"><?= $T->__("Password") ?></label><input type="password" name="password" id="login_password" />
          <button type="submit"><?= $T->__("Login") ?></button>
     </form>
     <form name="register" action="controllers/user.php" method="POST">
          <input type="hidden" name="action" value="register" />
          <label for="register_name"><?= $T->__("Username") ?></span><input type="text" name="name" id="register_name" />
          <label for="register_password"><?= $T->__("Password") ?></span><input type="password" name="password" id="register_password" />
          <label for="register_confirm_password"><?= $T->__("Repeat password") ?></span><input type="password" name="confirm_password" id="register_confirm_password" />
          <button type="submit"><?= $T->__("Register") ?></button>
     </form>
</div>