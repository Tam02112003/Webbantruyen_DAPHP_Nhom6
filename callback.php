<?php
require 'vendor/autoload.php';
require 'app/config/oauth_config.php';
session_start();

// Khởi tạo Google Client
$client = new Google_Client();
$client->setClientId($oauth_config['client_id']); 
$client->setClientSecret($oauth_config['client_secret']);
$client->setRedirectUri($oauth_config['redirect_uri']);
$client->addScope("email");
$client->addScope("profile");

// Kết nối đến cơ sở dữ liệu
require_once 'app/config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Bao gồm mô hình người dùng
require_once 'app/models/UserModel.php';
$userModel = new UserModel($conn);

// Hàm hỗ trợ tạo username từ email
function generateUsernameFromEmail($email, $addRandom = false) {
    $baseUsername = strtok($email, '@');
    $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '', $baseUsername);
    
    if ($addRandom) {
        $baseUsername .= '_' . bin2hex(random_bytes(2));
    }
    
    return $baseUsername;
}

// Xử lý mã xác thực
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (array_key_exists('error', $token)) {
        exit('Có lỗi khi lấy token: ' . $token['error']);
    }

    if (isset($token['access_token'])) {
        $client->setAccessToken($token['access_token']);
    } else {
        exit('Không thể lấy access token.');
    }

    $oauth2 = new Google\Service\Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    $email = $userInfo->email;
    $name = $userInfo->name ?? '';
    $username = generateUsernameFromEmail($email);
    
    if ($userModel->userExists($email)) {
        $userModel->updateUser($email);
        // Lấy thông tin user từ database để có role chính xác
        $user = $userModel->getUserByEmail($email);
    } else {
        $password = password_hash(uniqid(), PASSWORD_BCRYPT);
        $role = 'user'; // Mặc định là user khi tạo mới
        
        if (!$userModel->register($username, $email, $password)) {
            $username = generateUsernameFromEmail($email, true);
            $userModel->register($username, $email, $password);
        }
        $user = $userModel->getUserByEmail($email);
    }

    // Sử dụng role từ database thay vì hardcode
    $_SESSION['user'] = [
        'user_id' => $user['user_id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role' => $user['role'], // Lấy role từ database
        'google_picture' => $userInfo->picture ?? ''
    ];

    $_SESSION['success'] = "Đăng nhập thành công.";
    header('Location: index.php?controller=home&action=index');
    exit;
} else {
    $_SESSION['error'] = "Không có mã xác thực. Vui lòng thử lại.";
    header('Location: index.php?controller=auth&action=showLoginForm');
    exit;
}
?>