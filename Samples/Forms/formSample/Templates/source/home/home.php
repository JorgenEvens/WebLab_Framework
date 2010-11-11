<form method="<?php echo $this->form->getMethod(); ?>" action="<?php echo $this->form->getAction(); ?>">
    <?php if( isset( $_POST['sent'] ) && !$this->form->isValid() ): ?>
        <div class="errors" style="font-family: Verdana; font-size: 11px; color: red;" >
            <ul>
                <?php
                foreach( $this->form->getErrors() as $field => $errors ){
                    if( is_array( $errors ) ){
                        foreach( $errors as $error ){
                           echo ' <li>' . $field . ": " . $error . '</li>';
                        }
                    } else {
                        echo ' <li>' . $field . ": " . $errors . '</li>';
                    }
                };
                ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if( $this->form->isValid() ): ?>
    <h2>Form has been sent !</h2>
    <?php endif; ?>
    <label for="name">Naam: </label><?php echo $this->form->name; ?><br />
    <label for="email">E-mail: </label><?php echo $this->form->email; ?><br />
    <input type="submit" value="Verzenden" name="sent" />
</form>