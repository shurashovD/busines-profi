<?php

if ( isset($data['is_auth']) && $data['is_auth'] )
{
    $host = 'http://' . $_SERVER['HTTP_HOST'];
    header('Location:' . $host . '/tasks');
    exit();
}

?>



<div class="container min-vh-100 d-flex">
    <form class="m-auto col-12 col-md-8 col-lg-5" method="POST" action="/">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="mail"
                pattern="^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$"
                value=<?php
                    if ( isset($data['mail']) )
                    {
                        echo ($data['mail']);
                    }
                ?>
            >
            <small class="text-danger d-<? isset($data['validation_error']) && $data['validation_error'] ? "block" : "none" ?>" id="validation-message">
                <?php
                    if (isset($data['validation_error']) && $data['validation_error'])
                    {
                        echo $data['validation_error'];
                    }
                ?>
            </small>
        </div>
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
</div>