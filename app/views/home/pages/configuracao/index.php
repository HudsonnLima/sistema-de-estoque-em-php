<?php
use App\Core\Database;
use App\Models\Read;

$db = new Database();
$pdo = $db->getConnection();
?>


<?php
$empresa_id = '1';
$conf = $read->fetch("SELECT * FROM config WHERE empresa_id = :empresa_id", ['empresa_id' => $empresa_id]);

if(isset($empresa_id)):
?>

<label class="titulo">Definições gerais do sistema</label>
<hr>
<form name="formulario" action="" method="post">

    <div class="row g-2">
        <h6>Configurações do sistema:</h6>

        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="site_name" value="<?= $conf['site_name'] ?? '' ?>">
                <label for="floatingInputGrid">Nome do sistema:</label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="url_site" value="<?= $conf['url_site'] ?? '' ?>">
                <label for="floatingInputGrid">Endereço do sistema:</label>
            </div>
        </div>

    </div>


    <div class="row g-2">

        <div class="form-group col-md-4">
            <div class="form-floating">
                <input type="email" class="form-control" name="email" value="<?= $conf['email'] ?? '' ?>">
                <label for="floatingInputGrid">Email principal:</label>
            </div>
        </div>

        <div class="form-group col-md-2">
            <div class="form-floating">
                <input type="password" class="form-control" name="pass" value="<?= $conf['pass'] ?? '' ?>">
                <label for="floatingInputGrid">Senha:</label>
            </div>
        </div>

        <div class="form-group col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" name="port" value="<?= $conf['port'] ?? '' ?>">
                <label for="floatingInputGrid">Porta:</label>
            </div>
        </div>

        <div class="form-group col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" name="servidor" value="<?= $conf['servidor'] ?? '' ?>">
                <label for="floatingInputGrid">Servidor:</label>
            </div>
        </div>
    </div>

    <br />

    <div class="row g-2">
        <h6>Alertas de estoque:</h6>
        <div class="form-group col-md-4">
            <div class="form-floating">
                <input type="email" class="form-control" name="receiver_mail" value="<?= $conf['receiver_mail'] ?? '' ?>">
                <label for="floatingInputGrid">Email que recebe os alertas:</label>
            </div>
        </div>

        <div class="form-group col-md-4">
            <div class="form-floating">
                <input type="email" class="form-control" name="receiver_mail_copy1" value="<?= $conf['receiver_mal_copy1'] ?? '' ?>">
                <label for="floatingInputGrid">Email Cópia para:</label>
            </div>
        </div>


        <div class="form-group col-md-4">
            <div class="form-floating">
                <input type="email" class="form-control" name="receiver_mail_copy2" value="<?= $conf['receiver_mail_copy2'] ?? '' ?>">
                <label for="floatingInputGrid">Email Cópia para:</label>
            </div>
        </div>

    </div>
    <br />
    <input type="submit" value="Atualizar" name="atualizar" class="btn btn-primary" />
</form>


<?php endif; ?>
<div class="clear"></div>