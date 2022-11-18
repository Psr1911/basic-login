const token = localStorage.getItem('token');

if (token) 
    window.location = '/profile.html';
else
    window.location = '/login.html';