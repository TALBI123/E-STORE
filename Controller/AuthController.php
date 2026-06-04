<?php

namespace Controller;

use App\Core\Controller;
use App\Repository\User;

class AuthController extends Controller
{
    private const BASE_URL = '/tps_php/projet'; // Ajustez selon votre configuration
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }
    public function showLogin(): void
    {
        // Si déjà connecté, redirige vers l'accueil
        if (!empty($_SESSION['user_id'])) {
            $this->redirect(self::BASE_URL . '/');
        }
        $this->render('auth/login', []);
    }
    public function login(): void
    {

        $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        // password_verify() compare le mot de passe en clair avec le hash stocké
        if ($user && password_verify($password, $user['password_hash']) && $user['is_active']) {
            // session_regenerate_id() : prévient l'attaque de fixation de session
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            echo "User authenticated: ID={$user['id']}, Role={$user['role']}<br>"; // Debugging line
            $this->redirect($user['role'] === 'admin' ? 'admin/dashboard' : '/');
        } else {
            // Message générique : ne pas indiquer si c'est l'email ou le mdp qui est faux
            $this->render('auth/login', [
                'error' => 'Email ou mot de passe incorrect.',
            ]);
        }
    }

    public function showRegister(): void
    {
        echo "Showing registration form...<br>"; // Debugging line
        $this->render('auth/register', []);
    }


    public function register(): void
    {


        $name     = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $errors = [];

        if (empty($name) || strlen($name) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        }
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if ($password !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if ($this->userModel->emailIsExist($email)) {
            $errors[] = "Cette adresse email est déjà utilisée.";
        }

        if (!empty($errors)) {
            $this->render('auth/register', [
                'errors' => $errors,
                'old'    => compact('name', 'email'),
            ]);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->userModel->create([
            'name'          => $name,
            'email'         => $email,
            'password_hash' => $hash,
            'role'          => 'client',
        ]);

        $this->redirect(self::BASE_URL . '/login?registered=1');
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        $this->redirect(self::BASE_URL . '/login');
    }
}
