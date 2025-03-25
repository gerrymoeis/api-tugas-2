// DOM Elements
const loginLink = document.getElementById('loginLink');
const registerLink = document.getElementById('registerLink');
const logoutLink = document.getElementById('logoutLink');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const contactsSection = document.getElementById('contactsSection');
const contactsList = document.getElementById('contactsList');

// Particle Effect
function createParticles() {
    const particles = document.getElementById('particles');
    for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = `${Math.random() * 100}%`;
        particle.style.top = `${Math.random() * 100}%`;
        particle.style.animationDuration = `${Math.random() * 3 + 2}s`;
        particle.style.animationDelay = `${Math.random() * 2}s`;
        particles.appendChild(particle);
    }
}

// API Base URL
const API_URL = '/api';

// Auth Token Management
let authToken = localStorage.getItem('authToken');

function setAuthToken(token) {
    authToken = token;
    localStorage.setItem('authToken', token);
    updateUIState();
}

function clearAuthToken() {
    authToken = null;
    localStorage.removeItem('authToken');
    updateUIState();
}

// UI State Management
function updateUIState() {
    if (authToken) {
        loginLink.classList.add('hidden');
        registerLink.classList.add('hidden');
        logoutLink.classList.remove('hidden');
        loginForm.classList.add('hidden');
        registerForm.classList.add('hidden');
        contactsSection.classList.remove('hidden');
        loadContacts();
    } else {
        loginLink.classList.remove('hidden');
        registerLink.classList.remove('hidden');
        logoutLink.classList.add('hidden');
        contactsSection.classList.add('hidden');
    }
}

// API Calls
async function apiCall(endpoint, method = 'GET', data = null) {
    const headers = {
        'Content-Type': 'application/json'
    };
    
    if (authToken) {
        headers['Authorization'] = `Bearer ${authToken}`;
    }

    try {
        const response = await fetch(`${API_URL}${endpoint}`, {
            method,
            headers,
            body: data ? JSON.stringify(data) : null
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.errors?.message || 'An error occurred');
        }

        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Auth Functions
async function login(username, password) {
    try {
        const result = await apiCall('/login', 'POST', { username, password });
        setAuthToken(result.data.token);
    } catch (error) {
        alert('Login failed: ' + error.message);
    }
}

async function register(username, password, name) {
    try {
        await apiCall('/register', 'POST', { username, password, name });
        await login(username, password);
    } catch (error) {
        alert('Registration failed: ' + error.message);
    }
}

async function logout() {
    try {
        await apiCall('/logout', 'POST');
    } catch (error) {
        console.error('Logout error:', error);
    } finally {
        clearAuthToken();
    }
}

// Contact Management
async function loadContacts() {
    try {
        const result = await apiCall('/contacts');
        displayContacts(result.data);
    } catch (error) {
        console.error('Error loading contacts:', error);
    }
}

async function addContact(contactData) {
    try {
        await apiCall('/contacts', 'POST', contactData);
        loadContacts();
    } catch (error) {
        alert('Error adding contact: ' + error.message);
    }
}

async function deleteContact(id) {
    try {
        await apiCall(`/contacts/${id}`, 'DELETE');
        loadContacts();
    } catch (error) {
        alert('Error deleting contact: ' + error.message);
    }
}

// UI Rendering
function displayContacts(contacts) {
    contactsList.innerHTML = contacts.map(contact => `
        <div class="card animate-float">
            <h3 class="card-title">${contact.first_name} ${contact.last_name || ''}</h3>
            ${contact.email ? `<p>📧 ${contact.email}</p>` : ''}
            ${contact.phone ? `<p>📱 ${contact.phone}</p>` : ''}
            <button class="btn btn-primary" onclick="deleteContact(${contact.id})">Delete</button>
        </div>
    `).join('');
}

// Event Listeners
loginLink.addEventListener('click', (e) => {
    e.preventDefault();
    loginForm.classList.remove('hidden');
    registerForm.classList.add('hidden');
});

registerLink.addEventListener('click', (e) => {
    e.preventDefault();
    registerForm.classList.remove('hidden');
    loginForm.classList.add('hidden');
});

logoutLink.addEventListener('click', (e) => {
    e.preventDefault();
    logout();
});

document.getElementById('loginFormElement').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    await login(formData.get('username'), formData.get('password'));
});

document.getElementById('registerFormElement').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    await register(
        formData.get('username'),
        formData.get('password'),
        formData.get('name')
    );
});

document.getElementById('addContactForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const contactData = {
        first_name: formData.get('first_name'),
        last_name: formData.get('last_name'),
        email: formData.get('email'),
        phone: formData.get('phone')
    };
    await addContact(contactData);
    e.target.reset();
});

// Initialize
createParticles();
updateUIState();
