const API_URL = 'http://localhost:8000/api/v1';
function setToken(token) { localStorage.setItem('token', token); }

async function login() {
    const loginValue = document.getElementById('email_username').value;
    const password = document.getElementById('login_password').value;
    const errorDiv = document.getElementById('login-error');

    // limpa mensagens antigas
    errorDiv.textContent = '';

    const res = await fetch(`${API_URL}/auth/login`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ login: loginValue, password })
    });

    const data = await res.json();

    if (data.token) {
        const role = data.user.role;
        setToken(data.token);
        localStorage.setItem('role', role);

        if (role == 'admin') {
            window.location.href = 'views/admin/index.php';
        } else if (role == 'cliente') {
            window.location.href = 'views/cliente/index.php';
        } else if (role == 'entregador') {
            window.location.href = 'views/entregador/index.php';
        }

    } else {
        // mostra mensagem de erro no index.php
        errorDiv.textContent = data.message || "Erro ao fazer login!";
    }
}

async function register() {
    const name = document.getElementById('name').value;
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const telefone = document.getElementById('telefone').value;
    const password = document.getElementById('reg-password').value;
    const password_confirmation = document.getElementById('password_confirmation').value;
    const role = document.getElementById('role').value;
    const errorDiv = document.getElementById('register-error');

    // limpa mensagens antigas
    errorDiv.textContent = '';

    const res = await fetch(`${API_URL}/auth/register`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ name, username, email, telefone, password, password_confirmation, role })
    });

    const data = await res.json();

    if (res.ok) {
        // se registro for sucesso, loga o usuário
        await loginAfterRegister(username, password);
    } else {
        // se o backend retornar vários erros (ex: validação do Laravel)
        if (data.errors) {
            errorDiv.innerHTML = Object.values(data.errors).flat().join('<br>');
        } else {
            errorDiv.textContent = data.message || "Erro ao registrar!";
        }
    }
}

async function loginAfterRegister(loginValue, password) {
    const res = await fetch(`${API_URL}/auth/login`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ login: loginValue, password })
    });

    const data = await res.json();
    const errorDiv = document.getElementById('login-error');

    // limpa mensagens antigas
    if (errorDiv) errorDiv.textContent = '';

    if (data.token) {
        const role = data.user.role;
        setToken(data.token);
        localStorage.setItem('role', role);

        if (role == 'admin') {
            window.location.href = 'views/admin/index.php';
        } else if (role == 'cliente') {
            window.location.href = 'views/cliente/index.php';
        } else if (role == 'entregador') {
            window.location.href = 'views/entregador/index.php';
        }
    } else {
        if (errorDiv) {
            errorDiv.textContent = data.message || "Erro ao logar após registro!";
        }
    }
}
