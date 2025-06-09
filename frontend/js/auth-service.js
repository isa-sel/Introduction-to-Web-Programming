// js/services/auth-service.js
const AuthService = {
    
    API_URL: 'http://localhost:8080/api',
    
    getToken() {
        return localStorage.getItem('auth_token');
    },
    
    setToken(token) {
        localStorage.setItem('auth_token', token);
    },
    
    removeToken() {
        localStorage.removeItem('auth_token');
    },
    
    getUser() {
        const userStr = localStorage.getItem('user');
        return userStr ? JSON.parse(userStr) : null;
    },
    
    setUser(user) {
        localStorage.setItem('user', JSON.stringify(user));
    },
    
    removeUser() {
        localStorage.removeItem('user');
    },
    
    isAuthenticated() {
        return !!this.getToken();
    },
    
    hasRole(role) {
        const user = this.getUser();
        return user && user.role === role;
    },
    
    isAdmin() {
        return this.hasRole('admin');
    },
    
    async register(userData) {
        try {
            const response = await fetch(`${this.API_URL}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(userData)
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Registration failed');
            }
            
            if (data.data && data.data.token) {
                this.setToken(data.data.token);
                this.setUser(data.data.user);
            }
            
            return data.data;
        } catch (error) {
            console.error('Registration error:', error);
            throw error;
        }
    },
    
    async login(email, password) {
        try {
            const response = await fetch(`${this.API_URL}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Login failed');
            }
            
            this.setToken(data.data.token);
            this.setUser(data.data.user);
            
            return data.data;
        } catch (error) {
            console.error('Login error:', error);
            throw error;
        }
    },
    
    logout() {
        this.removeToken();
        this.removeUser();
        window.location.href = '#login';
    }
};