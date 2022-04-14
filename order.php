<?php

$pdo = new PDO('mysql:host=localhost;dbname=burger', 'root', '');

$email = $_GET['email'];
$address = 'улица ' . $_GET['street'] . ', дом ' . $_GET['home'] . ', корпус ' . $_GET['part'] . ', квартира ' . $_GET['appt'] . ', этаж ' . $_GET['floor'];

$query = $pdo->prepare("SELECT * FROM customers WHERE email = :email");
$query->execute(['email' => $email]);
$customer1 = $query->fetchAll(PDO::FETCH_ASSOC);

if ($customer1[0]['email']) {
    foreach ($customer1 as $user) {
        $id = $user['id'];
        $sql = $pdo->prepare("INSERT INTO orders (`user_id`, `date`, `address`) VALUES (:user_id, CURDATE(), :address);UPDATE customers SET all_orders = all_orders + 1 WHERE id = :id");
        $sql->bindValue(':user_id', $user['id']);
        $sql->bindValue(':address', $address);
        $sql->bindValue(':id', $user['id']);
        $sql->execute();

        $query = $pdo->prepare("SELECT * FROM orders WHERE user_id = :id");
        $query->execute(['id' => $id]);
        $customer = $query->fetchAll(PDO::FETCH_ASSOC);
        $lastOrder = array_key_last($customer);
        $orderId = $customer[$lastOrder]['id'];
        echo 'Спасибо! Ваш заказ будет отправлен по адресу: ' . $address . '<br>';
        echo 'Номер вашего заказа: ' . $orderId . '<br>';
        echo 'Это ваш ' . ($customer1[0]['all_orders'] + 1) . ' заказ';
    }
} else {
    $sql = $pdo->prepare("INSERT INTO customers (`email`) VALUES (:email)");
    $sql->bindValue(':email', $email);
    $sql->execute();

    $sql = $pdo->prepare("SELECT id, email FROM customers WHERE email = :email");
    $sql->bindValue(':email', $email);
    $sql->execute();
    $newCustomerId = $sql->fetch();

    $sql = $pdo->prepare("INSERT INTO orders (`user_id`, `date`, `address`) VALUES (:user_id, CURDATE(), :address)");
    $sql->bindValue(':user_id', $newCustomerId['id']);
    $sql->bindValue(':address', $address);
    $sql->execute();

    $query = $pdo->prepare("SELECT * FROM orders WHERE id = last_insert_id()");
    $query->execute();
    $customer = $query->fetch();
    $orderId = $customer['id'];
    echo 'Спасибо! Ваш заказ будет отправлен по адресу: ' . $address . '<br>';
    echo 'Номер вашего заказа: ' . $orderId . '<br>';
    echo 'Это ваш 1 заказ';
}
