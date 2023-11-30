<?php
include "db/database.php";

if(!isset($_SESSION['id'])){
    header('location:login.php');
}

// Variável para armazenar a ordem atual
$ordenacao = isset($_GET['ordenacao']) ? $_GET['ordenacao'] : 'ASC';


$sql = "SELECT * FROM items;";
$resultado = $conn->query($sql);
$items = $resultado->fetch_all(MYSQLI_ASSOC);

//Jogo sem rating
$sql_f = "SELECT *,i.id AS id_item FROM items i LEFT JOIN ratings r ON i.id = r.item_id AND r.user_id = {$_SESSION['id']} WHERE r.item_id IS NULL;";
$resultado = $conn->query($sql_f);
$items2 = $resultado->fetch_all(MYSQLI_ASSOC);

$jsonItems = json_encode($items);

//ranking
$sql_rankinglk = "SELECT items.id, items.name, COALESCE(SUM(CASE WHEN ratings.rating = 'like' THEN 1 ELSE 0 END), 0) AS likes, COALESCE(SUM(CASE WHEN ratings.rating = 'dislike' THEN 1 ELSE 0 END), 0) AS dislikes FROM items LEFT JOIN ratings ON items.id = ratings.item_id GROUP BY items.id, items.name ORDER BY dislikes $ordenacao, likes $ordenacao";
$resultado = $conn->query($sql_rankinglk);
$items4 = $resultado->fetch_all(MYSQLI_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://upload.wikimedia.org/wikipedia/commons/thumb/9/94/Video-Game-Controller-Icon-D-Edit.svg/1200px-Video-Game-Controller-Icon-D-Edit.svg.png" type="image/x-icon">
    <title>Rate The Game</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <header>
        <h1>Rate The Game</h1>
        <a href="logout.php"><input type="button" value="Logout" name="logout"></a>
    </header>
    
    <label for="ordenacao">Ordem:</label>
    <select id="ordenacao" name="ordenacao" onchange="atualizarOrdenacao()">
        <option value="ASC" <?php echo ($ordenacao == 'ASC') ? 'selected' : ''; ?>>Decrescente</option>
        <option value="DESC" <?php echo ($ordenacao == 'DESC') ? 'selected' : ''; ?>>Crescente</option>
        
    </select>

    <?php if($items2 == null): ?>
    <div class="box">
                <h3>Ranking de Jogos</h3>
                <?php foreach($items4 as $item) : ?>
                <img src="<?php echo $item[0]['image_url'];  ?>" alt="" width="250px">
                <h4><?php echo $item['name'] ?></h4>    
                <?php endforeach;
                endif; ?>
            </div>


        <?php if($_SESSION['tipo'] == "user") : ?>
        <div class="box">
        <img src="<?php echo $items2[0]['image_url']; ?>" alt="" width="250px">
        <p><?php echo $items2[0]['name']; ?></p>
            <!-- Botões de avaliação para usuários -->
            <a href="processar_rating.php?id=<?php echo $items2[0]['id_item']; ?>&&tipo=1"><input type="button" value="✅"></a><br>
            <a href="processar_rating.php?id=<?php echo $items2[0]['id_item']; ?>&&tipo=2"><input type="button" value="❌"></a><br>
    </div>

    <?php endif;?>

    <?php if($_SESSION['tipo'] == 'manager') :?>
        <a class="a" href=""><input type="button" value="Listar Jogos Cadastrados"></a>
        <a class="a" href="cadastrar_jogo.php"><input type="button" value="Cadastrar Jogo"></a>
        
    <?php endif;?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    
    function atualizarOrdenacao() {
    const ordenacao = document.getElementById("ordenacao").value;
            window.location.href = "index.php?ordenacao=" + ordenacao;
        }   
</script>
</body>
</html>
