function verify_roles() {
    const role = localStorage.getItem('role');
    if (role == 'admin') {
        window.location.href = 'views/admin/index.php';
    }else if (role == 'cliente') {
        window.location.href = 'views/cliente/index.php'; 
    }else if (role == 'entregador') {
        window.location.href = 'views/entregador/index.php';
    }
}