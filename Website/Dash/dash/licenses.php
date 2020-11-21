<?php
require '../functions/includes.php';
require '../functions/session.php';

session_regenerate_id();

if(!session_valid() or !isset($_SESSION['loader'])) {
    header('Location: ' . process_link('index.php', true));
    die('invalid login or loader not set');
}

$loader = $_SESSION['loader'];

$username = $_SESSION['username'];

if(isset($_POST['delete'])){
    licenses\delete($connection, $loader, $_POST['delete']);
    header("Refresh:0");
}

if (isset($_POST['amount']) && isset($_POST['duration'])) {
    $out = '';

    $generated_keys = licenses\generate($connection, $loader, $_POST['amount'], $_POST['duration'], !empty($_POST['usergroup']) ? $_POST['usergroup'] : null);

    foreach($generated_keys as $key){
        $out .= $key . '<br>';
    }

    die($out);
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FireFrame</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <style>
        hr {
            margin-top: 1rem;
            margin-bottom: 1rem;
            border: 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
    </style>

</head>
<body style="
background: url(../background.jpg) no-repeat center center fixed;
background-size: auto;
-webkit-background-size: cover;
-moz-background-size: cover;
-o-background-size: cover;">

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <?php
    echo '<a href="' . process_link("",false) . '" class="navbar-brand">FireFrame - Cheat Loaders</a>' ;
    ?>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link" href="index.php">Loader</a>
            <a class="nav-item nav-link" href="users.php">Users</a>
            <a class="nav-item nav-link active" href="licenses.php">Licenses <span class="sr-only">(current)</span></a>
            <a class="nav-item nav-link" href="modules.php">Modules</a>
            <a class="nav-item nav-link" href="">|</a>
            <a class="nav-item nav-link" href="<?php if (is_onion()) echo 'https://firefra.me'; else echo 'http://etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion/'; ?>"><?php if (is_onion()) echo 'Clearnet'; else echo 'Tor'; ?></a>
            <a class="nav-item nav-link" href="<?php echo process_link("tos.php",false); ?>">ToS & PP</a>
            <a class="nav-item nav-link" href="https://discord.gg/xPtevhPHQp"><img src="../Discord-Logo-White.png" width="30" height="30"></a>
            <a class="nav-item nav-link" href="https://t.me/fire_frame"><img src="../Telegram-Logo.png" width="28" height="28"></a>
        </div>
    </div>
    <form class="form-inline p-0 m-0" action="account.php">
      <span class="navbar-text text-light text-right mr-3">
          User : <?php echo $username;?>
      </span>
        <button class="btn btn-outline-light my-2 my-sm-0 mr-3" type="submit">Account</button>
        <button class="btn btn-outline-light my-2 my-sm-0" name="logout" id="logout">Logout</button>

        <button class="navbar-toggler ml-3 " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </form>
</nav>

<div class="container h-100 d-flex justify-content-center pt-5 mb-5">
    <div class="col-lg-10">
        <div class="card rounded-0 text-white bg-dark">
            <div class="card-header">
                Licenses
            </div>
            <div class="row p-3">
                <div class="col-lg mr-2">
                    <table class="table table-dark w-100">
                        <thead>
                        <tr>
                            <th scope="col">Code</th>
                            <th scope="col">Group</th>
                            <th scope="col">Duration</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <form method="post">
                        <?php
                        $licenses = licenses\fetch_all($connection, $loader);
                        foreach($licenses as $license) { ?>
                            <tr>
                                <th scope="row"><?= $license['code'] ?></th>
                                <td><?= htmlentities($license['usergroup']) ?></td>
                                <td><?= $license['duration'] ?></td>
                                <td><button class="btn btn-danger" value="<?= $license['code'] ?>"></button></td>
                            </tr>
                          <?php } ?>
                        </form>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container h-100 d-flex justify-content-center pt-5 mb-5">
    <div class="col-lg-10">
        <div class="card rounded-0 text-white bg-dark">
            <div class="card-header">
                Generate Licenses
            </div>
            <div class="row p-3">
                <div class="col-lg mr-2">
                    <form method="POST" class="p-3">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" id="amount"  name="amount" min="1" max="100" aria-describedby="emailHelp" placeholder="Amount" required>
                        </div>
                        <div class="form-group">
                            <label>Duration (days)</label>
                            <input type="number" class="form-control" id="duration" name="duration" min="1" max="1825" placeholder="Duration (days)" required>
                        </div>
                        <div class="form-group">
                            <label>Usergroup</label>
                            <input type="text" class="form-control" id="usergroup" name="usergroup" placeholder="Usergroup (Default,VIP)">
                        </div>
                        <small class="text-secondary">If you leave the Usergroup field empty, Default group will be automatically applied.<br>
                            You can also assign multiple groups to a license by separating the group names with a comma (,).</small>
                        <center>
                            <button type="submit" class="btn btn-light border w-100 mt-2 mb-2">Generate</button>
                        </center>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="page-footer font-small dark text-light bg-dark fixed-bottom">
    <div class="footer-copyright text-center py-3">© <?php echo date("Y");?> FireFrame</div>
</footer>
</body>
</html>